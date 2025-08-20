<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AboutController extends Controller
{
    /**
     * Menampilkan halaman about us.
     */
    public function index(): View
    {
        $team = []; // Siapkan variabel team

        // Cek hak akses menggunakan Gate
        if (Gate::allows('is-admin')) {
            // Jika admin, ambil SEMUA member dari database
            $team = Member::all();
        } else {
            // Jika bukan admin, ambil HANYA member yang dimiliki user ini
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $team = $user->members;
        }

        // Kirim data ke view
        return view('about', [
            'teamMembers' => $team,
            'companyName' => 'Laravel Team',
        ]);
    }
}
