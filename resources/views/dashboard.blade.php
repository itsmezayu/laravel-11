<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- Main Content --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- KOTAK SAPAAN PENGGUNA --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium">Selamat datang, {{ $userName }}!</h3>
                </div>
            </div>

            {{-- FITUR PENCARI INFO NEGARA --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">🌎 Pencari Info Negara</h3>
                    <form action="{{ route('dashboard') }}" method="GET" class="mb-6">
                        <div class="flex items-center gap-2">
                            <x-text-input name="search_country" class="block w-full" type="text"
                                placeholder="Ketik nama negara (e.g., Japan)" :value="request('search_country')" />
                            <x-primary-button type="submit">{{ __('Cari') }}</x-primary-button>
                        </div>
                    </form>
                    @if (isset($country))
                        <div class="bg-gray-50 dark:bg-gray-700/50 shadow-inner rounded-lg p-6">
                            <img src="{{ $country['flags']['svg'] }}" alt="Bendera {{ $country['name']['common'] }}"
                                class="w-1/3 border dark:border-gray-600 mb-4">
                            <h2 class="text-3xl font-bold">{{ $country['name']['official'] }}</h2>
                            <p class="text-xl text-gray-600 dark:text-gray-400 mb-4">{{ $country['name']['common'] }}
                            </p>
                            <div class="space-y-2">
                                <p><strong>Ibu Kota:</strong> {{ $country['capital'][0] ?? 'N/A' }}</p>
                                <p><strong>Populasi:</strong> {{ number_format($country['population']) }} jiwa</p>
                                <p><strong>Region:</strong> {{ $country['region'] }}
                                    ({{ $country['subregion'] ?? '' }})</p>
                            </div>
                        </div>
                    @elseif (isset($countryError))
                        <div class="bg-red-100 dark:bg-red-900/50 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative"
                            role="alert">
                            <strong class="font-bold">Oops!</strong>
                            <span class="block sm:inline">{{ $countryError }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- FITUR KAMUS CERDAS --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">📖 Kamus Cerdas</h3>
                    <form action="{{ route('dashboard') }}" method="GET" class="mb-6">
                        <div class="flex items-center gap-2">
                            <x-text-input name="search_word" class="block w-full" type="text"
                                placeholder="Ketik kata bahasa Inggris (e.g., success)" :value="request('search_word')" />
                            <x-primary-button type="submit">{{ __('Cari') }}</x-primary-button>
                        </div>
                    </form>
                    @if (isset($wordData))
                        @foreach ($wordData as $entry)
                            <div class="bg-gray-50 dark:bg-gray-700/50 shadow-inner rounded-lg p-6 space-y-4 mb-[15px]">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <h2 class="text-3xl font-bold">{{ $entry['word'] }}</h2>
                                    <span
                                        class="text-xl text-gray-600 dark:text-gray-400">{{ $entry['phonetic'] ?? '' }}</span>
                                    @foreach ($entry['phonetics'] as $phonetic)
                                        @if (isset($phonetic['audio']) && $phonetic['audio'])
                                            <audio controls src="{{ $phonetic['audio'] }}" class="h-8"></audio>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                @foreach ($entry['meanings'] as $meaning)
                                    <div class="pt-4 border-t dark:border-gray-600">
                                        <h4 class="font-bold italic">{{ $meaning['partOfSpeech'] }}</h4>
                                        <ul class="list-disc list-inside mt-2 space-y-1">
                                            @foreach ($meaning['definitions'] as $definition)
                                                <li>{{ $definition['definition'] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @elseif (isset($wordError))
                        <div class="bg-red-100 dark:bg-red-900/50 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative"
                            role="alert">
                            <strong class="font-bold">Oops!</strong>
                            <span class="block sm:inline">{{ $wordError }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- FITUR TEXT ANALYZER (PYTHON SERVICE) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">🐍 Text Analyzer</h3>
                    <form action="{{ route('dashboard') }}" method="POST">
                        @csrf
                        <textarea name="text_content" rows="8"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-900 dark:text-white">{{ $originalText ?? '' }}</textarea>
                        <x-primary-button type="submit" class="mt-4">{{ __('Analisis Teks') }}</x-primary-button>
                    </form>
                    @if (isset($analysisResults))
                        <div
                            class="mt-6 pt-6 border-t dark:border-gray-700 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <div class="text-sm uppercase text-gray-500 dark:text-gray-400">Jumlah Karakter</div>
                                <div class="text-3xl font-bold">{{ $analysisResults['char_count'] }}</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <div class="text-sm uppercase text-gray-500 dark:text-gray-400">Jumlah Kata</div>
                                <div class="text-3xl font-bold">{{ $analysisResults['word_count'] }}</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <div class="text-sm uppercase text-gray-500 dark:text-gray-400">Estimasi Waktu Baca
                                </div>
                                <div class="text-3xl font-bold">{{ $analysisResults['reading_time_minutes'] }} Menit
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- FITUR LOREM IPSUM GENERATOR --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">✍️ Lorem Ipsum Generator</h3>
                    <form action="{{ route('dashboard') }}" method="GET" class=" mb-6">
                        <input type="hidden" name="generate_lorem" value="1">

                        {{-- Input untuk Jumlah --}}
                        <div class="mb-4">
                            <x-input-label for="generate_count" :value="__('Jumlah')" />
                            <x-text-input id="generate_count" name="generate_count" type="number"
                                class="mt-1 block w-full" :value="request('generate_count', 3)" min="1" max="100" />
                        </div>

                        {{-- Pilihan Tipe (Paragraf atau Kalimat) --}}
                        <div class="mb-4">
                            <x-input-label :value="__('Generate Berdasarkan')" />
                            <div class="mt-2 flex items-center space-x-6">
                                <label class="flex items-center">
                                    <input type="radio" name="generate_type" value="paragraphs"
                                        class="dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                        {{ request('generate_type', 'paragraphs') == 'paragraphs' ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Paragraf</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="generate_type" value="sentences"
                                        class="dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                        {{ request('generate_type') == 'sentences' ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Kalimat</span>
                                </label>
                            </div>
                        </div>

                        <x-primary-button type="submit">{{ __('Generate') }}</x-primary-button>
                    </form>

                    {{-- Area Hasil --}}
                    @if (isset($loremText))
                        <div class="mt-6 pt-6 border-t dark:border-gray-700 prose dark:prose-invert max-w-none">
                            @foreach ($loremText as $text)
                                <p>{{ $text }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- FITUR CHART GEMPA BUMI --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">🌋 Gempa Bumi - 7 Hari Terakhir</h3>
                    <div>
                        <canvas id="earthquakeChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- FITUR PRAKIRAAN CUACA --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">☀️ Weather Forecast Surabaya</h3>

                    @if (isset($weatherData['daily']))
                        <div class="space-y-3">
                            @foreach ($weatherData['daily']['time'] as $index => $date)
                                <div
                                    class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                                    <div>
                                        <p class="font-semibold">{{ \Carbon\Carbon::parse($date)->format('l, d M') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-red-500">
                                            {{ $weatherData['daily']['temperature_2m_max'][$index] }}&deg;C</p>
                                        <p class="text-sm text-blue-400">
                                            {{ $weatherData['daily']['temperature_2m_min'][$index] }}&deg;C</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Gagal memuat data prakiraan cuaca.</p>
                    @endif
                </div>
            </div>

            {{-- FITUR EKSPLORASI PTN & PRODI --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">🏛️ Eksplorasi Data PTN & Prodi</h3>

                    @if (isset($ptnError))
                        <p class="text-red-500">{{ $ptnError }}</p>
                    @else
                        <div class="mb-4">
                            <x-input-label for="ptn_select" :value="__('Pilih Perguruan Tinggi Negeri')" />
                            <select id="ptn_select"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-900 dark:text-gray-300">
                                <option value="">-- Pilih PTN --</option>
                                @foreach ($ptnList as $ptn)
                                    <option value="{{ $ptn['kode_ptn'] }}">{{ $ptn['nama_ptn'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Area ini akan diisi dengan daftar prodi secara dinamis --}}
                        <div id="prodi-list-container" class="mt-6 pt-6 border-t dark:border-gray-700">
                            <p class="text-gray-500">Silakan pilih PTN untuk melihat daftar prodinya.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- KARTU SISWA ELIGIBLE (INTERAKTIF) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">🎓 Siswa Eligible SNBP</h3>

                    <div class="mb-4">
                        <x-input-label for="akreditasi_select" :value="__('Pilih Akreditasi Sekolah')" />
                        <select id="akreditasi_select"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-900 dark:text-gray-300">
                            <option value="">-- Pilih Akreditasi --</option>
                            <option value="A">Akreditasi A (40%)</option>
                            <option value="B">Akreditasi B (25%)</option>
                            <option value="C">Akreditasi C (5%)</option>
                        </select>
                    </div>

                    {{-- Area ini akan diisi dengan tabel siswa eligible --}}
                    <div id="eligible-student-container" class="border-t dark:border-gray-700 pt-4">
                        <p class="text-gray-500">Silakan pilih akreditasi untuk menampilkan daftar siswa.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

{{-- Script untuk Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- Inisialisasi Chart.js untuk menampilkan data gempa bumi --}}
<script>
    // Ambil data gempa dari variabel yang di-passing dari controller
    const earthquakeData = @json($earthquakeData ?? null);
    if (earthquakeData) {
        const ctx = document.getElementById('earthquakeChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: earthquakeData.labels,
                datasets: [{
                    label: 'Magnitudo Gempa',
                    data: earthquakeData.magnitudes, // <-- Gunakan data magnitudo
                    backgroundColor: 'rgba(239, 68, 68, 0.2)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            // Kustomisasi judul tooltip (tanggal)
                            title: function(context) {
                                return context[0].label;
                            },
                            // Kustomisasi isi tooltip (magnitudo dan lokasi)
                            label: function(context) {
                                const magnitude = context.parsed.y;
                                const place = earthquakeData.places[context.dataIndex];
                                return `Magnitudo: ${magnitude}`;
                            },
                            afterLabel: function(context) {
                                return `Lokasi: ${earthquakeData.places[context.dataIndex]}`;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
{{-- Script API --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Logika untuk Eksplorasi PTN & Prodi
        let currentSort = {
            by: 'nama_prodi',
            direction: 'asc'
        };
        const ptnSelect = document.getElementById('ptn_select');
        const prodiContainer = document.getElementById('prodi-list-container');

        function fetchAndDisplayProdi(url) {
            if (!ptnSelect.value) {
                prodiContainer.innerHTML =
                    '<p class="text-gray-500">Silakan pilih PTN untuk melihat daftar prodinya.</p>';
                return;
            }
            prodiContainer.innerHTML = '<p class="text-gray-500">Memuat data prodi...</p>';

            fetch(url)
                .then(response => response.json())
                .then(paginator => {
                    if (paginator.error || paginator.data.length === 0) {
                        prodiContainer.innerHTML =
                            `<p class="text-gray-500">Data prodi tidak ditemukan.</p>`;
                        return;
                    }

                    let tableHtml = `<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase cursor-pointer" data-sort="kode_prodi">
                                        Kode Prodi
                                        <span id="sort-icon-kode_prodi"></span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase cursor-pointer" data-sort="nama_prodi">
                                        Nama Prodi
                                        <span id="sort-icon-nama_prodi"></span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase cursor-pointer" data-sort="tahun">
                                        Tahun
                                        <span id="sort-icon-tahun"></span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase cursor-pointer" data-sort="peminat">
                                        Peminat
                                        <span id="sort-icon-peminat"></span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase cursor-pointer" data-sort="daya_tampung">
                                        Daya Tampung
                                        <span id="sort-icon-daya_tampung"></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">`;

                    paginator.data.forEach(prodi => {
                        tableHtml += `
                            <tr>

                                <td class="px-6 py-4 whitespace-nowrap">${prodi.kode_prodi ?? 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${prodi.nama_prodi ?? 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${prodi.tahun ?? 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${prodi.peminat ?? 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${prodi.daya_tampung ?? 'N/A'}</td>
                            </tr>
                        `;
                    });
                    tableHtml += `</tbody></table></div>`;

                    // Membuat link paginasi
                    let paginationHtml =
                        '<div class="mt-4 flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">';
                    paginationHtml +=
                        `<div>Menampilkan ${paginator.from} sampai ${paginator.to} dari ${paginator.total} hasil</div>`;
                    paginationHtml += '<div class="flex items-center space-x-2">';
                    paginator.links.forEach(link => {
                        const url = link.url ? `data-url="${link.url}"` : '';
                        const label = link.label.replace(/&laquo;|&raquo;/g, '').trim();
                        const classes = link.active ? 'bg-indigo-600 text-white' : (link.url ?
                            'bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600' :
                            'bg-gray-200 dark:bg-gray-800 text-gray-400 dark:text-gray-600');
                        const tag = link.url ? 'button' : 'span';

                        paginationHtml +=
                            `<${tag} ${url} class="px-3 py-1 rounded-md ${classes}">${label}</${tag}>`;
                    });
                    paginationHtml += '</div></div>';

                    prodiContainer.innerHTML = tableHtml + paginationHtml;
                    // Update ikon sorting
                    document.querySelectorAll('thead th span').forEach(span => span.textContent = '');
                    const currentIconSpan = document.getElementById(`sort-icon-${currentSort.by}`);
                    if (currentIconSpan) {
                        currentIconSpan.textContent = currentSort.direction === 'asc' ? ' ▲' : ' ▼';
                    }
                })
                .catch(error => {
                    prodiContainer.innerHTML =
                        '<p class="text-red-500">Terjadi kesalahan. Cek console untuk detail.</p>';
                    console.error('Fetch Error:', error);
                });
        }

        ptnSelect.addEventListener('change', () => {
            const ptnId = ptnSelect.value;
            // Reset sorting saat ganti PTN
            currentSort = {
                by: 'nama_prodi',
                direction: 'asc'
            };
            const initialUrl =
                `/api/ptn/${ptnId}/prodi?sort_by=${currentSort.by}&sort_direction=${currentSort.direction}`;
            fetchAndDisplayProdi(initialUrl);
        });

        prodiContainer.addEventListener('click', function(e) {
            const target = e.target;
            const header = target.closest('th[data-sort]');
            const button = target.closest('button[data-url]');

            if (header) { // Jika header tabel diklik untuk sorting
                const sortBy = header.dataset.sort;
                if (currentSort.by === sortBy) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.by = sortBy;
                    currentSort.direction = 'asc';
                }
                const url =
                    `/api/ptn/${ptnSelect.value}/prodi?sort_by=${currentSort.by}&sort_direction=${currentSort.direction}`;
                fetchAndDisplayProdi(url);
            } else if (button) { // Jika tombol paginasi diklik
                const url = new URL(button.dataset.url);
                fetchAndDisplayProdi(url.pathname + url.search);
            }
        });

        // Logika untuk Siswa Eligible
        const akreditasiSelect = document.getElementById('akreditasi_select');
        const eligibleContainer = document.getElementById('eligible-student-container');

        let eligibleSort = {
            by: 'nama_siswa',
            direction: 'asc'
        };

        function fetchAndDisplayEligible(url) {
            if (!akreditasiSelect.value) {
                eligibleContainer.innerHTML =
                    '<p class="text-gray-500">Silakan pilih akreditasi untuk menampilkan daftar siswa.</p>';
                return;
            }

            eligibleContainer.innerHTML = '<p class="text-gray-500">Memuat data siswa...</p>';

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.error || data.length === 0) {
                        eligibleContainer.innerHTML =
                            '<p class="text-gray-500">Data tidak ditemukan.</p>';
                        return;
                    }

                    // Sorting client-side jika API tidak support, jika support, gunakan API param
                    let sortedData = [...data];
                    sortedData.sort((a, b) => {
                        let key = eligibleSort.by;
                        let dir = eligibleSort.direction === 'asc' ? 1 : -1;
                        let valA = a[key] ?? '';
                        let valB = b[key] ?? '';
                        // Numeric sort for rata_total and id_siswa
                        if (key === 'rata_total') {
                            valA = parseFloat(valA) || 0;
                            valB = parseFloat(valB) || 0;
                        }
                        if (key === 'id_siswa') {
                            valA = valA.toString();
                            valB = valB.toString();
                        }
                        if (valA < valB) return -1 * dir;
                        if (valA > valB) return 1 * dir;
                        return 0;
                    });

                    let tableHtml = `<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase cursor-pointer" data-sort="id_siswa">
                                NISN
                                <span id="eligible-sort-icon-id_siswa"></span>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase cursor-pointer" data-sort="nama_siswa">
                                Nama Siswa
                                <span id="eligible-sort-icon-nama_siswa"></span>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase cursor-pointer" data-sort="rata_total">
                                Rata-Rata Total
                                <span id="eligible-sort-icon-rata_total"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">`;

                    sortedData.forEach(student => {
                        tableHtml += `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">${student.id_siswa ? student.id_siswa : ''}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${student.nama_siswa ?? 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${student.rata_total ?? 'N/A'}</td>
                        </tr>
                    `;
                    });

                    tableHtml += `</tbody></table></div>`;
                    eligibleContainer.innerHTML = tableHtml;

                    // Update sort icon
                    document.querySelectorAll('#eligible-student-container thead th span').forEach(span =>
                        span.textContent = '');
                    const iconSpan = document.getElementById(`eligible-sort-icon-${eligibleSort.by}`);
                    if (iconSpan) {
                        iconSpan.textContent = eligibleSort.direction === 'asc' ? ' ▲' : ' ▼';
                    }
                })
                .catch(error => {
                    eligibleContainer.innerHTML =
                        '<p class="text-red-500">Terjadi kesalahan saat memuat data.</p>';
                });
        }

        akreditasiSelect.addEventListener('change', function() {
            const selectedAkreditasi = this.value;
            eligibleSort = {
                by: 'nama_siswa',
                direction: 'asc'
            };
            if (!selectedAkreditasi) {
                eligibleContainer.innerHTML =
                    '<p class="text-gray-500">Silakan pilih akreditasi untuk menampilkan daftar siswa.</p>';
                return;
            }
            fetchAndDisplayEligible(`/api/eligible-students?akreditasi=${selectedAkreditasi}`);
        });

        eligibleContainer.addEventListener('click', function(e) {
            const header = e.target.closest('th[data-sort]');
            if (!header) return;
            const sortBy = header.dataset.sort;
            if (eligibleSort.by === sortBy) {
                eligibleSort.direction = eligibleSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                eligibleSort.by = sortBy;
                eligibleSort.direction = 'asc';
            }
            const selectedAkreditasi = akreditasiSelect.value;
            if (!selectedAkreditasi) return;
            fetchAndDisplayEligible(`/api/eligible-students?akreditasi=${selectedAkreditasi}`);
        });

    });
</script>
