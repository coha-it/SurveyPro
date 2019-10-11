<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'surveys';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'created_by', 
        'active', 
        'author', 
        'title', 
        'desc_short', 
        'desc_long', 
        'start_date', 
        'end_date', 
        'is_finished', 
        'is_canceled', 
        'only_editable_by_creator', 
        'is_public', 
    ];

    /**
     * Get the user record associated with the info.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
}
