<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\CategoryRequest;
use RealRashid\SweetAlert\Facades\Alert;

class CategoryController
{
    protected $token;

    public function __construct()
    {
        $this->token = session('access_token');
    }

    public function index(Request $request)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/categories', $request->all());

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Something went wrong');
            return redirect()->back();
        }

        $data = $response->json();
        $categories = collect($data['data'] ?? [])->map(function($item) {
            return json_decode(json_encode($item));
        });

        $pagination = json_decode(json_encode($data['links'] ?? []));
        $meta = $data['meta'] ?? [];
        $perPage = $meta['per_page'] ?? 10;
        $currentPage = $request->query('page', 1);

        $startNumber = ($currentPage - 1) * $perPage;

        return view('masters.categories.index', compact('categories', 'pagination', 'startNumber'));
    }

    public function store(Request $request)
    {
        $response = Http::withToken($this->token)
            ->post(config('services.api.base_url').'/categories', $request->all());

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

        Alert::success('Success', 'Category created successfully');
        return redirect()->route('manage.categories.index');
    }

    public function show($id)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url')."/categories/{$id}");

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
            ->get(config('services.api.base_url')."/categories/{$id}");

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
            ->put(config('services.api.base_url')."/categories/{$id}", $request->all());

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

        Alert::success('Success', 'Category updated successfully');
        return redirect()->route('manage.categories.index');
    }

    public function destroy($id)
    {
        $response = Http::withToken($this->token)
            ->delete(config('services.api.base_url')."/categories/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Delete failed');
            return redirect()->back();
        }

        Alert::success('Success', 'Category deleted successfully');
        return redirect()->route('manage.categories.index');
    }
}
