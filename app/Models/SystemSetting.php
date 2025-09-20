<?php

namespace App\Models;

class SystemSetting extends Model
{
    protected static string $table = 'system_settings';
    protected static array $fillable = [
        'institution_name',
        'institution_address',
        'logo_path',
        'max_upload_size',
        'file_mime',
        'created_at',
        'updated_at',
    ];

    public static function current(): ?array
    {
        $settings = static::all(order: 'id DESC', limit: 1);
        return $settings[0] ?? null;
    }
}
