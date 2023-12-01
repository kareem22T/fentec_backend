<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\DataFormController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use DataFormController;

    public function getLoginIndex() {
        $createAdmin = Admin::all()->count() > 0 ? '' : Admin::create(['email' => 'fentec.dev@gmail.com', 'password' => Hash::make('admin')]);
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'please enter your email',
            'password.required' => 'please enter your password',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Login failed', [$validator->errors()->first()], []);
        }

        $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];

        if (Auth::guard('admin')->attempt($credentials)) {
            return $this->jsonData(true, true, 'Successfully Operation', [], []);
        }

        return $this->jsonData(false, null, 'Faild Operation', ['Email or password is not correct!'], []);
    }

    public function sellersManagerLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'please enter your email',
            'password.required' => 'please enter your password',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Login failed', [$validator->errors()->first()], []);
        }

        $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            if ($admin->role == "Sellers Manager") {
                $token = $admin->createToken('token')->plainTextToken;
                return $this->jsonData(true, true, 'Successfully Operation', [], ['token' => $token]);
            } else {
                return $this->jsonData(false, null, 'Faild Operation', ['You are not have the role as Sellers Manager'], []);
            }
        }
        return $this->jsonData(false, null, 'Faild Operation', ['admin email password are incorrect'], []);
    }

    public function getAdmin(Request $request)
    {
        if ($request->user()) :
            return $this->jsonData(true, true, '', [], ['admin' => $request->user()]);
        else :
            return $this->jsonData(false, null, 'Account Not Found', [], []);
        endif;
    }
    public function logout() {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }
}
