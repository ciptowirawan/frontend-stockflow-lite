@extends('layouts.form')

@section('title', 'Create Sale')

@section('content')
<section class="landing-page">
    <div class="container my-2">
        <div class="landing-navigation">
            <a href="{{ route('manage.sales.index') }}"><i class="fa-solid fa-arrow-left fa-xl"></i></a>
            <b>Tambahkan Penjualan</b>
        </div>
    </div>

    <div class="card form-content rounded-5">
        <div class="card-body">
            <div class="container my-3">

            <form action="{{ route('manage.sales.store') }}" method="POST" id="sale-form">
                @csrf

                <div class="mb-3">
                    <label>Customer</label>
                    <select name="customer_id" id="customer_id" class="form-select" required>
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Tanggal Order</label>
                    <input type="date" name="order_date" id="order_date" class="form-control" required>
                </div>

                <hr class="mt-4 mb-3">
                        
                <div class="mb-3">
                    <label>Kategori</label>
                    <select id="category_id" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nama Barang</label>
                    <select id="product_id" class="form-select">
                        <option value="">-- Pilih Produk --</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Quantity</label>
                            <input type="number" id="qty" class="form-control" min="1" value="1">
                        </div>
                        
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Unit</label>
                            <input type="text" id="unit_display" class="form-control" readonly>
                        </div>
                    </div>
                        
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Harga Jual</label>
                            <input type="text" id="price_display" class="form-control" readonly>
                            <input type="hidden" id="price_value">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-2 mb-4">
                    <button type="button" id="btn-add-line" class="btn btn-primary px-4">Post Item</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="summary-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Grand Total</th>
                                <th id="grand-total-display">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <input type="hidden" name="grand_total" id="grand_total" value="0">
                <div id="products-container"></div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-success" id="btn-open-modal" disabled>Submit Penjualan</button>
                    <a href="{{ route('manage.sales.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>

            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Konfirmasi Penjualan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row g-4">
                <div class="col-8">
                    <table class="table table-sm table-bordered" id="confirm-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="col-4">
                    <div class="mb-2">
                        <strong>Grand Total:</strong>
                        <div id="confirm-grand-total" class="fw-bold">Rp 0</div>
                    </div>
                    <div class="mb-3">
                        <label>Paid Amount</label>
                        <input type="text" id="paid_amount" name="paid_amount" class="form-control">
                    </div>
                    <div class="mb-2">
                        <strong>Kembalian:</strong>
                        <div id="kembalian-display">Rp 0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn-confirm-submit" class="btn btn-success">Konfirmasi Penjualan</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
    </div>
  </div>
</div>
@endsection

