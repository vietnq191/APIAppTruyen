<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Categories::all()->toArray();
        $data_history = [
            "id"=> -1,
            "category_name" => "Truyện đã xem",
            "description" => "",
            "deleted_at" => null,
            "created_at" => null,
            "updated_at" => null
        ];
        array_unshift($categories, $data_history);
        return response($categories);
    }
}
