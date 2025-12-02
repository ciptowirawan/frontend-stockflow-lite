@extends('layouts.index')

@section('title', 'Purchase')

@section('content')
<div class="container">
    <h1 class="mb-2">Purchase</h1>

    <div class="d-flex justify-content-end">
        <a href="{{ route('manage.purchases.create') }}" class="btn btn-primary mb-3 w-25">
            + Tambah Purchase
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Purchase Date</th>
                <th>Supplier</th>
                <th>Grand Total</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $index => $purchase)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->timezone('Asia/Jakarta')->translatedFormat('d F Y')}}</td>
                <td>{{ $purchase->supplier->name ?? '-' }}</td>
                <td>{{ number_format($purchase->grand_total, 0, ',', '.') }}</td>
                <td>{{ $purchase->created_by->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($purchase->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td width="150px">
                    <a href="{{ route('manage.purchases.show', $purchase->id) }}" class="btn btn-primary btn-sm">View</a>         
                    <form action="{{ route('manage.purchases.destroy', $purchase->id) }}" method="POST" class="d-inline delete-form" data-purchase-name="{{ $purchase->supplier->name ?? 'Purchase #'.$purchase->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete">Batalkan</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada purchase yang tercatat.</td>
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
            const purchaseName = form.dataset.purchaseName;
            Swal.fire({
                title:`Batalkan purchase ${purchaseName}?`,
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
