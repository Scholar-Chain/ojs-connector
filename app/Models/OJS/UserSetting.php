<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = null;
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'locale',
        'setting_name',
        'assoc_type',
        'assoc_id',
        'setting_value',
        'setting_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
