@extends('layouts.index')

@section('title', 'Product')

@section('content')
<div class="container">
    <h1 class="mb-2">Product</h1>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-primary mb-3 w-25" onclick="openModal()">
            + Tambah Product
        </button>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Unit</th>
                <th>Harga</th>
                <th>Kategori</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ ucfirst(strtolower($product->name)) }}</td>
                <td>{{ $product->unit }}</td>
                <td>{{ number_format($product->price, 0, ',', '.') }}</td>
                <td>{{ ucfirst(strtolower($product->category->name ?? "-")) }}</td>
                <td>{{ $product->created_by->name ?? '-' }}</td>
                <td>{{ $product->updated_by->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($product->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td>{{ \Carbon\Carbon::parse($product->updated_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td width="150px">
                    <button class="btn btn-warning btn-sm" data-product-data='@json($product)' onclick="openModal(this)">Edit</button>
                    <form action="{{ route('manage.products.destroy', $product->id) }}" method="POST" class="d-inline delete-form" data-product-name="{{ $product->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                    </form>
                </td>
            </tr> 
            @empty
            <tr>
                <td colspan="10" class="text-center">Belum ada produk yang terdaftar.</td>
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

<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="productForm">
            @csrf
            <input type="hidden" name="_method" id="_method" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control uppercase" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label>Unit</label>
                        <input type="text" class="form-control uppercase" name="unit" id="unit" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="text" class="form-control" name="price" id="price" required>
                    </div>
                    <div class="mb-3">
                        <label>Kategori</label>
                        <select class="form-select" name="category_id" id="category_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
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
        const form = document.getElementById('productForm');
        const methodInput = document.getElementById('_method');
        const modalElement = document.getElementById('productModal');
        const modalTitle = modalElement.querySelector('.modal-title'); 
        
        form.reset();
        form.action = '/manage/products';
        methodInput.value = 'POST';
        modalTitle.textContent = 'Tambah Product';

        let productData = null;

        if (buttonElement && buttonElement.hasAttribute('data-product-data')) {
            const jsonString = buttonElement.getAttribute('data-product-data');
            
            try {
                productData = JSON.parse(jsonString);
            } catch (e) {
                console.error("Gagal memparsing data product:", e);
                alert('Gagal memuat data product. Silakan cek konsol.');
                return;
            }
        }

        if (productData) {
            form.action = `/manage/products/${productData.id}`;
            methodInput.value = 'PUT';
        
            document.getElementById('name').value = productData.name; 
            document.getElementById('unit').value = productData.unit; 
            document.getElementById('price').value = productData.price; 
            document.getElementById('category_id').value = productData.category?.id ?? '';
            
            modalTitle.textContent = 'Edit Product'; 
        }
        
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {            
            const form = this.closest('form');
            const productName = form.dataset.productName; 
            Swal.fire({
                title:`Hapus product ${productName}?`,
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

<script src="{{ asset('js/price/form.js') }}"></script>

<script>
    attachRupiahFormatter('#price');
</script>
@endpush
