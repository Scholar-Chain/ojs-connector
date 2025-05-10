<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserGroup extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = "user_group_id";
    public $timestamps = false;

    public $fillable = [
        'context_id',
        'role_id',
        'is_default',
        'show_title',
        'permit_self_registration',
        'permit_metadata_edit',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_user_groups', 'user_group_id', 'user_id');
    }
}
