<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BillingData extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'taxpayer_type', 'ruc', 'ruc_dv', 'business_name',
        'trade_name', 'fiscal_address', 'city', 'department', 'email',
        'phone', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($billing) {
            if ($billing->is_default) {
                static::where('user_id', $billing->user_id)
                    ->where('id', '!=', $billing->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
