<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Exceptions\InternalException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HandleRefundRequest;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
//use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use Illuminate\Validation\UnauthorizedException;


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
//    public function edit($id, Content $content)
//    {
//        return $content
//            ->header('Edit')
//            ->description('description')
//            ->body($this->form()->edit($id));
//    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
//    public function create(Content $content)
//    {
//        return $content
//            ->header('Create')
//            ->description('description')
//            ->body($this->form());
//    }

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

    public function ship(Order $order, Request $request)
    {
        //check whether this order has been paid
        if (!$order->paid_at) {
            throw new InvalidRequestException('Order has not been paid!');
        }

        //check whether order has been shipped
        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('Order has been shipped!');
        }

        //validate() return value which passed verification
        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no'      => ['required'],
        ], [], [
            'express_company'   => ['Delivery Company'],
            'express_no'        => ['Delivery No.'],
        ]);

        //update ship_status
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,

            //$casts in Order Model defines ship_data is an array
            'ship_data'   => $data,
        ]);

        //return last page
        return redirect()->back();
    }

    public function handleRefund(Order $order, HandleRefundRequest $request)
    {
        //check order status
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new InvalidRequestException('Order Status Error!');
        }

        //check if agree refund
        if ($request->input('agree')) {
            //call refund function
            $this->_refundOrder($order);
        } else {
            //insert reject reason into extra field
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $request->input('reason');

            //set refund status
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra'         => $extra,
            ]);
        }

        return $order;
    }

    protected function _refundOrder(Order $order)
    {
        //check payment method
        switch ($order->payment_method) {
            case 'wechat':
                //generate refundNo
                $refundNo = Order::getAvailableRefundNo();
                app('wechat_pay')->refund([
                    'out_trade_no'  => $order->no, // OrderNo
                    'total_fee'     => $order->total_amount * 100, //Order price ￥分
                    'refund_fee'    => $order->total_amount * 100, // refund amount，￥分
                    'out_refund_no' => $refundNo, // refundNo
                    // 微信支付的退款结果并不是实时返回的，而是通过退款回调来通知，因此这里需要配上退款回调接口地址
                    // 由于是开发环境，需要配成 requestbin 地址，在实际中填写微信账号的退款地址
                    'notify_url'    => 'http://requestbin.fullcontact.com/******'

                    // 实际代码
                  //'notify_url'    => route('payment.wechat.refund_notify'),
                ]);

                //update refund status
                $order->update([
                    'refund_no' => $refundNo,
                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
                ]);
                break;

            case 'alipay':
                //generate refundNo
                $refundNo = Order::getAvailableRefundNo();

                //call refund function in alipay instance
                $ret = app('alipay')->refund([
                    'out_trade_no'   => $order->no, //Order No
                    'refund_amount'  => $order->total_amount,  //refund amount ￥yuan
                    'out_request_no' => $refundNo,//refund order No
                ]);

                //if sub_code string exists in return value means refund failed from alipay server
                if ($ret->sub_code) {
                    //save failed info
                    $extra = $order->extra;
                    $extra['refund_failed_code'] = $ret->sub_code;

                    //mark refund status as failed
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra,
                    ]);
                } else {
                    //mark refund status as success and save refundNo
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            default:
                //in case unexpected
                throw new InternalException('未知订单支付方式：'.$order->payment_method);
                break;
        }
    }
}
