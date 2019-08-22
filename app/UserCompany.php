<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserCompany extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'u_companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'created_by'
    ];

    /**
     * Get the user record associated with the info.
     */
    public function user()
    {
        return $this->hasMany('App\User', 'id');
    }

    /**
     * Get the user record associated with the info.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

}
