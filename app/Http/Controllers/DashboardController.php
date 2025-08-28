<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Faker\Factory as FakerFactory;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil input akreditasi dari request, default-nya 'A'
        $selectedAkreditasi = $request->input('akreditasi', '');

        // API untuk mendapatkan daftar semua PTN
        $ptnResponse = Http::get('http://127.0.0.1:8002/data/ptn');

        // API untuk mendapatkan daftar siswa eligible
        $eligibleResponse = Http::get('http://127.0.0.1:8003/eligible', ['akreditasi' => '' . $selectedAkreditasi]);

        // --- Logika Prakiraan Cuaca  ---
        $weatherResponse = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => -7.2575, // Latitude Surabaya
            'longitude' => 112.7521, // Longitude Surabaya
            'daily' => 'temperature_2m_max,temperature_2m_min',
            'timezone' => 'Asia/Jakarta',
        ]);



        // Siapkan variabel untuk semua data yang akan dikirim ke view
        $viewData = [
            'userName' => $user->name,
            'weatherData' => $weatherResponse->successful() ? $weatherResponse->json() : null,
            'country' => null,
            'countryError' => null,
            'analysisResults' => null,
            'originalText' => null,
            'wordData' => null,
            'wordError' => null,
            'loremText' => null,
            'earthquakeData' => null,
            'ptnList' => $ptnResponse->successful() ? $ptnResponse->json() : [],
            'ptnError' => $ptnResponse->failed() ? 'Gagal memuat daftar PTN.' : null,
            'eligibleStudents' => $eligibleResponse->successful() ? $eligibleResponse->json() : [],
            'eligibleError' => $eligibleResponse->failed() ? 'Gagal memuat data siswa eligible.' : null,
            'selectedAkreditasi' => $selectedAkreditasi, // Kirim akreditasi terpilih ke view


        ];

        // --- Logika Pencarian Negara ---
        if ($request->has('search_country') && !empty($request->input('search_country'))) {
            $searchQuery = $request->input('search_country');
            $response = Http::get("https://restcountries.com/v3.1/name/{$searchQuery}");
            if ($response->successful()) {
                $viewData['country'] = $response->json()[0];
            } else {
                $viewData['countryError'] = "Negara '{$searchQuery}' tidak ditemukan.";
            }
        }

        // --- Logika Analisis Teks (Python Service) ---
        if ($request->has('text_content')) {
            $request->validate(['text_content' => 'required|string']);
            $textContent = $request->input('text_content');
            $response = Http::post('http://127.0.0.1:8001/analyze', ['content' => $textContent]);
            if ($response->successful()) {
                $viewData['analysisResults'] = $response->json();
            }
            $viewData['originalText'] = $textContent;
        }

        // --- Logika Kamus Cerdas ---
        if ($request->has('search_word')) {
            $searchQuery = $request->input('search_word');
            $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$searchQuery}");
            if ($response->successful()) {
                $viewData['wordData'] = $response->json();
            } else {
                $viewData['wordError'] = "Kata '{$searchQuery}' tidak ditemukan dalam kamus.";
            }
        }

        // --- Logika Lorem Ipsum Generator ---
        if ($request->has('generate_lorem')) {
            $validated = $request->validate([
                'generate_count' => 'required|integer|min:1|max:100',
                'generate_type' => 'required|string|in:paragraphs,sentences',
            ]);

            $faker = FakerFactory::create();
            $count = $validated['generate_count'];
            $type = $validated['generate_type'];

            if ($type === 'sentences') {
                $viewData['loremText'] = $faker->sentences($count);
            } else {
                $viewData['loremText'] = $faker->paragraphs($count);
            }
        }

        // --- Logika Chart Gempa Bumi ---
        $endDate = now()->format('Y-m-d');
        $startDate = now()->subDays(7)->format('Y-m-d'); // Mengambil data gempa bumi selama [] hari terakhir

        // Panggil API USGS untuk gempa bumi magnitudo [] selama [] hari terakhir
        $response = Http::get('https://earthquake.usgs.gov/fdsnws/event/1/query', [
            'format' => 'geojson',
            'starttime' => $startDate,
            'endtime' => $endDate,
            'minmagnitude' => 5.0 // Ganti dengan nilai magnitudo yang diinginkan
        ]);

        if ($response->successful()) {
            $earthquakes = $response->json()['features'];
            $labels = [];
            $magnitudes = [];
            $places = []; // <-- Array BARU untuk menyimpan lokasi

            foreach ($earthquakes as $eq) {
                $labels[] = date('M d', $eq['properties']['time'] / 1000); // Mengonversi timestamp ke format tanggal
                $magnitudes[] = $eq['properties']['mag']; // <-- Ambil data magnitudo
                $places[] = $eq['properties']['place']; // <-- Ambil data lokasi
            }

            $viewData['earthquakeData'] = [
                'labels' => array_reverse($labels),
                'magnitudes' => array_reverse($magnitudes), // <-- Ganti nama dari 'data'
                'places' => array_reverse($places)       // <-- Kirim data lokasi ke view
            ];
        }

        // Kirim semua data ke view 'dashboard'
        return view('dashboard', $viewData);
    }
}
