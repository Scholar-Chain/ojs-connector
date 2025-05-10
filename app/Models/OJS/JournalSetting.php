<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalSetting extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = null;
    public $timestamps = false;

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }
}
