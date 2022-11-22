<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $guarded = array();

    public function adImage(){
        return $this->belongsTo(Image::class, 'ad_image_id','id');
    }
}
