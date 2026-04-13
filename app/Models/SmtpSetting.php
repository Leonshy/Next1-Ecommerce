<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'smtp_settings';

    protected $fillable = ['host', 'port', 'username', 'password', 'encryption', 'from_email', 'from_name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected $hidden = ['password'];
}
