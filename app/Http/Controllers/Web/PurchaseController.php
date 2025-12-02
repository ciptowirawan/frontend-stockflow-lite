<?php

namespace App\Http\Controllers\Web;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\PurchaseRequest;
use RealRashid\SweetAlert\Facades\Alert;

class PurchaseController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = session('access_token');
    }

    public function index(Request $request)
    {
        $purchasesResponse = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/purchases', $request->all());

        if ($purchasesResponse->failed()) {
            Alert::error('Error', $purchaseResponse->json('message') ?? 'Something went wrong');
            return redirect()->back();
        }

        $purchasesData = $purchasesResponse->json();
        $purchases = collect($purchasesData['data'] ?? [])->map(function($item) {
            return json_decode(json_encode($item));
        });

        $pagination = (object) ($purchaseData['links'] ?? []);

        return view('masters.purchases.index', compact('purchases', 'pagination'));
    }

    public function show($id)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url')."/purchases/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->json();
        $purchase = json_decode(json_encode($data['data'] ?? []));

        return view('masters.purchases.show', compact('purchase'));
    }

    public function create()
    {
        $categoriesResponse = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/categories', [
                'per_page' => 'all'
            ]);

        if ($categoriesResponse->failed()) {
            Alert::error('Error', $categoriesResponse->json('message') ?? 'Failed to load categories.');
            return redirect()->back();
        }

        $categoriesData = $categoriesResponse->json();
        $categories = collect($categoriesData['data'] ?? [])->map(fn($item) => json_decode(json_encode($item)));

        $suppliersResponse = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/suppliers', [
                'per_page' => 'all'
            ]);

        if ($suppliersResponse->failed()) {
            Alert::error('Error', $suppliersResponse->json('message') ?? 'Failed to load suppliers.');
            return redirect()->back();
        }

        $suppliersData = $suppliersResponse->json();
        $suppliers = collect($suppliersData['data'] ?? [])->map(fn($item) => json_decode(json_encode($item)));


        return view('masters.purchases.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $response = Http::withToken($this->token)
            ->post(config('services.api.base_url').'/purchases', $request->all());

        if ($response->failed()) {
            $data = $response->json();

            $errorMessage = null;
            if (isset($data['errors']) && is_array($data['errors'])) {
                $allErrors = collect($data['errors'])->flatten();
                $errorMessage = $allErrors->first();
            }

            if (!$errorMessage) {
                $errorMessage = $data['message'] ?? 'Validation failed.';
            }

            Alert::error('Error', $errorMessage);
            return redirect()->back()->withInput();
        }

        Alert::success('Success', 'Purchase created successfully');
        return redirect()->route('manage.purchases.index');
    }

    public function destroy($id)
    {
        $response = Http::withToken($this->token)
            ->delete(config('services.api.base_url')."/purchases/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Delete failed');
            return redirect()->back();
        }

        Alert::success('Success', 'Purchase voided successfully');
        return redirect()->route('manage.purchases.index');
    }
}
