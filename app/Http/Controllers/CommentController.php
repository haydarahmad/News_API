<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\CommentResource;


class CommentController extends Controller
{
    public function index(News $news)
{
    $comments = $news->comments;
    return CommentResource::collection($comments);
}

    public function store(Request $request, News $news)
  {
    Redis::queue('comment-creation', [
        'news_id' => $news->id,
        'user_id' => auth()->user()->id,
        'comment' => $request->input('comment'),
    ]);

    return response()->json(['message' => 'Comment creation queued successfully']);
  }

  public function show(News $news)
  {
    // Implement getting news detail along with posted comments
    return new NewsResource($news);
  }
}
