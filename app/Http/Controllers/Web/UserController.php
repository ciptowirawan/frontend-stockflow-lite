<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = session('access_token');
    }

    public function index(Request $request)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url').'/users', $request->all());

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Something went wrong');
            return redirect()->back();
        }

        $data = $response->json();
        $users = collect($data['data'] ?? [])->map(function($item) {
            return json_decode(json_encode($item));
        });

        $pagination = json_decode(json_encode($data['links'] ?? []));

        return view('masters.users.index', compact('users', 'pagination'));
    }

    public function store(Request $request)
    {
        $response = Http::withToken($this->token)
            ->post(config('services.api.base_url').'/users', $request->all());

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

        Alert::success('Success', 'User created successfully');
        return redirect()->route('manage.users.index');
    }

    public function show($id)
    {
        $response = Http::withToken($this->token)
            ->get(config('services.api.base_url')."/users/{$id}");

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
            ->get(config('services.api.base_url')."/users/{$id}");

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
            ->put(config('services.api.base_url')."/users/{$id}", $request->all());

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

        Alert::success('Success', 'User updated successfully');
        return redirect()->route('manage.users.index');
    }

    public function destroy($id)
    {
        $response = Http::withToken($this->token)
            ->delete(config('services.api.base_url')."/users/{$id}");

        if ($response->failed()) {
            Alert::error('Error', $response->json('message') ?? 'Delete failed');
            return redirect()->back();
        }

        Alert::success('Success', 'User deleted successfully');
        return redirect()->route('manage.users.index');
    }
}
