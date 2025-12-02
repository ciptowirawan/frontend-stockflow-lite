@extends('layouts.index')

@section('title', 'Sales')

@section('content')
<div class="container">
    <h1 class="mb-2">Sales</h1>

    <div class="d-flex justify-content-end">
        <a href="{{ route('manage.sales.create') }}" class="btn btn-primary mb-3 w-25">
            + Tambah Sale
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Grand Total</th>
                <th>Paid Amount</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $index => $sale)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->order_date)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s')}}</td>
                <td>{{ $sale->customer->name ?? '-' }}</td>
                <td>{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                <td>{{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                <td>{{ $sale->created_by->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td width="150px">
                    <a href="{{ route('manage.sales.show', $sale->id) }}" class="btn btn-primary btn-sm mr-1">View</a>         
                    <form action="{{ route('manage.sales.destroy', $sale->id) }}" method="POST" class="d-inline delete-form" data-sale-name="{{ $sale->customer->name ?? 'Sale #'.$sale->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete">Batalkan</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada sales yang tercatat.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between mt-3">
        @if($pagination->prev ?? false)
            <a href="{{ $pagination->prev }}" class="btn btn-outline-primary">Previous</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Previous</button>
        @endif

        @if($pagination->next ?? false)
            <a href="{{ $pagination->next }}" class="btn btn-outline-primary">Next</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Next</button>
        @endif
    </div>
</div>
@endsection

@push('body-scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            const saleName = form.dataset.saleName;
            Swal.fire({
                title:`Batalkan sale ${saleName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
