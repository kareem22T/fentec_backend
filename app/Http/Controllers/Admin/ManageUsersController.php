<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\DataFormController;
use App\Traits\SendEmailTrait;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class ManageUsersController extends Controller
{
    use DataFormController;
    use SendEmailTrait;

    public function previewIndex () {
        return view('admin.dashboard.users.preview');
    }

    public function getUsers() {
        $usersRequests = User::where('approved', false)->where('rejected', false)->where('name', '!=', null)->get();
        $usersList = User::where('approved', true)->get();
        $incompleteUsers = User::where('name', null)->get();

        return  $this->jsondata(true, null, 'Successful Operation', [], [
            "usersRequests" => $usersRequests,
            "usersList" => $usersList,
            "incompleteUsers" => $incompleteUsers,
        ]);
    }

    public function approve(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ], [
            'id.required' => 'user id is required',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Login failed', [$validator->errors()->first()], []);
        }

        $user = User::find($request->id);
        $user->approved  = true;
        $user->save();

        if ($user) :
            $email = $user->email;
            $msg_title = 'Fentec Account Approved';
            $msg_body = 
                "Hello" . $user->name . " <br>You Account has been approved now you can enjoy the journy with us";

            $this->sendEmail($email, $msg_title, $msg_body);

            return  $this->jsondata(true, null, 'User Approved Successfuly', [], []);
        endif;
    }

    public function reject(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'rejection_reason' => 'required',
        ], [
            'id.required' => 'user id is required',
            'rejection_reason.required' => 'Please enter rejection_reason',
        ]);

        if ($validator->fails()) {
            return $this->jsondata(false, null, 'Login failed', [$validator->errors()->first()], []);
        }

        $user = User::find($request->id);
        $user->approved = false;
        $user->rejected = true;
        $user->rejection_reason = $request->rejection_reason;
        $user->save();

        if ($user) :
            $email = $user->email;
            $msg_title = 'Fentec Account Rejected';
            $msg_body = 
                "Hello" . $user->name . " <br>You Account has been rejected because: <br>" . $user->rejection_reason;

            $this->sendEmail($email, $msg_title, $msg_body);

            return  $this->jsondata(true, null, 'User Rejected Successfuly', [], []);
        endif;
    }
}
