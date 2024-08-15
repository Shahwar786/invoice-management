<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function getInvoicesByCurrency()
    {
        // Fetch distinct currencies from invoices
        $currencies = Invoice::select('currency')->distinct()->get();

        // Initialize the result array
        $result = [];

        // Loop through each currency and fetch invoices for each time period
        foreach ($currencies as $currency) {
            $result[$currency->currency] = [
                'today' => $this->fetchInvoices($currency->currency, 'today'),
                'yesterday' => $this->fetchInvoices($currency->currency, 'yesterday'),
                'this_week' => $this->fetchInvoices($currency->currency, 'this_week'),
                'last_week' => $this->fetchInvoices($currency->currency, 'last_week'),
                'this_month' => $this->fetchInvoices($currency->currency, 'this_month'),
                'last_month' => $this->fetchInvoices($currency->currency, 'last_month'),
                'this_year' => $this->fetchInvoices($currency->currency, 'this_year'),
                'last_year' => $this->fetchInvoices($currency->currency, 'last_year'),
            ];
        }

        // Return the result as a JSON response
        return response()->json($result);
    }

    private function fetchInvoices($currency, $period)
    {
        // Get the date range for the specified period
        $dateRange = $this->getDateRangeForPeriod($period);

        // Fetch invoices within the specified date range for the given currency
        return Invoice::where('currency', $currency)
                      ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                      ->get(['id as invoice_id', 'amount', 'invoice_date']);
    }

    private function getDateRangeForPeriod($period)
    {
        // Determine the start and end dates for each time period
        switch ($period) {
            case 'today':
                return [
                    'start' => Carbon::today()->startOfDay(),
                    'end' => Carbon::today()->endOfDay()
                ];
            case 'yesterday':
                return [
                    'start' => Carbon::yesterday()->startOfDay(),
                    'end' => Carbon::yesterday()->endOfDay()
                ];
            case 'this_week':
                return [
                    'start' => Carbon::now()->startOfWeek(),
                    'end' => Carbon::now()->endOfWeek()
                ];
            case 'last_week':
                return [
                    'start' => Carbon::now()->subWeek()->startOfWeek(),
                    'end' => Carbon::now()->subWeek()->endOfWeek()
                ];
            case 'this_month':
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth()
                ];
            case 'last_month':
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth(),
                    'end' => Carbon::now()->subMonth()->endOfMonth()
                ];
            case 'this_year':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()->endOfYear()
                ];
            case 'last_year':
                return [
                    'start' => Carbon::now()->subYear()->startOfYear(),
                    'end' => Carbon::now()->subYear()->endOfYear()
                ];
            default:
                throw new \InvalidArgumentException("Invalid period: $period");
        }
    }
}
