<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CouponCodesController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Coupon List')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
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
            ->header('Edit Coupon')
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
            ->header('Add Coupon')
//            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CouponCode);

        //DESC order according create time
        $grid->model()->orderBy('created_at', 'desc');
        $grid->id('ID')->sortable();
        $grid->name('Name');
        $grid->code('CouponNo');
        $grid->description('Description');
        $grid->column('usage', 'Usage')->display(function ($value) {
            return "{$this->used} / {$this->total}";
        });
        $grid->enabled('Enabled')->display(function ($value) {
            return $value ? 'Yes' : 'No';
        });
        $grid->created_at('Created');
//        $grid->type('Type')->display(function($value) {
//            return CouponCode::$typeMap[$value];
//        });
//
//        //display different coupon type
//        $grid->value('Discount')->display(function($value) {
//            return $this->type === CouponCode::TYPE_FIXED ? '￥'.$value : $value.'%';
//        });
//        $grid->min_amount('Amount(Min)');
//        $grid->total('Total');
//        $grid->used('Used');
//        $grid->enabled('Enabled')->display(function($value) {
//            return $value ? 'Yes' : 'No';
//        });
//        $grid->created_at('Created');
//
//        $grid->actions(function ($actions) {
//            $actions->disableView();
//        });

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
        $show = new Show(CouponCode::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->code('Code');
        $show->type('Type');
        $show->value('Value');
        $show->total('Total');
        $show->used('Used');
        $show->min_amount('Min amount');
        $show->not_before('Not before');
        $show->not_after('Not after');
        $show->enabled('Enabled');
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
        $form = new Form(new CouponCode);

        $form->display('id', 'ID');
        $form->text('name', 'Name')->rules('required');
        $form->text('code', 'CouponCode')->rules(function($form) {
            //if $form->model()->id !== null, means it is edit operation
            if ($id = $form->model()->id) {
                return 'nullable|unique:coupon_codes,code,'.$id.',id';
            } else {
                return 'nullable|unique:coupon_codes';
            }
        });
        $form->radio('type', 'Type')->options(CouponCode::$typeMap)->rules('required');
        $form->text('value', 'Discount')->rules(function ($form) {
            if ($form->type === CouponCode::TYPE_PERCENT) {
                //if %, range from 1 to 99
                return 'required|numeric|between:1,99';
            } else {
                //if $, over 0.01
                return 'required|numeric|min:0.01';
            }
        });
        $form->text('total', 'Total')->rules('required|numeric|min:0');
//        $form->number('used', 'Used');
        $form->text('min_amount', 'Min amount')->rules('required|numeric|min:0');
        $form->datetime('not_before', 'Not before');
        $form->datetime('not_after', 'Not after');
//        $form->datetime('not_before', 'Not before')->default(date('Y-m-d H:i:s'));
//        $form->datetime('not_after', 'Not after')->default(date('Y-m-d H:i:s'));
        $form->radio('enabled', 'Enabled')->options(['1' => 'Yes', '0' => 'No']);

        $form->saving(function (Form $form) {
            if (!$form->code) {
                $form->code = CouponCode::findAvailableCode();
            }
        });

        return $form;
    }
}
