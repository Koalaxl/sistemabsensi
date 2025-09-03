<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $user = Pengguna::where('username', $request->username)
            ->where('password', $request->password) // sebaiknya bcrypt
            ->first();

        if ($user) {
            // simpan session
            $request->session()->put('login_id', $user->id);
            $request->session()->put('login_role', $user->role);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'guru') {
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
