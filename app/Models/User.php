<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'gender',
        'phone',
        'dob',
        'newsletter_enable',
        'is_password_set'
    ];

    public function image(){
        //   return $this->hasOne(Media::class ,'id', 'avatar_id');
        return $this->belongsTo('images');
    }

    use Authenticatable;

	public function withRoles() {
		return $this -> belongsTo('role_users' , 'user_id' , 'role_id' ) -> withTimestamps() ;
	}

	public function withActivation() {

        return $this->hasOne('activations');
    }

    public static function byEmail($email){
        return static::whereEmail($email)->first();

    }

        /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function posts()
    {
        return $this->hasMany('Modules\Post\Entities\Post');
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }

    protected $casts = [
        "social_media" => "array",
        "permissions" => "array"
    ];
}
