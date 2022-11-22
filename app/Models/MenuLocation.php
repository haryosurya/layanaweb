<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuLocation extends Model
{
    protected $fillable = ['title', 'unique_name', 'menu_id'];

    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }

    public function menuItem()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'menu_id')->orderBy('order', 'asc');
    }
}
