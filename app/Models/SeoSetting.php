<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'seo_settings';

    protected $fillable = ['page_key', 'meta_title', 'meta_description', 'keywords', 'canonical_url', 'og_image'];

    public static function forPage(string $pageKey): ?self
    {
        return static::where('page_key', $pageKey)->first()
            ?? static::where('page_key', 'global')->first();
    }
}
