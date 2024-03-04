<?php

namespace App\Http\Controllers\User;

use App\Helper\_EmailHelper;
use App\Helper\_RuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use App\Services\Interfaces\UserInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    private UserInterface $user;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }


    public function profile(Request $request): UserResource|JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            if ($user) {
                return UserResource::create($user);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return UserResource|JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = $this->user->store($request);

            if ($user) {
                _EmailHelper::sendEmail($user, [], 'thanks', "Thank you for registering");
                return UserResource::create($user);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            Log::error($exception);
            return BaseResource::return($exception->getMessage());
        }
    }

    public function contact(Request $request)
    {
        try {
            $rules = [
                "name" => _RuleHelper::_Rule_Require,
                "email" => _RuleHelper::_Rule_Require . "|" . _RuleHelper::_Rule_Email,
                'message_content' => _RuleHelper::_Rule_Require,
            ];
            $request->validate($rules);
            $res = _EmailHelper::sendEmailToInfo($request->input('email'), [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'message' => $request->input('message_content'),
            ], 'contact', 'Contact Us');
            Log::error($res);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $user = $this->user->loginByEmail($request);
            if ($user) {
                return UserResource::create($user);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }
}
