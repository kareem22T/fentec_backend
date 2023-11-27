<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Coupon;
use App\Traits\DataFormController;

class AdminHomeController extends Controller
{
    use DataFormController;
    
    public function getIndex() {
        return view('admin.dashboard.home');
    }

    public function addCoupon (Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'code' => 'required',
            'start_in' => 'required',
            'end_in' => 'required',
        ], [
            'title.required' => 'Please Enter Coupon Title',
            'code.required' => 'Please Enter Coupon Code',
            'start_in.required' => 'Please Enter Coupon start date',
            'end_in.required' => 'Please Enter Coupon end date',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Login failed', [$validator->errors()->first()], []);
        }

        $coupon = Coupon::create([
            'title' => $request->title,
            'code' => $request->code,
            'start_in' => $request->start_in,
            'end_in' => $request->end_in,
        ]);

        return $this->jsondata(true, null, 'Coupon added successfuly', [], []);
    }
}
