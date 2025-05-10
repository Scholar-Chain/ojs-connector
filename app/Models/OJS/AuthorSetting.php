<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorSetting extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        "author_id",
        "locale",
        "setting_name",
        "setting_value"
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id', 'author_id');
    }
}