@push('body-scripts')
<script src="{{ asset('js/price/form.js') }}"></script>
<script>
    function formatRupiah(value) {
        if (!value) return '';
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value));
    }
    function parseDigits(str) {
        return Number(String(str || '').replace(/[^0-9]/g, '')) || 0;
    }

    const state = {
        lines: [],
        customerName: '',
        orderDate: '',
        grandTotal: 0
    };

    const customerSelect = document.getElementById('customer_id');
    const orderDateInput = document.getElementById('order_date');
    const categorySelect  = document.getElementById('category_id');
    const productSelect   = document.getElementById('product_id');
    const qtyInput        = document.getElementById('qty');
    const unitDisplay    = document.getElementById('unit_display');
    const priceDisplay    = document.getElementById('price_display');
    const priceValue      = document.getElementById('price_value');

    const summaryTableBody = document.querySelector('#summary-table tbody');
    const grandTotalDisplay = document.getElementById('grand-total-display');
    const grandTotalHidden  = document.getElementById('grand_total');
    const productsContainer = document.getElementById('products-container');

    const btnAddLine     = document.getElementById('btn-add-line');
    const btnOpenModal   = document.getElementById('btn-open-modal');
    const confirmModalEl = document.getElementById('confirmModal');
    const confirmTableBody = document.querySelector('#confirm-table tbody');
    const confirmGrandTotal = document.getElementById('confirm-grand-total');
    const kembalianDisplay = document.getElementById('kembalian-display');
    const btnConfirmSubmit = document.getElementById('btn-confirm-submit');

    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        productSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
        unitDisplay.value = '';
        priceDisplay.value = '';
        priceValue.value = '';

        if (!categoryId) return;

        fetch(`/manage/products/category/${categoryId}`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(products => {
                products.forEach(p => {
                    productSelect.innerHTML += `<option value="${p.id}" data-name="${p.name}">${p.name}</option>`;
                });
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal memuat produk kategori', 'error');
            });
    });

    productSelect.addEventListener('change', function() {
        const productId = this.value;
        priceDisplay.value = '';
        priceValue.value = '';
        unitDisplay.value = '';

        if (!productId) return;

        fetch(`/manage/products/${productId}`) 
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(p => {
                const price = Number(p.sale_price || p.price || 0); 
                priceValue.value = price;
                unitDisplay.value = p.unit;
                priceDisplay.value = formatRupiah(price);
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal memuat harga produk', 'error');
            });
    });

    btnAddLine.addEventListener('click', function() {

        const productId = productSelect.value;
        const productName = productSelect.options[productSelect.selectedIndex]?.text || '';
        const unit = unitDisplay.value;
        const qty = Number(qtyInput.value || 0);
        const price = Number(priceValue.value || 0);

        if (!productId) {
            Swal.fire('Validasi', 'Silakan pilih produk', 'warning'); return;
        }
        if (qty < 1) {
            Swal.fire('Validasi', 'Quantity minimal 1', 'warning'); return;
        }
        if (price <= 0) {
            Swal.fire('Validasi', 'Harga jual produk tidak valid', 'warning'); return;
        }

        const existingLine = state.lines.find(line => line.product_id === productId);

        if (existingLine) {
            existingLine.qty += qty;
            existingLine.price = price; 
        } else {
            state.lines.push({ product_id: productId, product_name: productName, qty, unit, price });
        }

        renderSummary();
        resetLineInputs();
        btnOpenModal.disabled = state.lines.length === 0;
    });

    function resetLineInputs() {
        productSelect.value = '';
        qtyInput.value = 1;
        unitDisplay.value = '';
        priceDisplay.value = '';
        priceValue.value = '';
    }

    function renderSummary() {
        summaryTableBody.innerHTML = '';
        let total = 0;

        productsContainer.innerHTML = '';
        
        state.lines.forEach((line, idx) => {
            const lineTotal = line.qty * line.price;
            total += lineTotal;

            summaryTableBody.innerHTML += `
                <tr>
                    <td>${idx + 1}</td>
                    <td>${line.product_name}</td>
                    <td>${line.qty}</td>
                    <td>${line.unit}</td>
                    <td>${formatRupiah(line.price)}</td>
                    <td>${formatRupiah(lineTotal)}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" data-index="${idx}" onclick="deleteLine(${idx})">Hapus</button>
                    </td>
                </tr>
            `;
            
            productsContainer.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="products[${idx}][product_id]" value="${line.product_id}">
                <input type="hidden" name="products[${idx}][qty]" value="${line.qty}">
                <input type="hidden" name="products[${idx}][price]" value="${line.price}">
                <input type="hidden" name="products[${idx}][sub_total]" value="${lineTotal}">
            `);
        });

        state.grandTotal = total;
        grandTotalDisplay.textContent = formatRupiah(total);
        grandTotalHidden.value = total;
    }

    window.deleteLine = function(index) {
        state.lines.splice(index, 1);
        renderSummary();
        btnOpenModal.disabled = state.lines.length === 0;
    }

    btnOpenModal.addEventListener('click', function() {
        if (state.lines.length === 0) {
            Swal.fire('Validasi', 'Belum ada item di summary', 'warning'); return;
        }
        
        if (!customerSelect.value || !orderDateInput.value) {
            Swal.fire('Validasi', 'Mohon lengkapi data Customer dan Tanggal Order.', 'warning');
            return;
        }

        confirmTableBody.innerHTML = '';
        state.lines.forEach((line, idx) => {
            const lineTotal = line.qty * line.price;
            confirmTableBody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${idx + 1}</td>
                    <td>${line.product_name}</td>
                    <td>${line.qty}</td>
                    <td>${line.unit}</td>
                    <td>${formatRupiah(line.price)}</td>
                    <td>${formatRupiah(lineTotal)}</td>
                </tr>
            `);
        });

        confirmGrandTotal.textContent = formatRupiah(state.grandTotal);
        kembalianDisplay.textContent = formatRupiah(0);
        document.getElementById('paid_amount').value = '';

        const modal = bootstrap.Modal.getOrCreateInstance(confirmModalEl);
        modal.show();
    });

    document.getElementById('paid_amount').addEventListener('input', function() {
        const paid = parseDigits(this.value);
        const change = Math.max(paid - state.grandTotal, 0);
        kembalianDisplay.textContent = formatRupiah(change);
    });

    btnConfirmSubmit.addEventListener('click', function() {
        const paidEl = document.getElementById('paid_amount');
        const rawPaid = parseDigits(paidEl.value);
        
        if (rawPaid < state.grandTotal) {
            Swal.fire('Pembayaran Kurang', 'Jumlah bayar kurang dari Grand Total.', 'warning');
            return;
        }

        const hiddenPaid = document.createElement('input');
        hiddenPaid.type = 'hidden';
        hiddenPaid.name = 'paid_amount';
        hiddenPaid.value = rawPaid;
        document.getElementById('sale-form').appendChild(hiddenPaid);


        const modal = bootstrap.Modal.getOrCreateInstance(confirmModalEl);
        modal.hide();
        document.getElementById('sale-form').submit();
    });
    
    attachRupiahFormatter('#paid_amount'); 
</script>
@endpush