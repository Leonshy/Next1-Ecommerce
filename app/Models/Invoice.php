<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'invoice_number', 'order_id', 'user_id', 'taxpayer_type', 'ruc', 'ruc_dv',
        'business_name', 'trade_name', 'fiscal_address', 'billing_city',
        'billing_department', 'billing_email', 'billing_phone',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'shipping_city',
        'subtotal', 'discount', 'shipping_cost', 'total',
        'items', 'status', 'issued_at',
    ];

    protected $casts = [
        'items'        => 'array',
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total'        => 'decimal:2',
        'issued_at'    => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('issued_at', $year)->count() + 1;
        return 'FAC-' . $year . str_pad($last, 8, '0', STR_PAD_LEFT);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
