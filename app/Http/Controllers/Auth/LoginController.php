<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                $messages = $this->responseMessage($validator->errors()->toArray());
                return $this->apiResponse([], $messages, parent::ERROR_STATUS);
            }
            $credentials = $request->only([
                'email',
                'password',
            ]);
            if ($token = auth('api')->attempt($credentials)) {
                /** @var User $user */
                $user = auth('api')->user();
                if ($user->is_active !== User::STATUS_ACTIVE) {
                    auth()->logout();
                    return $this->apiResponse([], 'Your account is inactive', parent::ERROR_STATUS);
                }
                $successResponse = array(
                    'userInfo' => $user,
                    'access_token' => 'Bearer ' . $token,
                    'token_type' => 'bearer',
                );
                return $this->apiResponse($successResponse);
            }
            return $this->apiResponse([], 'Username or password is incorrect', parent::ERROR_STATUS);
        } catch (\Exception $ex) {

            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
