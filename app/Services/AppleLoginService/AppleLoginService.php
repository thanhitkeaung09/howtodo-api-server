<?php

declare(strict_types=1);

namespace App\Services\AppleLoginService;

use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use App\Services\FileStorage\FileStorageService;
use App\Services\SocialService\SocialUserService;
use App\Services\UserService;
use App\Services\AuthService;

class AppleLoginService
{
    public function __construct(
        public UserService $userService,
        public AuthService $authService,
        public FileStorageService $fileStorageService
    ) {
    }
    public function handle($request)
    {
        //to start here
        $exists = User::query()->where("social_id", $request->social_id)->first();
        // $check = User::query()->where("social_id", $request->social_id)->exists();
        $softDelete = User::onlyTrashed()->where('social_id','like','%'. $request->social_id.'%')->first();
        $social_id = explode('_',$softDelete->social_id);
        // $softdeleteUser = User::query()->where('email', $softDelete->email)->first(); // to start here

        if ($softDelete) {
            $existUser = User::query()->where('email', $softDelete->email)->first();
            if ($existUser) {
                return new ApiSuccessResponse($this->authService->generateToken($existUser, $existUser->email));
            } else {
                // dd($softDelete->social_id);
                $user = new User();
                $user->name = $softDelete->name;
                $user->email = $softDelete->email;
                $user->social_id = $social_id[0];
                $user->device_token = $request->fcm_token;
                $user->social_type = $request->social_type;
                $user->profile_image =
                    $this->fileStorageService->put(
                        config('filesystems.folders.profiles'),
                        "https://icons.veryicon.com/png/o/miscellaneous/icon-icon-of-ai-intelligent-dispensing/login-user-name-1.png",
                    );
                $user->phone = null;
                $user->save();
                return new ApiSuccessResponse($this->authService->generateToken($user, $user->social_id));
            }
        } else {
            if ($exists) {
                return new ApiSuccessResponse($this->authService->generateToken($exists, $exists->social_id));
            } else {
                return $this->register($request);
            }
        }
    }

    public function register($request)
    {
        $user = User::create([...$request->toArray(), 'profile_image' => $this->fileStorageService->put(
            config('filesystems.folders.profiles'),
            "https://icons.veryicon.com/png/o/miscellaneous/icon-icon-of-ai-intelligent-dispensing/login-user-name-1.png",
        ), "phone" => null]);
        return new ApiSuccessResponse($this->authService->generateToken($user, $user->social_id));
    }
}
