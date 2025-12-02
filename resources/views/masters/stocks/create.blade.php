@extends('layouts.form')

@section('title', 'Create Stock')

@section('content')
<section class="landing-page">
    <div class="container my-2">
        <div class="landing-navigation">
            <a href="{{ route('manage.stock-details.index') }}"><i class="fa-solid fa-arrow-left fa-xl"></i></a>
            <b>Tambahkan Stock</b>
        </div>
    </div>

    <div class="card form-content rounded-5">
        <div class="card-body">
            <div class="container my-3">

            <form action="{{ route('manage.stock-details.store') }}" method="POST" id="form-submit">
                @csrf

                <div class="mb-3">
                    <label>Kategori</label>
                    <select id="category" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nama Barang</label>
                    <select name="product_id" id="product" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Quantity</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Type</label>
                    <select name="type" class="form-select" required>
                        <option value="P_RET">P_RET (Purchase Return)</option>
                        <option value="S_RET">S_RET (Sales Return)</option>
                        <option value="ADJ">ADJ (Adjustment)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('manage.stock-details.index') }}" class="btn btn-secondary">Batal</a>
            </form>

            </div>
        </div>
    </div>
</section>
@endsection

@push('body-scripts')
<script>
    document.getElementById('category').addEventListener('change', function() {
        const categoryId = this.value;
        if (!categoryId) return;

        fetch(`/manage/products/category/${categoryId}`)
            .then(res => res.json())
            .then(products => {
                const productSelect = document.getElementById('product');
                productSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
                products.forEach(p => {
                    productSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal memuat produk kategori', 'error');
            });
    });
</script>
@endpush
