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
