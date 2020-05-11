<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
//use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\ModelForm;

class UsersController extends Controller
{
    //use HasResourceActions;
    use ModelForm;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
//    public function index()
//    {
//        return Admin::content(function (Content $content) {
//           $content->header('header');
//           $content->description('description');
//           $content->body($this->grid());
//        });
//    }

//    public function index(Content $content)
//    {
//        return $content
//            ->header('Index')
//            ->description('description')
//            ->body($this->grid());
//    }

    public function index(Content $content)
    {
        return $content
            ->header('User Index')
            ->description('description')
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
//    public function edit($id)
//    {
//        return Admin::content(function (Content $content) use ($id) {
//           $content->header('header');
//           $content->description('description');
//           $content->body($this->form()->edit($id));
//        });
//    }
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
//    public function create()
//    {
//        return Admin::content(function (Content $content) {
//           $content->header('header');
//           $content->description('description');
//           $content->body($this->form());
//        });
//    }

    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /*
     * Make a delete option.
     */


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
//    protected function grid()
//    {
//        return Admin::grid(User::class, function (Grid $grid) {
//
//            $grid->id('ID')->sortable();
//
//            $grid->created_at();
//            $grid->updated_at();
//        });
//    }

//    protected function grid()
//    {
//        $grid = new Grid(new User);
//
//        $grid->id('Id');
//        $grid->name('Name');
//        $grid->email('Email');
//        $grid->password('Password');
//        $grid->remember_token('Remember token');
//        $grid->email_verified('Email verified');
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');
//
//        return $grid;
//    }

    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->id('Id')->sortable();
        $grid->name('Name')->sortable();
        $grid->email('Email');
        $grid->email_verified('Email verified')
            ->display(function ($value) {
                return $value? 'Yes' : 'No';
            });
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show = new Show(User::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->email('Email');
        $show->password('Password');
        $show->remember_token('Remember token');
        $show->email_verified('Email verified');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
//    protected function form()
//    {
//        return Admin::form(User::class, function (Form $form) {
//
//            $form->display('id', 'ID');
//
//            $form->display('created_at', 'Created At');
//            $form->display('updated_at', 'Updated At');
//        });
//    }

    protected function form()
    {
        $form = new Form(new User);

        $form->text('name', 'Name');
        $form->email('email', 'Email');
        $form->password('password', 'Password');
        $form->text('remember_token', 'Remember token');
        $form->switch('email_verified', 'Email verified');

        return $form;
    }
}
