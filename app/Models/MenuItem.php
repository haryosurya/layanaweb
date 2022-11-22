<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [];
    protected $table='menu_item';

    public function menu()
    {
        return $this->hasOne(Menu::class,'id','menu_id');
    }
    public function parent()
    {
        return $this->belongsToOne(static::class, 'parent');
    }

    //each menu might have multiple children
    public function children()
    {
        return $this->hasMany(static::class, 'parent')
                    ->with('children')
                    ->orderBy('order', 'asc');
    }
   public function category()
    {
        return $this->belongsTo(Category::class);
    }
   public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
   public function page()
    {
        return $this->belongsTo(Page::class);
    }
    public function Post()
    {
        return $this->belongsTo(Post::class);
    }

    public function postByCategory()
    {
        return $this->hasMany(Post::class, 'category_id', 'category_id')
                    ->with('image','user')
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->take(4);
    }
}
