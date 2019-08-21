<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserPan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'u_pans';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pan', 'pin', 'failed_logins'
    ];

    /**
     * Get the user record associated with the info.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id');
    }
}
