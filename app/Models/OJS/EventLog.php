<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventLog extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = "log_id";
    protected $table = "event_log";
    public $timestamps = false;

    protected $fillable = [
        "user_id",
        "date_logged",
        "event_type",
        "assoc_type",
        "assoc_id",
        "message",
        "is_translated"
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function assoc(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'assoc_id', 'submission_id');
    }
}
