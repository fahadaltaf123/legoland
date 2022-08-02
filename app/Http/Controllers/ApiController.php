<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:5|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
        }


        $request->request->set('token',$token);
        $request->headers->set('Authorization Bearer ',$token);
        $request->headers->set('Authorization','Bearer '.$token);
//        $token = "your encrypted token goes here";

        $decoded = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))),true);

        $user = User::find($decoded['sub']);

        if($user and $user->status != 1){
            return response()->json([
                'success' => false,
                'message' => 'User not activated',
            ], 500);
        }

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated, do logout
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);
        if($user and $user->status != 1){
            return response()->json([
                'success' => false,
                'message' => 'User not activated',
            ], 500);
        }

        return response()->json(['user' => $user]);
    }

    public function getAllAssignedTickets(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);
        if($user and $user->status != 1){
            return response()->json([
                'success' => false,
                'message' => 'User not activated',
            ], 500);
        }

        $tickets = Tickets::where('customer_id',$user->id)->get();
        return response()->json(['tickets' => $tickets]);
    }

    public function authenticateByOrderPhone(Request $request)
    {
        $credentials = $request->only('ticket_id', 'phone_no');

        //valid credential
        $validator = Validator::make($credentials, [
            'ticket_id' => 'required',
            'phone_no' => 'required|string|min:5|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }


        $user = User::where('phone',$request->get('phone_no'))->first();

        if($user and $user->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'User not activated',
            ], 500);
        }

        $ticket = Tickets::find($request->get('ticket_id'));

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid User Phone',
            ], 500);
        }

        if(!$ticket or ($user and $ticket->customer_id != $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Order Id',
            ], 500);
        }


        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function getAllAssignedTicketsByOrderIdAndPhone(Request $request)
    {
        $this->validate($request, [
            'ticket_id' => 'required',
            'user_id' => 'required'
        ]);

        $user = User::find($request->get('user_id'));

        if($user and $user->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'User not activated',
            ], 500);
        }

        $ticket = Tickets::find($request->get('ticket_id'));

        if(!$ticket or $ticket->customer_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Order Id',
            ], 500);
        }

//        $tickets = Tickets::where('customer_id',$user->id)->get();
        return response()->json(['ticket' => [$ticket]]);
    }
}