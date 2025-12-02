<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Api\SaleController as ApiSaleController;
use App\Models\Sale;
use App\Http\Requests\SaleRequest;

class SaleController extends ApiSaleController
{
    public function index(Request $request)
    {
        $response = parent::index($request);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Something went wrong');
            return redirect()->back();
        }

        $data = $response->getData(true);
        $sales = collect($data['data'] ?? [])->map(function ($item) {
            return (object) $item;
        });

        return view('masters.sales.index', [
            'sales' => $sales
        ]);
    }

    public function store(SaleRequest $request)
    {
        $response = parent::store($request);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Validation failed');
            return redirect()->back()->withInput();
        }

        Alert::success('Success', 'Sale created successfully');
        return redirect()->route('sales.index');
    }

    public function show(Sale $sale)
    {
        $response = parent::show($sale);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->getData(true);
        $saleObject = (object) ($data['data'] ?? []);

        return view('masters.sales.show', [
            'sale' => $saleObject
        ]);
    }

    public function edit(Sale $sale)
    {
        $response = parent::show($sale);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->getData(true);
        $saleObject = (object) ($data['data'] ?? []);

        return view('masters.sales.edit', [
            'sale' => $saleObject
        ]);
    }

    public function update(SaleRequest $request, Sale $sale)
    {
        $response = parent::update($request, $sale);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Update failed');
            return redirect()->back()->withInput();
        }

        Alert::success('Success', 'Sale updated successfully');
        return redirect()->route('sales.index');
    }

    public function destroy(Sale $sale)
    {
        $response = parent::destroy($sale);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Delete failed');
            return redirect()->back();
        }

        Alert::success('Success', 'Sale deleted successfully');
        return redirect()->route('sales.index');
    }
}
