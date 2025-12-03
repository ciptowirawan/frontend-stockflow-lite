<?php

namespace App\Http\Controllers\Web;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Requests\SaleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;

class SaleController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = session('access_token');
    }

    public function index(Request $request)
    {
        $salesResponse = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/sales', $request->all());

        if ($salesResponse->failed()) {
            Alert::error('Error', $salesResponse->json('message') ?? 'Something went wrong');
            return redirect()->back();
        }

        $salesData = $salesResponse->json();
        $sales = collect($salesData['data'] ?? [])->map(function($item) {
            return json_decode(json_encode($item));
        });

        $pagination = (object) ($salesData['links'] ?? []);

        $meta = $salesData['meta'] ?? [];
        $perPage = $meta['per_page'] ?? 10;
        $currentPage = $request->query('page', 1);

        $startNumber = ($currentPage - 1) * $perPage;

        return view('masters.sales.index', compact('sales', 'pagination', 'startNumber'));
    }

    public function show($id)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url')."/sales/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->json();
        $sale = json_decode(json_encode($data['data'] ?? []));

        return view('masters.sales.show', compact('sale'));
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

        $customersResponse = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/customers', [
                'per_page' => 'all'
            ]);

        if ($customersResponse->failed()) {
            Alert::error('Error', $customersResponse->json('message') ?? 'Failed to load customers.');
            return redirect()->back();
        }

        $customersData = $customersResponse->json();
        $customers = collect($customersData['data'] ?? [])->map(fn($item) => json_decode(json_encode($item)));


        return view('masters.sales.create', compact('categories', 'customers'));
    }

    public function store(Request $request)
    {
        $response = Http::withToken($this->token)
            ->post(config('services.api.base_url').'/sales', $request->all());

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

        Alert::success('Success', 'Sale created successfully');
        return redirect()->route('manage.sales.index');
    }

    public function destroy($id)
    {
        $response = Http::withToken($this->token)
            ->delete(config('services.api.base_url')."/sales/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Delete failed');
            return redirect()->back();
        }

        Alert::success('Success', 'Sale voided successfully');
        return redirect()->route('manage.sales.index');
    }
}
