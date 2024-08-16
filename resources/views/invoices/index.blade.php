@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Invoices</h1>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-4">
            <label for="currency" class="form-label">Currency</label>
            <select id="currency" class="form-select">
                <option value="" selected>All</option>
                <!-- Additional currencies will be populated dynamically -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="period" class="form-label">Time Period</label>
            <select id="period" class="form-select">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="this_week">This Week</option>
                <option value="last_week">Last Week</option>
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_year">This Year</option>
                <option value="last_year">Last Year</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button id="filterBtn" class="btn btn-primary w-100">Filter</button>
        </div>
    </div>

    <!-- Invoice Display Section -->
    <div id="invoiceDisplay" class="row">
        <!-- Invoice data will be displayed here dynamically -->
    </div>
</div>

<!-- Axios and Script for handling filters -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.getElementById('filterBtn');
    const invoiceDisplay = document.getElementById('invoiceDisplay');

    filterBtn.addEventListener('click', function () {
        const currency = document.getElementById('currency').value;
        const period = document.getElementById('period').value;

        console.log(`Currency selected: ${currency}`);
        console.log(`Period selected: ${period}`);

        axios.get(`/api/invoices?currency=${currency}&period=${period}`)
            .then(response => {
                invoiceDisplay.innerHTML = '';

                if (Object.keys(response.data).length === 0) {
                    invoiceDisplay.innerHTML = '<p class="text-center">No invoices found for the selected filters.</p>';
                } else {
                    // Display the entire JSON object as a string
                    const pre = document.createElement('pre');
                    pre.textContent = JSON.stringify(response.data, null, 2);  // Convert the JSON object to a string
                    invoiceDisplay.appendChild(pre);
                }
            })
            .catch(error => {
                console.error('Error fetching invoices:', error);
                invoiceDisplay.innerHTML = '<p class="text-center text-danger">Error fetching invoices. Please try again later.</p>';
            });
    });
});


</script>
@endsection
