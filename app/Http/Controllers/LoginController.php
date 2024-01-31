<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login()
    {
        $data = request()->only([
            'email', 'password'
        ]);

        if (!auth('app')->validate($data)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'password' => 'Le mot de passe ne correspond pas à nos enregistrements.',
                ]);
        }

        $client = Client::where('email', $data['email'])->first();

        $remember = !empty(request('remember', false));

        auth('app')->login($client, $remember);

        return redirect('/');
    }

    public function logout()
    {
        auth('app')->logout();

        return redirect('/login');
    }
}
