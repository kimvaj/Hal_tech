<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    public function format()
    {
        return [
            'id' => $this->id,
            'quotation_number' => $this->quotation_number,
            'quotation_name' => $this->quotation_name,
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

    public function quotation_detail()
    {
        return $this->hasMany(Quotation::class);
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
