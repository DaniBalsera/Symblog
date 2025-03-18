<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Comment extends Eloquent
{
    protected $table = 'comment';
    protected $fillable = ['user', 'comment', 'blog_id'];

    // Relación: Un comentario pertenece a un blog
    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}
