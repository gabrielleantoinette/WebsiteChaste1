@extends('layouts.admin')

@section('content')
    <div class="flex justify-between mb-5">
        <h1 class="text-xl font-bold mb-5">Invoices List</h1>
        <a href="{{ url('/admin/invoices/create-customer') }}" class="btn btn-primary">Create</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <td>ID</td>
                <td>Code</td>
                <td>Customer</td>
                <td>Employee</td>
                <td>Driver</td>
                <td>Accountant</td>
                <td>Grand Total</td>
                <td>Status</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->code }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>{{ $invoice->employee->name }}</td>
                    <td>{{ $invoice->driver ? $invoice->driver->name : '-' }}</td>
                    <td>{{ $invoice->accountant ? $invoice->accountant->name : '-' }}</td>
                    <td>{{ $invoice->grand_total }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>
                        <a href="{{ url('/admin/invoices/detail/' . $invoice->id) }}" class="btn btn-sm btn-primary">
                            Detail
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
