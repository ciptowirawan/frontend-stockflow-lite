<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Api\AuthController as ApiAuthController;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->token = session('access_token');
    }

    public function login(Request $request)
    {
        $response = Http::post(config('services.api.base_url').'/login', $request->all());

        if ($response->failed()) {
            $data = $response->json();
            Alert::error('Login Failed', $data['message'] ?? 'Email atau password salah.');
            return redirect()->back()->withInput();
        }

        $data = $response->json();

        session([
            'username'     => $data['data']['username'],
            'access_token' => $data['data']['access_token'],
            'token_type'   => $data['data']['token_type'],
            'expires_in'   => $data['data']['expires_in'],
        ]);

        Alert::success('Login Successful', 'Welcome back!');
        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = Http::withToken($this->token)
            ->post(config('services.api.base_url').'/logout', $request->all());

        Alert::success('Logout Successful', 'You have been logged out.');
        return redirect('/');
    }
}
