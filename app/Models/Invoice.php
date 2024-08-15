<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 
        'currency', 
        'amount', 
        'invoice_date', 
        'created_at', 
        'updated_at'
    ];

    /**
     * Scope a query to only include invoices for a given date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $start
     * @param  string  $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope a query to group invoices by currency.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupByCurrency($query)
    {
        return $query->select('currency', 'id as invoice_id', 'amount', 'invoice_date')
                     ->groupBy('currency', 'invoice_id', 'amount', 'invoice_date');
    }
}
