<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlledVocab extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = 'controlled_vocab_id';
    public $timestamps = false;

    protected $fillable = [
        "symbolic",
        "assoc_type",
        "assoc_id"
    ];

    public function assoc(): BelongsTo
    {
        return $this->belongsTo(Publication::class, 'assoc_id', 'publication_id');
    }
}
