<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AwnserOption extends Model
{
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'awnser_options';
    // protected $dates = ['deleted_at'];
    // protected $with = ['options'];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Relations
     */

    // Awnser
    public function awnser()
    {
        return $this->belongsTo('App\Awnser');
    }

    // Option
    public function question_option()
    {
        return $this->belongsTo('App\QuestionOption');
    }
}
