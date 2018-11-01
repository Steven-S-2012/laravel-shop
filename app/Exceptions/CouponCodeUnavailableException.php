<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class CouponCodeUnavailableException extends Exception
{
    public function __construct($message, int $code = 403)
    {
        parent::__construct($message, $code);
    }

    //if triggered, call render() to output
    public function render(Request $request)
    {
        //if user passed Api requirement, return JSON error message
        if ($request->expectsJson()) {
            return response()->json(['msg' => $this->message], $this->code);
        }

        //otherwise return last page with error msg
        return redirect()->back()->withErrors(['coupon_code' => $this->message]);
    }
}
