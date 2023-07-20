<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\NewsResource;

use App\Models\News;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class NewsController extends Controller
{

    public function show(News $news)
  {
    $news->load('comments'); // Load related comments for the news
    return new NewsResource($news);
  }


public function index()
{
    // Implement pagination for news list
    $news = News::paginate(10);
    return NewsResource::collection($news);
}

public function store(Request $request)
{

    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $user = Auth::user();

    $news = new News();
    $news->title = $request->input('title');
    $news->content = $request->input('content');
    $news->user_id = $user->id; 
    $news->save();

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('news_images', 'public');
        $news->image_url = $imagePath;
        $news->save();
    }

    return new NewsResource($news);
}

public function update(Request $request, News $news)
{
    if ($request->hasFile('image')) {
        Storage::disk('public')->delete($news->image_url);
        $imagePath = $request->file('image')->store('news_images', 'public');
        $news->image_url = $imagePath;
        $news->save();
    }

}

public function destroy(News $news)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $user = Auth::user();

    if ($user->id !== $news->user_id) {
        return response()->json(['error' => 'You are not authorized to update this news'], 403);
    }

    $news->title = $request->input('title');
    $news->content = $request->input('content');
    $news->save();


    // Menghapus gambar terkait sebelum menghapus berita
    if ($news->image_url) {
        Storage::disk('public')->delete($news->image_url);
    }

    // Menghapus berita dari database
    $news->delete();

    return new NewsResource($news);

    // Use event & listener to create a log for news deletion (belum diimplementasi)
    // ...
}
}
