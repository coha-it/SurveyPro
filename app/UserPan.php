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
    protected $visible = [
        'is_pan_user', 'pan', 'pin', 'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $fillable = [
        'pan', 'pin'
    ];

    /**
     * Get the user record associated with the info.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id');
    }
}
