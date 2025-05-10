<?php

namespace App\Models\OJS;

use App\Traits\FieldsType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    use FieldsType;

    protected $connection = 'ojs';
    protected $primaryKey = 'submission_id';
    public $timestamps = false;

    protected $fillable = [
        "context_id",
        "date_last_activity",
        "last_modified",
        "locale",
        "stage_id",
        "submission_progress",
        "current_publication_id",
    ];

    public function context(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'context_id', 'journal_id');
    }

    public function publication(): HasOne
    {
        return $this->hasOne(Publication::class, 'publication_id', 'current_publication_id');
    }

    public function stageAssignment(): HasOne
    {
        return $this->hasOne(StageAssignment::class, 'submission_id', 'submission_id');
    }
}
