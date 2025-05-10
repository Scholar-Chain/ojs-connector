<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageAssignment extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = "stage_assignment_id";
    public $timestamps = false;

    protected $fillable = [
        "submission_id",
        "user_group_id",
        "user_id",
        "date_assigned",
        "recommend_only",
        "can_change_metadata"
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id', 'submission_id');
    }

    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id', 'user_group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
