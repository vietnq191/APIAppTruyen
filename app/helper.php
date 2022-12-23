<?php

use App\Models\Author;
use App\Models\Categories;
use App\Models\Story;

if (!function_exists('getAuthorByName')) {
    function getAuthorByName($name = "")
    {
        $author = Author::where("author_name", $name)->first();
        if(!$author)
        {
            $author = Author::create(["author_name" => $name]);
        }

        return $author;  
    }
}

if (!function_exists('getStoryByName')) {
    function getStoryByName($name = "")
    {
        $stories = Story::where("title", $name)->first();
        if(!$stories)
        {
            dd("error getStoryByName");
        }
        return $stories;  
    }
}

if (!function_exists('getCategoryByName')) {
    function getCategoryByName($name = "")
    {
        $category = Categories::where("category_name", $name)->first();
        if(!$category)
        {
            return null;
        }
        return $category;  
    }
}