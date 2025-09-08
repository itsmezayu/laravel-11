<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Faker\Factory as FakerFactory;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        //   --- Logika API PTN ---
        $ptnResponse = Http::get('http://127.0.0.1:8002/data/ptn');

        //  --- Logika API Siswa Eligible ---
        $selectedAkreditasi = $request->input('akreditasi', '');
        $eligibleResponse = Http::get('http://127.0.0.1:5000/eligible', ['akreditasi' => '' . $selectedAkreditasi]);

        // --- Peta kota & koordinat ---
        $cityCoordinates = [
            'jakarta'      => ['name' => 'Jakarta', 'lat' => -6.2088, 'lon' => 106.8456],
            'surabaya'     => ['name' => 'Surabaya', 'lat' => -7.2575, 'lon' => 112.7521],
            'bandung'      => ['name' => 'Bandung', 'lat' => -6.9175, 'lon' => 107.6191],
            'medan'        => ['name' => 'Medan', 'lat' => 3.5952, 'lon' => 98.6722],
            'bekasi'       => ['name' => 'Bekasi', 'lat' => -6.2383, 'lon' => 106.9756],
            'tangerang'    => ['name' => 'Tangerang', 'lat' => -6.1700, 'lon' => 106.6406],
            'depok'        => ['name' => 'Depok', 'lat' => -6.4025, 'lon' => 106.7942],
            'semarang'     => ['name' => 'Semarang', 'lat' => -6.9667, 'lon' => 110.4167],
            'palembang'    => ['name' => 'Palembang', 'lat' => -2.9761, 'lon' => 104.7754],
            'makassar'     => ['name' => 'Makassar', 'lat' => -5.1477, 'lon' => 119.4327],
            'batam'        => ['name' => 'Batam', 'lat' => 1.0456, 'lon' => 104.0305],
            'bogor'        => ['name' => 'Bogor', 'lat' => -6.5950, 'lon' => 106.8166],
            'malang'       => ['name' => 'Malang', 'lat' => -7.9797, 'lon' => 112.6304],
            'padang'       => ['name' => 'Padang', 'lat' => -0.9471, 'lon' => 100.4172],
            'pekanbaru'    => ['name' => 'Pekanbaru', 'lat' => 0.5071, 'lon' => 101.4478],
            'denpasar'     => ['name' => 'Denpasar', 'lat' => -8.6705, 'lon' => 115.2126],
            'yogyakarta'   => ['name' => 'Yogyakarta', 'lat' => -7.7956, 'lon' => 110.3695],
            'samarinda'    => ['name' => 'Samarinda', 'lat' => -0.5022, 'lon' => 117.1537],
            'manado'       => ['name' => 'Manado', 'lat' => 1.4748, 'lon' => 124.8421],
            'banjarmasin'  => ['name' => 'Banjarmasin', 'lat' => -3.3285, 'lon' => 114.5938],
            'pontianak'    => ['name' => 'Pontianak', 'lat' => -0.0263, 'lon' => 109.3425],
            'cimahi'       => ['name' => 'Cimahi', 'lat' => -6.8730, 'lon' => 107.5412],
            'kediri'       => ['name' => 'Kediri', 'lat' => -7.8481, 'lon' => 112.0176],
            'solo'         => ['name' => 'Solo', 'lat' => -7.5667, 'lon' => 110.8167],
            'mataram'      => ['name' => 'Mataram', 'lat' => -8.5833, 'lon' => 116.1167],
            'balikpapan'   => ['name' => 'Balikpapan', 'lat' => -1.2654, 'lon' => 116.8312],
            'tasikmalaya'  => ['name' => 'Tasikmalaya', 'lat' => -7.3274, 'lon' => 108.2208],
            'cirebon'      => ['name' => 'Cirebon', 'lat' => -6.7320, 'lon' => 108.5523],
            'ambon'        => ['name' => 'Ambon', 'lat' => -3.6950, 'lon' => 128.1814],
            'ternate'      => ['name' => 'Ternate', 'lat' => 0.7900, 'lon' => 127.3900],
            'jambi'        => ['name' => 'Jambi', 'lat' => -1.6100, 'lon' => 103.6131],
            'probolinggo'  => ['name' => 'Probolinggo', 'lat' => -7.7569, 'lon' => 113.2115],
            'tegal'        => ['name' => 'Tegal', 'lat' => -6.8694, 'lon' => 109.1407],
            'palu'         => ['name' => 'Palu', 'lat' => -0.8917, 'lon' => 119.8707],
            'kupang'       => ['name' => 'Kupang', 'lat' => -10.1772, 'lon' => 123.6070],
            'bengkulu'     => ['name' => 'Bengkulu', 'lat' => -3.8004, 'lon' => 102.2655],
            'palangkaraya' => ['name' => 'Palangkaraya', 'lat' => -2.2080, 'lon' => 113.9133],
            'serang'       => ['name' => 'Serang', 'lat' => -6.1215, 'lon' => 106.1502],
            'karanganyar'  => ['name' => 'Karanganyar', 'lat' => -7.6361, 'lon' => 111.0542],
            'magelang'     => ['name' => 'Magelang', 'lat' => -7.4797, 'lon' => 110.2178],
            'lubuklinggau' => ['name' => 'Lubuklinggau', 'lat' => -3.2960, 'lon' => 102.8615],
            'binjai'       => ['name' => 'Binjai', 'lat' => 3.5986, 'lon' => 98.4854],
            'langsa'       => ['name' => 'Langsa', 'lat' => 4.4682, 'lon' => 97.9685],
            'bandaaceh'    => ['name' => 'Banda Aceh', 'lat' => 5.5483, 'lon' => 95.3238],
            'pematangsiantar' => ['name' => 'Pematangsiantar', 'lat' => 2.9595, 'lon' => 99.0682],
            'blitar'       => ['name' => 'Blitar', 'lat' => -8.0954, 'lon' => 112.1646],
            'sukabumi'     => ['name' => 'Sukabumi', 'lat' => -6.9222, 'lon' => 106.9275],
            'pasuruan'     => ['name' => 'Pasuruan', 'lat' => -7.6455, 'lon' => 112.9075],
            'sorong'       => ['name' => 'Sorong', 'lat' => -0.8760, 'lon' => 131.2614],
            'jayapura'     => ['name' => 'Jayapura', 'lat' => -2.5337, 'lon' => 140.7181],
        ];

        // Ambil kota yang dipilih dari request, default ke Surabaya
        $selectedCityKey = $request->input('city', 'surabaya');
        $cityData = $cityCoordinates[$selectedCityKey] ?? $cityCoordinates['surabaya'];
        $cityName = $cityData['name'];

        // --- BUAT KEY CACHE KHUSUS UNTUK KOTA INI ---
        $cacheKey = 'weather_' . $selectedCityKey;

        // Ambil dari cache jika ada, jika tidak fetch dan simpan
        $weatherData = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($cityData) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $cityData['lat'],
                'longitude' => $cityData['lon'],
                'daily' => 'temperature_2m_max,temperature_2m_min',
                'timezone' => 'Asia/Jakarta',
            ]);
            return $response->successful() ? $response->json() : null;
        });

        // --- Logika API Libur Nasional ---
        $currentYear = date('Y');
        $holidaysResponse = Http::withoutVerifying()->get("https://date.nager.at/api/v3/PublicHolidays/{$currentYear}/ID");

        // --- Data yang akan dikirim ke view ---
        $viewData = [
            'userName' => $user->name,
            'weatherData' => $weatherData,
            'cityName' => $cityName,
            'selectedCity' => $selectedCityKey,
            'cityList' => $cityCoordinates,
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
            'selectedAkreditasi' => $selectedAkreditasi,
            'holidays' => $holidaysResponse->successful() ? $holidaysResponse->json() : [],
            'holidaysError' => $holidaysResponse->failed() ? 'Gagal memuat data libur nasional.' : null,
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
