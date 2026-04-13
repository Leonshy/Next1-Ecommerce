<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'label', 'recipient_name', 'phone', 'country',
        'department', 'city', 'neighborhood', 'street_address',
        'cross_street_1', 'house_number', 'reference', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        // Equivalente al trigger ensure_single_default_address
        static::saving(function ($address) {
            if ($address->is_default) {
                static::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
