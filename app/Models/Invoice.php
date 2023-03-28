<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public function format()
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'invoice_name' => $this->invoice_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'qty' => $this->qty,
            'total' => $this->total,
            'note' => $this->note,
            'countDetail' => 20,
            'currency' => null,
            'company' => null,
            'user' => null,
            'created_at' => $this->created_at,
            'update_at' => $this->update_at,
        ];
    }

    public function invoice_detail()
    {
        return $this->hasMany(Invoice::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
