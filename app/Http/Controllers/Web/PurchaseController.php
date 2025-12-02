<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Api\PurchaseController as ApiPurchaseController;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends ApiPurchaseController
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
        $purchases = collect($data['data'] ?? [])->map(function ($item) {
            return (object) $item;
        });

        return view('masters.purchases.index', [
            'purchases' => $purchases
        ]);
    }

    public function store(PurchaseRequest $request)
    {
        $response = parent::store($request);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Validation failed');
            return redirect()->back()->withInput();
        }

        Alert::success('Success', 'Purchase created successfully');
        return redirect()->route('purchases.index');
    }

    public function show(Purchase $purchase)
    {
        $response = parent::show($purchase);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->getData(true);
        $purchaseObject = (object) ($data['data'] ?? []);

        return view('masters.purchases.show', [
            'purchase' => $purchaseObject
        ]);
    }

    public function edit(Purchase $purchase)
    {
        $response = parent::show($purchase);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->getData(true);
        $purchaseObject = (object) ($data['data'] ?? []);

        return view('masters.purchases.edit', [
            'purchase' => $purchaseObject
        ]);
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        $response = parent::update($request, $purchase);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Update failed');
            return redirect()->back()->withInput();
        }

        Alert::success('Success', 'Purchase updated successfully');
        return redirect()->route('purchases.index');
    }

    public function destroy(Purchase $purchase)
    {
        $response = parent::destroy($purchase);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Delete failed');
            return redirect()->back();
        }

        Alert::success('Success', 'Purchase deleted successfully');
        return redirect()->route('purchases.index');
    }
}
