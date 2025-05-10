<?php

namespace App\Models\OJS;

use App\Traits\FieldsType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use FieldsType;

    const OJS_THUMBNAIL_SETTING_NAME = 'journalThumbnail';

    protected $connection = 'ojs';
    protected $primaryKey = 'journal_id';
    public $timestamps = false;

    protected $appends = [
        'thumbnail_url'
    ];

    public function getThumbnailUrlAttribute(): string | null
    {
        $setting = $this->journalSettings()
            ->where('setting_name', self::OJS_THUMBNAIL_SETTING_NAME)
            ->first();

        if (is_null($setting)) return null;

        $value = json_decode($setting->setting_value, true);

        return config('ojs.url') . "/public/journals/" . $this->journal_id . "/" . $value['uploadName'];
    }

    public function journalSettings(): HasMany
    {
        return $this->hasMany(JournalSetting::class, 'journal_id');
    }
}
