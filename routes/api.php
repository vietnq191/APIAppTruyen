<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/stories', [StoryController::class, 'index']);
Route::get('/stories/category-{id}', [StoryController::class, 'getListByCategory']);
Route::get('/stories/{id}', [StoryController::class, 'show']);
Route::get('/story/{story_id}/{chapter_id}', [StoryController::class, 'getDetailChapter']);
Route::get('/list-chapters/{id}', [StoryController::class, 'getListChapters']);