<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\DataFormController;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\SavePhotoTrait;

class RegisterController extends Controller
{
    use DataFormController;
    use SavePhotoTrait;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'unique:users,email'],
            'phone' => 'required|unique:users,phone',
            'password' => ['required', 'min:8'],
        ], [
            'email.required' => 'Please enter your email address.',
            'phone.required' => 'Please enter your phone number.',
            'email.unique' => 'This email address already exists.',
            'phone.unique' => 'This phone number already exists.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'Password should be at least 8 characters long.',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Registration failed', [$validator->errors()->first()], []);
        }

        $createUser = User::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        if ($createUser) :
            $token = $createUser->createToken('token')->plainTextToken;
            return
                $this->jsonData(
                    true,
                    $createUser->active,
                    'Register successfuly',
                    [],
                    [
                        'id' => $createUser->id,
                        'email' => $createUser->email,
                        'phone' => $createUser->phone,
                        'token' => $token
                    ]
                );
        endif;
    }

    public function register2(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z ]+$/',
            'dob' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(15)->format('Y-m-d'),
                'after_or_equal:' . now()->subYears(70)->format('Y-m-d'),
            ],
            'identity' => 'required'
        ], [
            'name.required' => 'Please enter your name',
            'name.regex' => 'Please enter a valid name at least your first two name',
            'dob.required' => 'Date of birth is required.',
            'dob.date' => 'Invalid date format.',
            'dob.before_or_equal' => 'You must be at least 15 years old.',
            'dob.after_or_equal' => 'You must be at most 70 years old.',
            'identity.required' => 'Please upload your identity photo',
            'identity.regex' => 'unsupported extention'
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Registration failed', [$validator->errors()->first()], []);
        }

        $user = $request->user();
        $user->name = $request->name;
        $user->dob = $request->dob;
        
        if ($request->photo)
            $profile_pic = $this->saveImg($request->photo, 'images/uploads', 'profile' . $user->id);

        if($request->identity)
            $identity_pic = $this->saveImg($request->identity, 'images/uploads', 'identity' . $user->id);

        $user->identity_path = $identity_pic;
        // $user->identity_path = $identity_pic;
        $user->save();

        if ($user) :
            return
                $this->jsonData(
                    true,
                    $user->active,
                    'Registered successfuly',
                    [],
                    [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                    ]
                );
        endif;

    }

    public function login(LoginRequest $request)
    {
        if (filter_var($request->input('emailorphone'), FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $request->input('emailorphone'), 'password' => $request->input('password')];
        } else {
            $credentials = ['phone' => $request->input('emailorphone'), 'password' => $request->input('password')];
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            return $this->jsonData(true, $user->active, 'Successfully Operation', [], ['token' => $token]);
        }
        return $this->jsonData(false, null, 'Faild Operation', ['Your email/phone number or password are incorrect'], []);
    }

    public function getUser(Request $request)
    {
        if ($request->user())
            return $this->jsonData(true, $request->user()->verify, '', [], ['user' => $request->user()]);
        else
            return $this->jsonData(false, null, 'Account Not Found', [], []);
    }
}
