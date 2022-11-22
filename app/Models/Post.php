<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Sentinel;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Post extends Model implements Feedable
{
    protected $fillable = ['title'];

    public function image(){
        //   return $this->hasOne(Media::class ,'id', 'avatar_id');
        return $this->belongsTo(Image::class);
    }
    public function videoThumbnail(){
        return $this->belongsTo(Image::class, 'video_thumbnail_id','id');
    }
    public function video(){
        return $this->belongsTo(Video::class, 'video_id','id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function subCategory(){
        return $this->belongsTo(SubCategory::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
 

    public function replay()
    {
        return $this->hasMany(Comment::class, 'comment_id')->where('comment_id',null)->with('replay');
    }
    public function audio(){
        return $this->belongsToMany(Audio::class)->withTimestamps();
    }
 
    public function toFeedItem(): FeedItem
    {

        return FeedItem::create([
            'id'        => $this->id,
            'title'     => $this->title,
            'summary'   => @$this->content,
            'updated'   => $this->created_at,
            'link'      => route('article.detail', ['id' => $this->slug]),
            'author'    => $this->user->first_name,
            'enclosure' => $this->image ? @$this->image->og_image: 'default-image/default-730x400.png',
        ]);
    }

    public static function getFeedItems()
    {
        return Post::with(['category','image'])
            ->where('visibility', 1)
            ->where('status',1)
            ->whereNotNull('image_id')
            ->where('language', LaravelLocalization::setLocale() ?? settingHelper('default_language'))
            ->orderBY('id', 'desc')
            ->limit(50)->get();
    }

    protected $casts = [
    	"contents" => "array"
    ];

}
