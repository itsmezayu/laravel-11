<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EligibleStudentController extends Controller
{
    public function getEligibleStudents(Request $request)
    {
        // Validasi input akreditasi
        $akreditasi = $request->validate([
            'akreditasi' => 'required|string|in:A,B,C',
        ]);

        // Panggil API Flask di port 8003
        $response = Http::get('http://127.0.0.1:8003/eligible', [
            'akreditasi' => $akreditasi['akreditasi'],
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Gagal mengambil data.'], 500);
    }
}
