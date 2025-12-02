<?php

namespace App\Http\Controllers\Web;

use App\Models\StockDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StockDetailRequest;

class StockDetailController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = session('access_token');
    }

    public function index(Request $request)
    {
        $stocksResponse = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/stock-details', $request->all());

        if ($stocksResponse->failed()) {
            Alert::error('Error', $stocksResponse->json('message') ?? 'Something went wrong');
            return redirect()->back();
        }

        $stocksData = $stocksResponse->json();
        $stocks = collect($stocksData['data'] ?? [])->map(function($item) {
            return json_decode(json_encode($item));
        });
        $pagination = (object) ($stocksData['links'] ?? []);

        return view('masters.stocks.index', compact('stocks', 'pagination'));
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

        return view('masters.stocks.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $response = Http::withToken($this->token)
            ->post(config('services.api.base_url').'/stock-details', $request->all());

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

        Alert::success('Success', 'Stock Movement created successfully');
        return redirect()->route('manage.stock-details.index');
    }

    public function show($id)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url')."/stock-details/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Resource not found');
            return redirect()->back();
        }

        $data = $response->json();
        $categoryObject = (object) ($data['data'] ?? []);

        return $categoryObject;
    }
}
