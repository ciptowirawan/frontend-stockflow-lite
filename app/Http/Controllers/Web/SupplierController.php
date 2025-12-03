<?php

namespace App\Http\Controllers\Web;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;

class SupplierController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = session('access_token');
    }

    public function index(Request $request)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/suppliers', $request->all());

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Something went wrong');
            return redirect()->back();
        }

        $data = $response->json();
        $suppliers = collect($data['data'] ?? [])->map(function($item) {
            return json_decode(json_encode($item));
        });

        $pagination = json_decode(json_encode($data['links'] ?? []));

        $meta = $data['meta'] ?? [];
        $perPage = $meta['per_page'] ?? 10;
        $currentPage = $request->query('page', 1);

        $startNumber = ($currentPage - 1) * $perPage;

        return view('masters.suppliers.index', compact('suppliers', 'pagination', 'startNumber'));
    }

    public function store(Request $request)
    {
        $response = Http::withToken($this->token)
            ->post(config('services.api.base_url').'/suppliers', $request->all());

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

        Alert::success('Success', 'Supplier created successfully');
        return redirect()->route('manage.suppliers.index');
    }

    public function show($id)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url')."/suppliers/{$id}");

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
            ->get(config('services.api.base_url')."/suppliers/{$id}");

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
            ->put(config('services.api.base_url')."/suppliers/{$id}", $request->all());

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

        Alert::success('Success', 'Supplier updated successfully');
        return redirect()->route('manage.suppliers.index');
    }

    public function destroy($id)
    {
        $response = Http::withToken($this->token)
            ->delete(config('services.api.base_url')."/suppliers/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Delete failed');
            return redirect()->back();
        }

        Alert::success('Success', 'Supplier deleted successfully');
        return redirect()->route('manage.suppliers.index');
    }
}
