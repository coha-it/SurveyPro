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
        'start_datetime', 
        'end_datetime', 
        'is_finished', 
        'is_canceled', 
        'only_editable_by_creator', 
        'is_public'
    ];

    /**
     * The appending attributes that are customized, but not a database-field
     *
     * @var array
     */
    protected $appends = [
        'url_full',
        'is_editable'
    ];

    // Attributes
    public function getUrlFullAttribute()
    {
        return route('backend.survey', ['id' => $this->id]);
    }

    public function getIsEditableAttribute()
    {
        return $this->isEditable();
    }

    // Methods
    public function isEditable() {
        return 
            $this->isUnfinished() && 
            $this->isUncanceled() && 
            $this->isNotInProcess();
    }

    public function isInProcess() {
        return 
            $this->active && 
            now()->toDateTimeString('Y-m-d H:i:s') > $this->start_datetime;
    }

    public function isNotInProcess() {
        return !$this->isInProcess();
    }

    public function isFinished() {
        return $this->is_finished;
    }

    public function isUnfinished() {
        return !$this->isFinished();
    }

    public function isCanceled() {
        return $this->is_canceled;
    }

    public function isUncanceled() {
        return !$this->isCanceled();
    }


    // With all Informations
    public function getSelfWithRelations()
    {
        return $this->with(['groups', /*'questions' */])->find($this->id);
    }

    /**
     * Get the Creator record associated with the Survey.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    /**
     * Get the user record associated with the info.
     */
    public function groups()
    {
        return $this->belongsToMany('App\Group', 'survey_group');
    }

}
