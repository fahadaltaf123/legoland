<?php

namespace App\Admin\Controllers;

use App\Models\Tickets;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TicketController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tickets';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Tickets());

        $grid->column('id', __('Id'));
        $grid->column('is_universal', __('Is universal'));
        $grid->column('no_of_adults', __('No of adults'));
        $grid->column('no_of_kids', __('No of kids'));
        $grid->column('status', __('Status'));
        $grid->column('customer_id', __('Customer id'));
        $grid->column('visit_date', __('Visit date'));
        $grid->column('visit_time', __('Visit time'));
        $grid->column('total', __('Total'));
        $grid->column('discount', __('Discount'));
        $grid->column('after_discount', __('After discount'));
        $grid->column('balance_due', __('Balance due'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Tickets::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('is_universal', __('Is universal'));
        $show->field('no_of_adults', __('No of adults'));
        $show->field('no_of_kids', __('No of kids'));
        $show->field('status', __('Status'));
        $show->field('customer_id', __('Customer id'));
        $show->field('visit_date', __('Visit date'));
        $show->field('visit_time', __('Visit time'));
        $show->field('total', __('Total'));
        $show->field('discount', __('Discount'));
        $show->field('after_discount', __('After discount'));
        $show->field('balance_due', __('Balance due'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Tickets());

        $users = User::where('status',1)->get();
        $userArr = [];
        foreach ($users as $user) {
            $userArr[$user->id] = $user->name;
        }

        $form->select('customer_id', __('Customer id'))->options($userArr);

        $form->date('visit_date', __('Visit date'))->default(date('Y-m-d H:i:s'));
        $form->time('visit_time', __('Visit time'));

        $form->number('no_of_adults', __('No of adults'));
        $form->number('no_of_kids', __('No of kids'));

        $form->switch('is_universal', __('Is universal'));

        $form->decimal('total', __('Total'));
        $form->decimal('discount', __('Discount'));
        $form->decimal('after_discount', __('After discount'));

        $form->decimal('deposit_amt', __('Deposit Amount'));
        $form->select('deposit_type', __('Deposit Type'))->options([
            'cash'=>'Cash','card' => 'Credit Card'
        ]);

        $form->decimal('balance_due', __('Balance due'));
        $form->switch('status', __('Status'));
        return $form;
    }
}
