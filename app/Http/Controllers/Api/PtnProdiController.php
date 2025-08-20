<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class PtnProdiController extends Controller
{
    public function getProdiByPtn(Request $request, $ptnId)
    {
        $response = Http::get("http://127.0.0.1:8002/data");

        if ($response->successful()) {
            $allData = collect($response->json());

            $prodiList = $allData->filter(function ($item) use ($ptnId) {
                return isset($item['kode_ptn']) && $item['kode_ptn'] == $ptnId;
            });

            // Logika Sorting
            $sortBy = $request->query('sort_by', 'nama_prodi');
            $sortDirection = $request->query('sort_direction', 'asc');

            if ($sortDirection === 'desc') {
                $prodiList = $prodiList->sortByDesc($sortBy);
            } else {
                $prodiList = $prodiList->sortBy($sortBy);
            }

            return response()->json($prodiList->values());
        }

        return response()->json(['error' => 'Gagal mengambil data prodi.'], 404);
    }
}
