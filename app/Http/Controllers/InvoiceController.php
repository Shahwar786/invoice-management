<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function getInvoicesByCurrency()
    {
        $currencies = Invoice::select('currency')->distinct()->get();

        $result = [];

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

        return view('invoices.index', compact('result'));
    }


    public function fetchInvoicesByFilters(Request $request)
    {
        $currency = $request->input('currency');
        $period = $request->input('period');
        $amountMin = $request->input('amount_min');
        $amountMax = $request->input('amount_max');

        \Log::debug('Currency: ' . $currency);
        \Log::debug('Period: ' . $period);
        \Log::debug('Amount Min: ' . $amountMin);
        \Log::debug('Amount Max: ' . $amountMax);

        try {
            $dateRange = $this->getDateRangeForPeriod($period);
        } catch (\Exception $e) {
            \Log::error('Error getting date range: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid period'], 400);
        }

        $query = Invoice::whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

        if ($currency) {
            $query->where('currency', $currency);
        }

        if ($amountMin) {
            $query->where('amount', '>=', $amountMin);
        }

        if ($amountMax) {
            $query->where('amount', '<=', $amountMax);
        }

        $invoices = $query->get(['currency', 'id as invoice_id', 'amount', 'created_at']);

        $groupedInvoices = [];

        foreach ($invoices as $invoice) {
            $invoicePeriod = $this->determinePeriod($invoice->created_at, $period);
            if ($invoicePeriod) {
                $groupedInvoices[$invoice->currency][$invoicePeriod][] = [
                    'invoice_id' => $invoice->invoice_id,
                    'amount' => $invoice->amount,
                    'date' => $invoice->created_at->format('Y-m-d'),
                ];
            }
        }

        return response()->json($groupedInvoices);
    }

    private function determinePeriod($date, $selectedPeriod)
    {
        switch ($selectedPeriod) {
            case 'today':
                return Carbon::today()->isSameDay($date) ? 'today' : null;
            case 'yesterday':
                return Carbon::yesterday()->isSameDay($date) ? 'yesterday' : null;
            case 'this_week':
                return Carbon::now()->startOfWeek()->lte($date) && Carbon::now()->endOfWeek()->gte($date) ? 'this_week' : null;
            case 'last_week':
                return Carbon::now()->subWeek()->startOfWeek()->lte($date) && Carbon::now()->subWeek()->endOfWeek()->gte($date) ? 'last_week' : null;
            case 'this_month':
                return Carbon::now()->startOfMonth()->lte($date) && Carbon::now()->endOfMonth()->gte($date) ? 'this_month' : null;
            case 'last_month':
                return Carbon::now()->subMonth()->startOfMonth()->lte($date) && Carbon::now()->subMonth()->endOfMonth()->gte($date) ? 'last_month' : null;
            case 'this_year':
                return Carbon::now()->startOfYear()->lte($date) && Carbon::now()->endOfYear()->gte($date) ? 'this_year' : null;
            case 'last_year':
                return Carbon::now()->subYear()->startOfYear()->lte($date) && Carbon::now()->subYear()->endOfYear()->gte($date) ? 'last_year' : null;
            default:
                return null;
        }
    }

    public function getCurrencies()
    {
        return response()->json(Invoice::select('currency')->distinct()->pluck('currency'));
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
