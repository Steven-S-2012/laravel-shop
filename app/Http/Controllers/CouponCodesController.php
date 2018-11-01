<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CouponCode;
use Carbon\Carbon;
use App\Exceptions\CouponCodeUnavailableException;

class CouponCodesController extends Controller
{
    public function show($code)
    {
        //check if coupon exists
        if (!$record = CouponCode::where('code', $code)->first()) {
            throw new CouponCodeUnavailableException('Coupon does not exist!');
        }

        //if coupon does not activate, means coupon do not exit
        $record->checkAvailable();

//        if (!$record->enabled) {
//            abort(404);
//        }
//
//        if ($record->total - $record->used <= 0) {
//            return response()->json(['msg' => 'This coupon has been used!'],403);
//        }
//
//        if ($record->not_before && $record->not_before->gt(Carbon::now())) {
//            return response()->json(['msg' => 'This coupon does not available now!'], 403);
//        }
//
//        if ($record->not_after && $record->not_after->lt(Carbon::now())) {
//            return response()->json(['msg' => 'This coupon is expired!'], 403);
//        }

        return $record;
    }
}
