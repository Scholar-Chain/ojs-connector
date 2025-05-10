<?php

namespace App\Models\OJS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Author extends Model
{
    protected $connection = 'ojs';
    protected $primaryKey = 'author_id';
    public $timestamps = false;

    protected $fillable = [
        "email",
        "include_in_browse",
        "publication_id",
        "user_group_id"
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class, 'publication_id', 'publication_id');
    }

    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id', 'user_group_id');
    }

    public function authorSetting(): HasOne
    {
        return $this->hasOne(AuthorSetting::class, 'author_id', 'author_id');
    }
}
