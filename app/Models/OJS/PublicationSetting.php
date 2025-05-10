<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationSetting extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        "publication_id",
        "locale",
        "setting_name",
        "setting_value"
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class, 'publication_id', 'section_id');
    }
}
