<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;


class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    )
    {
    }

    public function notiList():PaginatedResourceResponse
    {
        return new PaginatedResourceResponse($this->notificationService->notiList());
    }
}
