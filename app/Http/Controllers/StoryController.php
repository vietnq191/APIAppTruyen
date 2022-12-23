<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetDetailChapterRequest;
use App\Http\Requests\GetStoriesByCategoryRequest;
use App\Http\Requests\GetStoryRequest;
use App\Models\Chapter;
use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index(Request $request)
    {
        $stories = Story::with('getAuthor')->with('getCategories.getCategory:id,category_name')
            ->filter($request->all())->sort($request->all())->paginate();
        return response($stories);
    }

    public function getListByCategory(GetStoriesByCategoryRequest $request)
    {
        $stories = Story::with('getAuthor')->with('getCategories.getCategory:id,category_name')
        ->whereHas('getCategories', function ($query) use ($request) {
            $query->where('category_id', $request->id);
        })
        ->filter($request->all())->sort($request->all())->paginate();
        return response($stories);
    }

    public function show(GetStoryRequest $request)
    {
        $story = Story::where('id', $request->id)->with('getAuthor')->with('getCategories.getCategory:id,category_name')->with('getChapters:id,story_id,title')->get();
        return response($story);
    }

    public function getDetailChapter(GetDetailChapterRequest $request)
    {
        $story = Chapter::where('story_id', $request->story_id)->where('id', $request->chapter_id)->get();
        return response($story);
    }

    public function getListChapters(GetStoryRequest $request)
    {
        $story = Chapter::select('id', 'title', 'created_at')->where('story_id', $request->id)->get();
        return response($story);
    }
}
