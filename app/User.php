<?php

namespace App;

use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the info record associated with the user.
     */
    public function info()
    {
        return $this->hasOne('App\UserInfo', 'user_id');
    }

    /**
     * Get the right record associated with the user.
     */
    public function right()
    {
        return $this->hasOne('App\UserRight', 'user_id');
    }

    /**
     * Get the pan record associated with the user.
     */
    public function pan()
    {
        return $this->hasOne('App\UserPan', 'user_id');
    }

    /**
     * Get the newsletter record associated with the user.
     */
    public function newsletter()
    {
        return $this->hasOne('App\UserNewsletter', 'user_id');
    }

    /**
     * Get the info record associated with the user.
     */
    public function getSelfWithRelations()
    {
        return $this->with(['pan', 'right'])->find($this->id);
    }

    /**
     * Check if this User Is a Pan User
     */
    public function isPanUser() {
        return $this->pan && $this->pan->is_pan_user === 1;
    }

    /**
     * Check if this User is a E-Mail and not a PAN-User
     */
    public function isEmailUser() {
        return !$this->isPanUser();
    }

    /**
     * Check if Is Admin
     */
    public function isAdminUser() {
        return $this->right && $this->right->admin === 1;
    }

    /**
     * Check if User can create other Users
     */
    public function canCreateUsers() {
        return $this->right && $this->right->create_users === 1;
    }

    /**
     * Check if User can create Groups
     */
    public function canCreateGroups() {
        return $this->right && $this->right->create_groups === 1;
    }

    /**
     * Check if User can create Surveys
     */
    public function canCreateSurveys() {
        return $this->right && $this->right->create_surveys === 1;
    }
}
