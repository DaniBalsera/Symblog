<?php
require "../../bootstrap.php";
use App\Models\Blog;
// $blog = Blog::Create([
//     'title' => "Almeja2 Khan",
//     'author' => "ahmed.khan@lbs.com",
//     'blog' => "skjfn",
//     'image' => "jght.png",
//     'tags' => "php",
// ]);

// $blog = Blog::all();
// var_dump($blog);
foreach (Blog::find(97)->comment as $value) {
    var_dump($value->comment);
}