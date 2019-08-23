<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description_public',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $visible = [
        'id', 'name', 'description_public'
    ];

    /**
     * Get the user record associated with the info.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Get the user record associated with the info.
     */
    public function members()
    {
        return $this->belongsToMany('App\User')->wherePivot('is_member', 1);
    }
    
    /**
     * Get the user record associated with the info.
     */
    public function moderators()
    {
        return $this->belongsToMany('App\User')->wherePivot('is_mod', 1);
    }
}
