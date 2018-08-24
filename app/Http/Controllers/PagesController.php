<?php
/**
 * Created by PhpStorm.
 * User: potato
 * Date: 14/08/18
 * Time: 6:02 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function root()
    {
        return view('pages.root');
    }//

    public function emailVerifyNotice()
    {
        return view('pages.email_verify_notice');
    }
}
