<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings', [
            'user' => auth('app')->user(),
        ]);
    }

    public function update()
    {
        $data = request()->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('clients', 'email')->ignore(auth('app')->id()),
            ],
            'password' => 'nullable|min:6|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        auth('app')->user()->update($data);

        return redirect()->back();
    }
}
