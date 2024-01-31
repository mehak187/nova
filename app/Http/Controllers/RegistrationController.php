<?php

namespace App\Http\Controllers;

use App\Mail\NewUserRegistration;
use App\Mail\Welcome;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('register');
    }

    public function store()
    {
        $data = request()->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:clients,email',
            'mobile_phone' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $data['password'] = bcrypt($data['password']);

        $client = Client::create($data);

        auth('app')->login($client);

        Mail::to('mail@calliopee.ch')
            ->send(new NewUserRegistration($client));

        Mail::to($client->email)
            ->send(new Welcome());

        return redirect('/');
    }

    public function fromCode(Client $client)
    {
        return view('register-from-code', compact('client'));
    }

    public function storeFromCode(Client $client)
    {
        $data = request()->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('clients', 'email')->ignore($client->id)
            ],
             'mobile_phone' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $data['password'] = bcrypt($data['password']);
        $data['register_code'] = null;

        $client->update($data);

        auth('app')->login($client);

        return redirect('/');
    }
}
