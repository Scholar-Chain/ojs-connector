<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Publication extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = 'publication_id';
    public $timestamps = false;

    protected $fillable = [
        "last_modified",
        "section_id",
        "submission_id",
        "primary_contact_id",
        "status",
        "version"
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'section_id', 'journal_id');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id', 'current_publication_id');
    }

    public function publicationSetting(): HasOne
    {
        return $this->hasOne(PublicationSetting::class, 'publication_id', 'section_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'primary_contact_id', 'author_id');
    }
}
