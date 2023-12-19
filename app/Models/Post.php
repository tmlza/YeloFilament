<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title','slug','thumbnail','color','category_id','content','tags','published'];

    protected $casts = [
        'tags'=> 'array'
        ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function authors() {
        return $this->belongsToMany(User::class,'post_users')->withPivot(['order'])->withTimestamps();
    }
}
