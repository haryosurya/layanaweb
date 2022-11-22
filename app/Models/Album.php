<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = [];
 
    
        public function galleryImages()
        {
            return $this->hasMany(GalleryImage::class);
        }
}
