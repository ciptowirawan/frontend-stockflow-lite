@extends('layouts.index')

@section('title', 'Stock')

@section('content')
<div class="container">
    <h1 class="mb-2">Stock</h1>

    <div class="d-flex justify-content-end">
        <a href="{{ route('manage.stock-details.create') }}" class="btn btn-primary mb-3 w-25">
            + Tambah Stock
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Stock After</th>
                <th>Type</th>
                <th>Created By</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $index => $stock)
            <tr>
                <td>{{ $startNumber + $index + 1 }}</td>
                <td>{{ ucfirst(strtolower($stock->product->name)) ?? '-' }}</td>
                <td>{{ $stock->quantity }}</td>
                <td>{{ $stock->stock_after }}</td>
                <td>{{ $stock->type }}</td>
                <td>{{ $stock->created_by->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($stock->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada stock yang tercatat.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between mt-3">
        @if($pagination->prev ?? false)
            <a href="{{ route('manage.stock-details.index', ['page' => request('page', 1) - 1]) }}" 
            class="btn btn-outline-primary">Previous</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Previous</button>
        @endif

        @if($pagination->next ?? false)
            <a href="{{ route('manage.stock-details.index', ['page' => request('page', 1) + 1]) }}" 
            class="btn btn-outline-primary">Next</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Next</button>
        @endif
    </div>
</div>
@endsection
