<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\SurveyFinished;
use App\SurveyFinishedData;

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
        'is_editable',
        'is_fillable',
        'has_started',
        'has_ended',
        'is_expired',
        'question_count',
        'awnser_count',
        'user_finished'
    ];

    // Attributes
    public function getUrlFullAttribute()
    {
        return route('backend.survey', ['id' => $this->id]);
    }

    public function getIsExpiredAttribute()
    {
        return $this->isExpired();
    }

    public function getIsEditableAttribute()
    {
        return $this->isEditable();
    }

    public function getQuestionCountAttribute()
    {
        return $this->questions()->count();
    }

    public function getAwnserCountAttribute()
    {
        return $this->userAwnsers()->count();
    }

    public function getHasStartedAttribute()
    {
        return $this->hasStarted();
    }

    public function getHasEndedAttribute()
    {
        return $this->hasEnded();
    }

    public function getUserFinishedAttribute()
    {
        return $this->userFinished()->first();
    }

    // Only if the Survey is Fillable
    // + If Its in current Process
    // + If it is not already finished
    public function getIsFillableAttribute() {
        return
            $this->isInProcess() &&
            $this->isUnexpired() &&
            $this->isUnfinished();
    }

    // Methods
    public function isEditable() {
        return
            $this->isUnfinished() &&
            $this->isUncanceled() &&
            $this->isNotInProcess();
    }

    public function hasStarted() {
        return now()->toDateTimeString() > $this->start_datetime;
    }

    public function hasEnded() {
        return now()->toDateTimeString() > $this->end_datetime;
    }

    public function isUnexpired() {
        return !$this->isExpired();
    }

    public function isExpired() {
        return
            $this->hasStarted() &&
            $this->hasEnded();
    }

    public function isInProcess() {
        return
            $this->active &&
            now()->toDateTimeString() > $this->start_datetime &&
            now()->toDateTimeString() < $this->end_datetime;
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
        return $this->with(['groups', 'questions'])->find($this->id);
    }

    public function getSelfWithQuestions()
    {
        return $this
                ->with(['questions'])
                ->find($this->id);
    }

    public function getSelfWithQuestionsAndUsersAwnser()
    {
        return $this
                ->with(['questions', 'questions.usersAwnser'])
                ->find($this->id);
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

    /**
     * Get the Questions
     */
    public function questions()
    {
        return $this->hasMany('App\Question')->orderBy('order');
    }

    /**
     * Get the Questions
     */
    public function question($id)
    {
        return $this->questions()->find($id);
    }

    public function userAwnsers()
    {
        return $this
                ->hasManyThrough('App\Awnser', 'App\Question')
                ->where('user_id', auth()->user()->id);
    }

    /**
     * Check the Finished
     */
    public function userFinished()
    {
        // return $this
        //         ->hasOne('App\SurveyFinished')
        //         ->where('user_id', auth()->user()->id);
        return $this->hasOneThrough(
            'App\SurveyFinished',   // owner
            'App\User',             // car
            'id',                   // Foreign key on cars table...
            'survey_id'             // Foreign key on owners table...
        );
    }

    public function finishSurvey()
    {
        return $this->userFinished()->create([
            'survey_id' => $this->id,
            'user_id' => auth()->user()->id
        ]);
    }

}
