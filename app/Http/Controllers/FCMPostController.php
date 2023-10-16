<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\ApiSuccessResponse;


class FCMPostController extends Controller
{
    public function save_token(Request $request)
    {
        $user = auth()->user();
        $user->device_token = $request->fcm_token;
        $user->update();
        return new ApiSuccessResponse("Token is successfully saved");
    }
}
