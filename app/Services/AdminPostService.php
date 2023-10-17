<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Services\FileStorage\FileStorageService;
use App\Services\FCMService\FCMService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AdminPostService
{
    public function __construct(
        private FileStorageService $fileStorageService,
    ) {
    }

    public function create_post($request)
    {
             $FcmToken = User::whereNotNull('device_token')->pluck('device_token')->all();
        if (is_null($request->cover_image)) {
            $post = Post::create([
                "admin_id" => $request->admin_id,
                "user_id" => $request->user_id,
                "category_id" => $request->category_id,
                "cover_image" => null,
                "article_title" => $request->article_title,
                "body_text_image" => $request->body_text_image,
                "short_words" => $request->short_words
            ]);
            FCMService::of($FcmToken)->withData(["title"=>$request->article_title,"body"=>$request->short_words,"id"=>$post->id])->send();
            return $post;
        } else {
            $post = Post::create([
                "admin_id" => $request->admin_id,
                "user_id" => $request->user_id,
                "category_id" => $request->category_id,
                "cover_image" => $this->fileStorageService->upload(\config('filesystems.folders.profiles'), $request->cover_image),
                "article_title" => $request->article_title,
                "body_text_image" => $request->body_text_image,
                "short_words" => $request->short_words
            ]);
            FCMService::of($FcmToken)->withData(["title"=>$request->article_title,"body"=>$request->short_words,"id"=>$post->id])->send();
            return $post;
        }
    }

    public function view_post($request)
    {
        $date = $request->date;
        return Post::with('category', 'admin')->when($date, function ($query, $date) {
            $query->whereDate('created_at', $date);
        })->latest()->get();
    }

    public function delete_post($type)
    {
        $post = Post::find($type);
        $post->likes()->delete();
        $post->delete();
        return true;
    }

    public function update_post($type, $request)
    {
        $post = Post::find($type);
        $post->admin_id = $request->admin_id;
        $post->category_id = $request->category_id;

        if ($request->cover_image) {
            $post->cover_image = $this->fileStorageService->upload(\config('filesystems.folders.profiles'), $request->cover_image);
        }

        $post->article_title = $request->article_title;
        $post->body_text_image = $request->body_text_image;
        $post->short_words = $request->short_words;
        $post->update();
        return $post;
    }

    public function total_posts()
    {
        return Post::count();
    }

    public function total_readers()
    {
        return DB::table('post_reads')->count();
    }

    public function like_lists($type)
    {
        $count = Like::with('users')->where('likeable_type', Post::class)->where('likeable_id', $type)->count();
        $users = Like::with('users')->where('likeable_type', Post::class)->where('likeable_id', $type)->latest()->get();
        return ["count" => $count, "like_lists" => $users];
    }

    public function read_lists($type)
    {
        $count = DB::table('post_reads')->where('post_id', $type)->count();
        $reads = DB::table('post_reads')->where('post_id', $type)->get();
        foreach ($reads as $read) {
            $user = User::query()->where("id", $read->user_id)->first();
            $read->user = $user;
        }
        return ["count" => $count, "reads" => $reads];
    }
}
