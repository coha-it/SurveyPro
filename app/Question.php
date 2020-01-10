<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';
    protected $dates = ['deleted_at'];
    protected $with = ['options'];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Relations
     */

    // Survey
    public function survey()
    {
        return $this->belongsTo('App\Survey');
    }

    public function awnser()
    {
        return $this->hasOne('App\Awnser');
    }

    public function options()
    {
        return $this->hasMany('App\QuestionOption');
    }

    public function question_options()
    {
        return $this->hasMany('App\QuestionOption');
    }

    public function delete()
    {
        // delete all related photos
        foreach ($this->options as $option) {
            $option->delete();
        }

        // delete the question
        return parent::delete();
    }

}
