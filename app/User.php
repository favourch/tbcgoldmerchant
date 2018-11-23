<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'role_id', 'plan_id', 'referral_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function detail()
    {
        return $this->hasOne('App\Models\UserDetail');
    }

    public function bonus_points_logs()
    {
        return $this->hasMany('App\Models\BonusPointsLog');
    }

    public function membership_transactions()
    {
        return $this->hasMany('App\Models\MembershipTransaction');
    }

    public function cashout_transactions()
    {
        return $this->hasMany('App\Models\CashoutTransaction');
    }

    public function plan()
    {
        return $this->belongsTo('App\Models\UserPlan', 'plan_id');
    }

    public function maintenance_transactions()
    {
        return $this->hasMany('App\Models\MaintenanceTransaction');
    }

    public function level_two_user()
    {
        return $this->hasOne('App\Models\LevelTwoUser');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
