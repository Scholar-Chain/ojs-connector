<?php

namespace App\Models\OJS;

use App\Models\User as ConnectorUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'email',
        'url',
        'phone',
        'mailing_address',
        'billing_address',
        'country',
        'locales',
        'gossip',
        'date_last_email',
        'date_registered',
        'date_validated',
        'date_last_login',
        'must_change_password',
        'auth_id',
        'auth_str',
        'disabled',
        'disabled_reason',
        'inline_help',
    ];

    public function userSettings(): HasMany
    {
        return $this->hasMany(UserSetting::class, 'user_id');
    }

    public function userSetting($key): HasOne
    {
        return $this->hasOne(UserSetting::class, 'user_id')
            ->where('setting_name', $key);
    }

    public function connectorUser()
    {
        return ConnectorUser::where('ojs_user_id', $this->user_id)->first();
    }

    public function userGroups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class, 'user_user_groups', 'user_id', 'user_group_id');
    }
}
