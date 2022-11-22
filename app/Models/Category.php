<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['category_name', 'language', 'slug', 'meta_description', 'meta_keywords', 'order', 'show_on_menu', 'show_on_homepage'];

    public function subCategory()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class)->limit(10);
    }

    public function rssFeed()
    {
        return $this->hasMany(RssFeed::class);
    }
    
}
