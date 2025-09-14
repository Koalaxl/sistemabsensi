<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    
    public function login(Request $request)
    {
        $user = Pengguna::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // simpan session
            $request->session()->put('user', $user);

            if ($user->role === 'admin') {
                $request->session()->put('login_id', $user->id); // admin pakai id pengguna
                $request->session()->put('login_role', 'admin');
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'guru') {
                $request->session()->put('login_id', $user->id_guru); // guru pakai id_guru dari relasi
                $request->session()->put('login_role', 'guru');
                return redirect()->route('guru.dashboard');
            }
        }

        return back()->withErrors(['msg' => 'Username atau password salah']);
    }

    public function logout(Request $request)
    {
        $request->session()->flush(); // hapus semua session
        return redirect()->route('login');
    }
}
