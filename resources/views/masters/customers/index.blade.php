@extends('layouts.index')

@section('title', 'Customer')

@section('content')
<div class="container">
    <h1 class="mb-2">Customer</h1>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-primary mb-3 w-25" onclick="openModal()">
            + Tambah Customer
        </button>
    </div>

    {{-- Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Keterangan</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $index => $customer)
            <tr>
                <td>{{ $startNumber + $index + 1 }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->remarks ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($customer->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td>{{ \Carbon\Carbon::parse($customer->updated_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td width="150px">
                    <button class="btn btn-warning btn-sm" data-customer-data='@json($customer)' onclick="openModal(this)">Edit</button>
                    <form action="{{ route('manage.customers.destroy', $customer->id) }}" method="POST" class="d-inline delete-form" data-customer-name="{{ $customer->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                    </form>
                </td>
            </tr> 
            @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada customer yang terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between mt-3">
        @if($pagination->prev ?? false)
            <a href="{{ route('manage.customers.index', ['page' => request('page', 1) - 1]) }}" 
            class="btn btn-outline-primary">Previous</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Previous</button>
        @endif

        @if($pagination->next ?? false)
            <a href="{{ route('manage.customers.index', ['page' => request('page', 1) + 1]) }}" 
            class="btn btn-outline-primary">Next</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Next</button>
        @endif
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="customerForm">
            @csrf
            <input type="hidden" name="_method" id="_method" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <input type="text" class="form-control" name="address" id="address" required>
                    </div>
                    <div class="mb-3">
                        <label>Telepon</label>
                        <input type="text" class="form-control" name="phone" id="phone" required>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <input type="text" class="form-control" name="remarks" id="remarks">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('body-scripts')
<script>
    function openModal(buttonElement = null) {
        const form = document.getElementById('customerForm');
        const methodInput = document.getElementById('_method');
        const modalElement = document.getElementById('customerModal');
        const modalTitle = modalElement.querySelector('.modal-title'); 
        
        form.reset();
        form.action = '/manage/customers';
        methodInput.value = 'POST';
        modalTitle.textContent = 'Tambah Customer';

        let customerData = null;

        if (buttonElement && buttonElement.hasAttribute('data-customer-data')) {
            const jsonString = buttonElement.getAttribute('data-customer-data');
            
            try {
                customerData = JSON.parse(jsonString);
            } catch (e) {
                console.error("Gagal memparsing data customer:", e);
                alert('Gagal memuat data customer. Silakan cek konsol.');
                return;
            }
        }

        if (customerData) {
            form.action = `/manage/customers/${customerData.id}`;
            methodInput.value = 'PUT';
        
            document.getElementById('name').value = customerData.name; 
            document.getElementById('address').value = customerData.address; 
            document.getElementById('phone').value = customerData.phone; 
            document.getElementById('remarks').value = customerData.remarks ?? ''; 
            
            modalTitle.textContent = 'Edit Customer'; 
        }
        
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {            
            const form = this.closest('form');
            const customerName = form.dataset.customerName; 
            Swal.fire({
                title:`Hapus customer ${customerName}?`,
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
