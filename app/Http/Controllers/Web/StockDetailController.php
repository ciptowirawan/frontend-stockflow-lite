<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Api\StockDetailController as ApiStockDetailController;
use App\Models\StockDetail;
use App\Http\Requests\StockDetailRequest;

class StockDetailController extends ApiStockDetailController
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
        $stockDetails = collect($data['data'] ?? [])->map(function ($item) {
            return (object) $item;
        });

        return view('master.stock_details.index', [
            'stockDetails' => $stockDetails
        ]);
    }

    public function store(StockDetailRequest $request)
    {
        $response = parent::store($request);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Validation failed');
            return redirect()->back()->withInput();
        }

        Alert::success('Success', 'Stock detail created successfully');
        return redirect()->route('stock_details.index');
    }

    public function show(StockDetail $stockDetail)
    {
        $response = parent::show($stockDetail);

        if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() >= 400) {
            $data = $response->getData(true);
            Alert::error('Error', $data['message'] ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->getData(true);
        $stockDetailObject = (object) ($data['data'] ?? []);

        return view('master.stock_details.show', [
            'stockDetail' => $stockDetailObject
        ]);
    }
}
