<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
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

class RegisterController extends Controller
{
    use DataFormController;
    use SavePhotoTrait;
    use SendEmailTrait;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'unique:users,email', 'email'],
            'phone' => 'required|unique:users,phone',
            'password' => ['required', 'min:8'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
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
                    $createUser->verify,
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
        $user->approved = 0;
        $user->rejected = 0;
        $user->rejection_reason = null;
        $user->approving_msg_seen = 0;
        
        if ($request->photo) :
            $profile_pic = $this->saveImg($request->photo, 'images/uploads', 'profile' . $user->id);
            $user->photo_path = $profile_pic;
        endif;

        if($request->identity) :
            $identity_pic = $this->saveImg($request->identity, 'images/uploads', 'identity' . $user->id);
            $user->identity_path = $identity_pic;
        endif;
        $user->save();

        if ($user) :
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $email = $admin->email;
                $msg_title = 'New Registeration Request';
                $msg_body = 
                    $user->name . " is waiting for review their account. <a href=''>Show request</a>" . "<br>" . "<br>" .
                    "<strong>User Information: </strong>" . "<br>" . 
                    "Name: " . $user->name . "<br>" .
                    "Name: " . $user->email . "<br>" .
                    "Name: " . $user->phone . "<br>";

                $this->sendEmail($email, $msg_title, $msg_body);
            }
            return
                $this->jsonData(
                    true,
                    $user->verify,
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

    public function collectPoints(Request $request)  {
        $validator = Validator::make($request->all(), [
            'choice' => 'required',
            'code' => 'required_id:choice,3',
        ], [
            'code.required_if' => 'please enter your friend invetation code',
            'choice.required' => 'please chose where did you knew about the app.',
        ]);

        if ($request->choice == 3) {
            $code = Invetation_code::where('code', $request->code)->first();
            if (!$code)
                return $this->jsondata(false, null, 'invalid invetation code', ['Invaled invetation code'], []);

            $request->user()->coins = ((int) $request->user()->coins) + 10;
            $request->user()->save();
            $user_owner = User::find($code->user_owner->id);
            $user_owner->coins = ((int) $user_owner->coins) + 10;
            $user_owner->save();

            return
                $this->jsonData(
                    true,
                    true,
                    'You have won 10 points',
                    [],
                    []
                );

        } else {
            $request->user()->coins = ((int) $request->user()->coins) + 10;
            $request->user()->save();

            return
                $this->jsonData(
                    true,
                    true,
                    'You have won 10 points',
                    [],
                    []
                );
        }

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emailorphone' => 'required',
            'password' => 'required|min:8',
        ], [
            'emailorphone.required' => 'please enter your email or phone number',
            'password.required' => 'please enter your password',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Login failed', [$validator->errors()->first()], []);
        }

        if (filter_var($request->input('emailorphone'), FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $request->input('emailorphone'), 'password' => $request->input('password')];
        } else {
            $credentials = ['phone' => $request->input('emailorphone'), 'password' => $request->input('password')];
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            return $this->jsonData(true, $user->verify, 'Successfully Operation', [], ['token' => $token]);
        }
        return $this->jsonData(false, null, 'Faild Operation', ['Your email/phone number or password are incorrect'], []);
    }

    public function sendVerfication(Request $request)
    {
        $code = rand(100000, 999999);

        $user = $request->user();
        $user->last_code = $code;
        $user->save();

        $email = $user->email;
        $msg_title = 'Verfication code';
        $msg_body = 'Your email verfication code is: <b>' . $code . '</b>';

        $this->sendEmail($email, $msg_title, $msg_body);

        if ($user) :
            return
                $this->jsonData(
                    true,
                    $user->verify,
                    'We have sent you a verification code on your email',
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

    public function activeAccount(Request $request) {
        $validator = Validator::make($request->all(), [
            'code' => 'required|numeric|digits:6',
        ], [
            'code.required' => 'Please enter your verification code',
            'code.numeric' => 'The code must be a number',
            'code.digits' => 'The code must be a 6 digits',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Activation failed', [$validator->errors()->first()], []);
        }

        $user = $request->user();

        if ($request->code == $user->last_code) {
            $user->verify = true;
            $user->save();

            if ($user) :
                return
                    $this->jsonData(
                        true,
                        $user->verify,
                        'Account has been verified successfuly',
                        [],
                        [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone,
                        ]
                    );
            endif;
        } else {
            return
                $this->jsonData(
                    false,
                    $user->verify,
                    'Account faild',
                    ['The code you entered is not correct, check your email again or click resend'],
                    [
                    ]
                );
        }
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
            return $this->jsondata(true, $user->verify, 'You have changed your password successfuly', [], []);

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
                    Your email has changed successfully!', 
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
                    'Your phone number has changed successfully!', 
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
