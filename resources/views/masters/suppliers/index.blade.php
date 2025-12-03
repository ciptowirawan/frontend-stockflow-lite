@extends('layouts.index')

@section('title', 'Supplier')

@section('content')
<div class="container">
    <h1 class="mb-2">Supplier</h1>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-primary mb-3 w-25" onclick="openModal()">
            + Tambah Supplier
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
            @forelse($suppliers as $index => $supplier)
            <tr>
                <td>{{ $startNumber + $index + 1 }}</td>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->address }}</td>
                <td>{{ $supplier->phone }}</td>
                <td>{{ $supplier->remarks ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($supplier->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td>{{ \Carbon\Carbon::parse($supplier->updated_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td width="150px">
                    <button class="btn btn-warning btn-sm" data-supplier-data='@json($supplier)' onclick="openModal(this)">Edit</button>
                    <form action="{{ route('manage.suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline delete-form" data-supplier-name="{{ $supplier->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                    </form>
                </td>
            </tr> 
            @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada supplier yang terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between mt-3">
        @if($pagination->prev ?? false)
            <a href="{{ route('manage.suppliers.index', ['page' => request('page', 1) - 1]) }}" 
            class="btn btn-outline-primary">Previous</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Previous</button>
        @endif

        @if($pagination->next ?? false)
            <a href="{{ route('manage.suppliers.index', ['page' => request('page', 1) + 1]) }}" 
            class="btn btn-outline-primary">Next</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Next</button>
        @endif
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="supplierModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="supplierForm">
            @csrf
            <input type="hidden" name="_method" id="_method" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Supplier</h5>
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
        const form = document.getElementById('supplierForm');
        const methodInput = document.getElementById('_method');
        const modalElement = document.getElementById('supplierModal');
        const modalTitle = modalElement.querySelector('.modal-title'); 
        
        form.reset();
        form.action = '/manage/suppliers';
        methodInput.value = 'POST';
        modalTitle.textContent = 'Tambah Supplier';

        let supplierData = null;

        if (buttonElement && buttonElement.hasAttribute('data-supplier-data')) {
            const jsonString = buttonElement.getAttribute('data-supplier-data');
            
            try {
                supplierData = JSON.parse(jsonString);
            } catch (e) {
                console.error("Gagal memparsing data supplier:", e);
                alert('Gagal memuat data supplier. Silakan cek konsol.');
                return;
            }
        }

        if (supplierData) {
            form.action = `/manage/suppliers/${supplierData.id}`;
            methodInput.value = 'PUT';
        
            document.getElementById('name').value = supplierData.name; 
            document.getElementById('address').value = supplierData.address; 
            document.getElementById('phone').value = supplierData.phone; 
            document.getElementById('remarks').value = supplierData.remarks ?? ''; 
            
            modalTitle.textContent = 'Edit Supplier'; 
        }
        
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {            
            const form = this.closest('form');
            const supplierName = form.dataset.supplierName; 
            Swal.fire({
                title:`Hapus supplier ${supplierName}?`,
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
