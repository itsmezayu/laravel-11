<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Menambahkan member --}}
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    + Tambah User
                </a>
            </div>

            {{-- Menampilkan pesan sukses --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-700 dark:bg-green-200 dark:text-green-800"
                    role="alert">
                    {{ session('success') }}
                </div>
            @endif
            {{-- Menampilkan pesan error --}}
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700 dark:bg-red-200 dark:text-red-800"
                    role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Form Pencarian --}}
            <div class="w-full mb-4">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 w-full">
                    {{-- Input dengan icon search --}}
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            placeholder="Cari nama atau email...">
                    </div>

                    {{-- Tombol Cari --}}
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition">
                        Cari
                    </button>

                    {{-- Tombol Reset --}}
                    @if (request('search'))
                        <a href="{{ route('admin.users.index') }}"
                            class="p-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 transition flex items-center justify-center"
                            title="Reset pencarian">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif

                </form>
            </div>

            {{-- Menampilkan tabel --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    @php $directionForName = ($sortBy == 'name' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                    <a href="{{ route('admin.users.index', ['sort_by' => 'name', 'sort_direction' => $directionForName]) }}"
                                        class="inline-flex items-center">
                                        Name
                                        @if ($sortBy == 'name')
                                            <span class="ml-1">{{ $sortDirection == 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    @php $directionForEmail = ($sortBy == 'email' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                    <a href="{{ route('admin.users.index', ['sort_by' => 'email', 'sort_direction' => $directionForEmail]) }}"
                                        class="inline-flex items-center">
                                        Email
                                        @if ($sortBy == 'email')
                                            <span class="ml-1">{{ $sortDirection == 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    @php $directionForRole = ($sortBy == 'role' && $sortDirection == 'asc') ? 'desc' : 'asc'; @endphp
                                    <a href="{{ route('admin.users.index', ['sort_by' => 'role', 'sort_direction' => $directionForRole]) }}"
                                        class="inline-flex items-center">
                                        Role
                                        @if ($sortBy == 'role')
                                            <span class="ml-1">{{ $sortDirection == 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->role }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{-- Tombol aksi hanya akan muncul jika email BUKAN email primordial --}}
                                        @if ($user->email !== 'superadmin@admin.com')
                                            <div class="flex justify-end space-x-4">
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Edit</a>

                                                {{-- Tombol Hapus hanya muncul jika bukan akunnya sendiri --}}
                                                @if (Auth::id() !== $user->id)
                                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                                        method="POST" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6">{{ $users->appends(request()->query())->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
