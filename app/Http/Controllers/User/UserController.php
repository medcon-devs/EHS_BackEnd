<?php

namespace App\Http\Controllers\User;

use App\Helper\_EmailHelper;
use App\Helper\_RuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use App\Services\Interfaces\UserInterface;
use Exception;
use App\Models\User;
use App\Models\UserWorkshops;
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

    
    public function exportCSV()
    {

        $csvFileName = 'users-' . strtotime(date('Y-m-d H:i:s')) . '.csv';
        $headers = [
            "Content-Encoding" => "UTF-8",
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
        ];
        $data = User::query()->get();
        $callback = function () use ($data) {
            $columns = ['Name', 'Email', 'Member Type', 'Phone', 'Job Title', 'department', 'hospital', 'Bayanati Number', 'Speciality', 'Code', 'Created At'];
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);
            foreach ($data as $key => $item) {
                $row = [
                    $item->name,
                    $item->email,
                    $item->member_type,
                    $item->phone,
                    $item->jobTitle,
                    $item->department,
                    $item->hospital,
                    $item->bayanati_number,
                    $item->speciality,
                    $item->code()->first() ? $item->code()->first()->code : "-",
                    $item->created_at,
                ];
                // Log::error(json_encode($row, true));
                $row_utf8 = array_map('utf8_encode', $row);

                fputcsv($file, $row_utf8);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportWorkshopCSV()
    {

        $csvFileName = 'users-workshop-' . strtotime(date('Y-m-d H:i:s')) . '.csv';
        $headers = [
            "Content-Encoding" => "UTF-8",
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
        ];
        $data = UserWorkshops::query()->get();
        $callback = function () use ($data) {
            $columns = ['Name','Email','Member Type','Job Title','Department','Hospital','Bayanati Number','Speciality','Code', 'option_1', 'option_2', 'Created At'];
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);
            foreach ($data as $key => $item) {
                $user = $item->User()->first();
                if($user){
                $row = [
                    $user ? $user->name:"-",
                    $user ? $user->email:"-",
                    $user ? $user->member_type:"-",
                    $user ? $user->jobTitle:"-",
                    $user ? $user->department:"-",
                    $user ? $user->hospital:"-",
                    $user ? $user->bayanati_number:"-",
                    $user ? $user->speciality:"-",
                    $user ? ($user->code()->first() ? $user->code()->first()->code : "-"):"-",
                    $item->option_1,
                    $item->option_2,
                    $item->created_at,
                ];
                $row_utf8 = array_map('utf8_encode', $row);
                fputcsv($file, $row_utf8);
            }

            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
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
                if($user->member_type=="EHS Staff"){
                    _EmailHelper::sendEmail($user, [], 'thanks', "Thank you for registering");
                }else{
                    _EmailHelper::sendEmail($user, [], 'thanks_external', "Thank you for registering");
                }
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
