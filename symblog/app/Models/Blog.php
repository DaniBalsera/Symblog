<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Models\Comment;

class Blog extends Eloquent
{
    protected $table = 'blog';
    protected $fillable = ['id','title','author','blog','image','tags','created_at', 'updated_at'];
   
    // Definir la relación uno a muchos con Comment
    public function comment(){
        return $this->hasMany(Comment::class);
    }

    // Función getComments para obtener los comentarios
    public function getComments() {
        $comments = [];
        foreach (Blog::find($this->id)->comment as $value2) {
            $comments[] = $value2;
        }
        return $comments;
    }

    // Función numComments para obtener el número de comentarios
    public function numComments() {
        $num = 0;
        foreach (Blog::find($this->id)->comment as $value2) {
            $num++;
        }
        return $num;
    }
}