<?php

namespace App\Http\Controllers\Web;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\ProductRequest;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = session('access_token');
    }

    public function index(Request $request)
    {
        $productsResponse = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/products', $request->all());

        if ($productsResponse->failed()) {
            Alert::error('Error', $productsResponse->json('message') ?? 'Something went wrong');
            return redirect()->back();
        }

        $productsData = $productsResponse->json();
        $products = collect($productsData['data'] ?? [])->map(function($item) {
            return json_decode(json_encode($item));
        });
        $pagination = (object) ($productsData['links'] ?? []);

        $categoriesResponse = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/categories', [
                'per_page' => 'all'
            ]);

        if ($categoriesResponse->failed()) {
            Alert::error('Error', $categoriesResponse->json('message') ?? 'Something went wrong');
            return redirect()->back();
        }

        $categoriesData = $categoriesResponse->json();
        $categories = collect($categoriesData['data'] ?? [])->map(fn($item) => (object) $item);

        return view('masters.products.index', compact('products', 'pagination', 'categories'));
    }


    public function store(Request $request)
    {
        $response = Http::withToken($this->token)
            ->post(config('services.api.base_url').'/products', $request->all());

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

        Alert::success('Success', 'Product created successfully');
        return redirect()->route('manage.products.index');
    }

    public function show($id)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url')."/products/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->json();
        $categoryObject = (object) ($data['data'] ?? []);

        return $categoryObject;
    }

    public function edit($id)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url')."/products/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->json();
        $categoryObject = (object) ($data['data'] ?? []);

        return $categoryObject;
    }

    public function update(Request $request, $id)
    {
        $response = Http::withToken($this->token)
            ->put(config('services.api.base_url')."/products/{$id}", $request->all());

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

        Alert::success('Success', 'Product updated successfully');
        return redirect()->route('manage.products.index');
    }

    public function destroy($id)
    {
        $response = Http::withToken($this->token)
            ->delete(config('services.api.base_url')."/products/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Delete failed');
            return redirect()->back();
        }

        Alert::success('Success', 'Product deleted successfully');
        return redirect()->route('manage.products.index');
    }
}
