<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
//use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Show;


class OrdersController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
//    public function index()
//    {
//        return Admin::content(function (Content $content) {

    public function index(Content $content)
    {
        return $content
            ->header('Order Index')
            //->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show(Content $content, Order $order)
    {
        return $content
            ->header('Order Detail')
            //->description('description')
            // use views as body's paras
            ->body(view('admin.orders.show', ['order' => $order]));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        //return Admin::grid(Order::class, function (Grid $grid) {
        $grid = new Grid(new Order);
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

        $grid->id('Id');
        $grid->no('No.');
        $grid->column('user.name', 'Buyer');
        $grid->total_amount('Total')->sortable();
        $grid->paid_at('Paid At')->sortable();
        $grid->ship_status('Shipment')->display(function($value) {
            return Order::$shipStatusMap[$value];
        });
        $grid->refund_status('Refund')->display(function($value) {
            return Order::$refundStatusMap[$value];
        });

        //disable create button
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            //disable delete & edit button
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            //disable multiple delete
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

//        $grid->address('Address');
//        $grid->total_amount('Total amount')->sortable();
//        $grid->remark('Remark');
//        $grid->paid_at('Paid at')->sortable();
//        $grid->payment_method('Payment method');
//        $grid->payment_no('Payment no');
//        $grid->refund_status('Refund status');
//        $grid->refund_no('Refund no');
//        $grid->closed('Closed');
//        $grid->reviewed('Reviewed');
//        $grid->ship_status('Ship status');
//        $grid->ship_data('Ship data');
//        $grid->extra('Extra');
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->id('Id');
        $show->no('No');
        $show->user_id('User id');
        $show->address('Address');
        $show->total_amount('Total amount');
        $show->remark('Remark');
        $show->paid_at('Paid at');
        $show->payment_method('Payment method');
        $show->payment_no('Payment no');
        $show->refund_status('Refund status');
        $show->refund_no('Refund no');
        $show->closed('Closed');
        $show->reviewed('Reviewed');
        $show->ship_status('Ship status');
        $show->ship_data('Ship data');
        $show->extra('Extra');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', 'No');
        $form->number('user_id', 'User id');
        $form->textarea('address', 'Address');
        $form->decimal('total_amount', 'Total amount');
        $form->textarea('remark', 'Remark');
        $form->datetime('paid_at', 'Paid at')->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', 'Payment method');
        $form->text('payment_no', 'Payment no');
        $form->text('refund_status', 'Refund status')->default('pending');
        $form->text('refund_no', 'Refund no');
        $form->switch('closed', 'Closed');
        $form->switch('reviewed', 'Reviewed');
        $form->text('ship_status', 'Ship status')->default('pending');
        $form->textarea('ship_data', 'Ship data');
        $form->textarea('extra', 'Extra');

        return $form;
    }
}
