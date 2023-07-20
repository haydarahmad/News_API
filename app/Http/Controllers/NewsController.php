<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\NewsResource;

class NewsController extends Controller
{

public function index()
{
    // Implement pagination for news list
    $news = News::paginate(10);
    return NewsResource::collection($news);
}

public function store(Request $request)
{
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('news_images', 'public');
        $news->image_url = $imagePath;
        $news->save();
    }

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
    // Implement deletion of news
    // Also, use event & listener to create a log for news deletion
}

}
