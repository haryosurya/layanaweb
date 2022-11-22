<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function withUsers() {
		return $this -> belongsToMany( 'role_users' , 'role_id' , 'user_id' ) -> withTimestamps() ;
	}

    public static function allRole(){
        return Static::all();
    }

    public function scopeWithoutSuperadmin($query)
    {
        return $query->where('id', '!=', 1);
    }
}
