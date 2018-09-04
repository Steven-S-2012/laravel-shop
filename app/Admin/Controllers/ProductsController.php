<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductsController extends Controller
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
            ->header('Product List')
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
        $grid = new Grid(new Product);

        $grid->id('Id')->sortable();
        $grid->title('Title')->sortable;
        $grid->size('Size');
        $grid->price('Price');
        $grid->price_m_au('Price m au');
//        $grid->price_vip_au('Price vip au');
//        $grid->price_vvip_au('Price vvip au');
        $grid->price_rmb('Price rmb');
//        $grid->price_vip_rmb('Price vip rmb');
//        $grid->price_20_rmb('Price 20 rmb');
//        $grid->price_vvip_rmb('Price vvip rmb');
        $grid->title_en('Title en');
        $grid->weight('Weight');
        $grid->image('Image');
        $grid->category('Category');
        $grid->barcode('Barcode');
//        $grid->gst('Gst');
//        $grid->cost('Cost');
//        $grid->real_cost('Real cost');
        $grid->barcode_family('Barcode family');
        $grid->description('Description');
        $grid->stock('Stock');
        $grid->specialnote('Specialnote');
        $grid->on_sale('On sale')->dispaly(function($value) { return $value ? 'Yes' : 'No'; });
        $grid->rating('Rating');
        $grid->sold_count('Sold count');
        $grid->review_count('Review count');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
               $batch->disableDelete();
            });
        });

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
        $show = new Show(Product::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->size('Size');
        $show->price('Price');
        $show->price_m_au('Price m au');
        $show->price_vip_au('Price vip au');
        $show->price_vvip_au('Price vvip au');
        $show->price_rmb('Price rmb');
        $show->price_vip_rmb('Price vip rmb');
        $show->price_20_rmb('Price 20 rmb');
        $show->price_vvip_rmb('Price vvip rmb');
        $show->title_en('Title en');
        $show->weight('Weight');
        $show->image('Image');
        $show->category('Category');
        $show->barcode('Barcode');
        $show->gst('Gst');
        $show->cost('Cost');
        $show->real_cost('Real cost');
        $show->barcode_family('Barcode family');
        $show->description('Description');
        $show->stock('Stock');
        $show->specialnote('Specialnote');
        $show->on_sale('On sale');
        $show->rating('Rating');
        $show->sold_count('Sold count');
        $show->review_count('Review count');
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
        $form = new Form(new Product);

        $form->text('title', 'Title');
        $form->number('size', 'Size');
        $form->decimal('price', 'Price');
        $form->decimal('price_m_au', 'Price m au');
        $form->decimal('price_vip_au', 'Price vip au');
        $form->decimal('price_vvip_au', 'Price vvip au');
        $form->decimal('price_rmb', 'Price rmb');
        $form->decimal('price_vip_rmb', 'Price vip rmb');
        $form->decimal('price_20_rmb', 'Price 20 rmb');
        $form->decimal('price_vvip_rmb', 'Price vvip rmb');
        $form->text('title_en', 'Title en');
        $form->number('weight', 'Weight');
        $form->image('image', 'Image');
        $form->text('category', 'Category');
        $form->number('barcode', 'Barcode');
        $form->decimal('gst', 'Gst');
        $form->decimal('cost', 'Cost');
        $form->decimal('real_cost', 'Real cost');
        $form->number('barcode_family', 'Barcode family');
        $form->textarea('description', 'Description');
        $form->number('stock', 'Stock');
        $form->textarea('specialnote', 'Specialnote');
        $form->switch('on_sale', 'On sale')->default(1);
        $form->decimal('rating', 'Rating')->default(5.00);
        $form->number('sold_count', 'Sold count');
        $form->number('review_count', 'Review count');

        return $form;
    }
}
