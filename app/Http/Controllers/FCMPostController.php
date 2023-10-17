<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\ApiSuccessResponse;


class FCMPostController extends Controller
{
    public function save_token(Request $request)
    {
         $validated = $request->validate([
        'fcm_token' => 'required',
    ]);
        $user = auth()->user();
        $user->device_token = $request->fcm_token;
        $user->save();
        return new ApiSuccessResponse("Token is successfully saved");
    }
}
