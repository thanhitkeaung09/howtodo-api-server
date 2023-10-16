<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AppleLoginRequest;
use App\Services\AppleLoginService\AppleLoginService;
class AppleController extends Controller
{
    public function __construct(
        public AppleLoginService $appleLoginService
    ){}
    public function __invoke(AppleLoginRequest $request)
    {
           return $this->appleLoginService->handle($request->payload());
    }
}
