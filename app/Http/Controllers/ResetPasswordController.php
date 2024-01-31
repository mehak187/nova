<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Notifications\ResetPassword;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    public function reset()
    {
        $data = request()->validate([
            'email' => 'required|exists:clients,email',
        ]);

        $client = Client::where('email', $data['email'])->first();

        $token = Str::random(30);

        DB::table('password_resets')
            ->insert([
                'email' => $client->email,
                'token' => $token,
                'created_at' => now(),
            ]);

        $client->notify(new ResetPassword($token));

        return back()->with('message', 'Merci, veuillez vérifier vos emails.');
    }

    public function change(string $token)
    {
        $result = DB::table('password_resets')
            ->where('token', $token)
            ->where('created_at', '>', now()->subMinutes(15))
            ->first();

        if (empty($result)) {
            abort(403, 'Le lien a expiré');
        }

        return view('change-password', [
            'token' => $token,
        ]);
    }

    public function update(string $token)
    {
        $data = request()->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $result = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        if (empty($result)) {
            abort(403, 'Le lien a expiré');
        }

        Client::where('email', $result->email)
            ->update([
                'password' => bcrypt($data['password']),
            ]);

        return redirect()->route('login')->with('message', 'Votre mot de passe a bien été changé, vous pouvez vous authentifier.');
    }
}
