<?php

namespace App\Http\Controllers\Seller;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Seller;
use App\Models\Admin;
use App\Models\Invetation_code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\DataFormController;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\SavePhotoTrait;
use App\Traits\SendEmailTrait;

use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    use DataFormController;
    use SavePhotoTrait;
    use SendEmailTrait;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'unique:sellers,email', 'email'],
            'phone' => 'required|unique:sellers,phone',
            'address' => ['required'],
            'password' => ['required', 'min:8'],
        ], [
            'name.required' => 'Please enter seller Name.',
            'email.required' => 'Please enter seller email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Please enter seller phone number.',
            'email.unique' => 'This email address already exists.',
            'phone.unique' => 'This phone number already exists.',
            'address.required' => 'Please enter seller address.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'Password should be at least 8 characters long.',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Registration failed', [$validator->errors()->first()], []);
        }

        $createUser = Seller::create([
            'email' => $request->email,
            'address' => $request->address,
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        if ($createUser) :
            $token = $createUser->createToken('token')->plainTextToken;
            return
                $this->jsonData(
                    true,
                    $createUser->verify,
                    'Register successfuly',
                    [],
                    [
                        'id' => $createUser->id,
                        'name' => $createUser->email,
                        'email' => $createUser->email,
                        'phone' => $createUser->phone,
                        'token' => $token
                    ]
                );
        endif;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emailorphone' => 'required',
            'password' => 'required|min:8',
        ], [
            'emailorphone.required' => 'please enter seller email or phone number',
            'password.required' => 'please enter seller password',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Login failed', [$validator->errors()->first()], []);
        }

        if (filter_var($request->input('emailorphone'), FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $request->input('emailorphone'), 'password' => $request->input('password')];
        } else {
            $credentials = ['phone' => $request->input('emailorphone'), 'password' => $request->input('password')];
        }

        if (Auth::guard('seller')->attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            return $this->jsonData(true, $user->verify, 'Successfully Operation', [], ['token' => $token]);
        }
        return $this->jsonData(false, null, 'Faild Operation', ['seller email/phone number or password are incorrect'], []);
    }

    public function changePassword(Request $request) {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, $user->verify, 'Change password failed', [$validator->errors()->first()], []);
        }

        $currentPassword = $request->old_password;

        if (!Hash::check($currentPassword, $user->password)) {
            return $this->jsondata(false, $user->verify, 'Change password', ['Incorrect old password'], []);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        if ($user)
            return $this->jsondata(true, $user->verify, 'You have changed seller password successfuly', [], []);

    }

    public function editEmail(Request $request) {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'new_email' => 'required',
        ],[
            'new_email.required' => 'please write an valid email'
        ]);

        if ($validator->fails())
            return $this->jsondata(false, $user->verify, 'Change email failed', [$validator->errors()->first()], []);

        if ($user->email !== $request->new_email):
            $user->email = $request->new_email;
            $user->verify = 0;
            $user->save();
        endif;

        $user->currentAccessToken()->delete();
        $token = $user->createToken('token')->plainTextToken;

        if ($user)
            return 
                $this->jsondata(
                    true, 
                    $user->verify, '
                    seller email has changed successfully!', 
                    [], 
                    [
                        'name' => $token,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'token' => $token
                    ]
                );

    }

    public function editPhone(Request $request) {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'new_phone' => 'required',
        ],[
            'new_phone.required' => 'please write an valid phone number'
        ]);

        if ($validator->fails())
            return $this->jsondata(false, $user->verify, 'Change phone failed', [$validator->errors()->first()], []);

        if ($user->phone !== $request->new_phone):
            $user->phone = $request->new_phone;
            $user->save();
        endif;

        $user->currentAccessToken()->delete();
        $token = $user->createToken('token')->plainTextToken;

        if ($user)
            return 
                $this->jsondata(
                    true, 
                    $user->verify, 
                    'seller phone number has changed successfully!', 
                    [], 
                    [
                        'name' => $token,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'token' => $token
                    ]
                );

    }

    public function getUser(Request $request)
    {
        if ($request->user()) :
            if ($request->notification_token) :
                $request->user()->notification_token = $request->notification_token;
                $request->user()->save();
            endif;
            return $this->jsonData(true, $request->user()->verify, '', [], ['user' => $request->user()]);
        else :
            return $this->jsonData(false, null, 'Account Not Found', [], []);
        endif;
    }

    public function seenApprovingMsg(Request $request)
    {
        if ($request->user()) :
                $request->user()->approving_msg_seen = true;
                $request->user()->save();
            return $this->jsonData(true, $request->user()->verify, '', [], []);
        endif;
    }

    public function logout (Request $request) {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        if ($user)
            return $this->jsonData(true, 0, 'Logged out successfuly', [], []);
        else
            return $this->jsonData(false, null, 'could not logout', ['Server error try again later'], []);


    }
}
