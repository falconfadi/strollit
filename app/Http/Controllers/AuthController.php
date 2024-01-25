<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\FailResource;
use App\Models\Category;
use App\Models\User;
use App\Services\CategoryService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
             'email' => 'required|string|email|max:255|',
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->response("Not Registered");
        } else {
            $user = User::create([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'email' =>$request->email,
                'email_verified_at' => Carbon::now()
            ]);
            $user->token = $user->createToken('authtoken');
            event(new Registered($user));
            //$token = $user->createToken('authtoken');
            return $this->response(" Registered Successfully",$user);
        }
    }

    public function login(LoginRequest $request)
    {
        $r = $request->authenticate();
        if (is_array($r)) {
            return response()->json($r);
        }
        $user_id = $request->user()->id;
        $user = User::where('id', $user_id)->first();
        $token = $request->user()->createToken('authtoken');

        if ($user) {

            $user->token = $token;
            $message = 'Logged in successfully';
            return $this->response($message,$user);
        } else {
            $message = 'Not Logged in !!';
            return $this->response($message);
        }
    }

    public function logout()
    {
        \Auth::user()->tokens()->delete();
        $message = 'Logged out';
        return $this->response($message);
    }
}
