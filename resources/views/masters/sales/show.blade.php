@extends('layouts.form')

@section('title', 'Detail Penjualan')

@section('content')
<section class="landing-page">
    <div class="container my-2">
        <div class="landing-navigation">
            <a href="{{ route('manage.sales.index') }}"><i class="fa-solid fa-arrow-left fa-xl"></i></a>
            <b>Detail Penjualan #{{ $sale->id }}</b>
        </div>
    </div>

    <div class="card form-content rounded-5">
        <div class="card-body">
            <div class="container my-3">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Customer</label>
                            <input type="text" class="form-control" value="{{ $sale->customer->name ?? 'N/A' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Tanggal Order</label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d', strtotime($sale->order_date)) }}" readonly>
                        </div>
                    </div>
                </div>

                <hr class="mt-2 mb-3">
                        
                <h5 class="mb-3">Item Penjualan</h5>

                <div class="table-responsive">
                    <table class="table table-bordered" id="summary-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Harga Jual</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach($sale->details as $item)
                            <tr>
                                <td>{{ $i++ }}</td>
                                {{-- Mengakses nama produk melalui relasi product --}}
                                <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->product->unit ?? 'Unit' }}</td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Grand Total</th>
                                <th id="grand-total-display">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <hr class="mb-2">
                
                <div class="row justify-content-end">
                    <div class="col-md-6 col-lg-4">
                        <div class="mb-2">
                            <label>Paid Amount</label>
                            <input type="text" class="form-control" 
                                value="Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-md-6 col-lg-4">
                        <div class="mb-2">
                            <label>Kembalian</label>
                            @php
                                $kembalian = $sale->paid_amount - $sale->grand_total;
                            @endphp
                            <input type="text" class="form-control fw-bold text-success" 
                                value="Rp {{ number_format($kembalian, 0, ',', '.') }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('manage.sales.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection