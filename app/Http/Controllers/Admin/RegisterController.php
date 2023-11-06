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

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }
}
