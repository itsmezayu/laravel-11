<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('About Us') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- About Us --}}
                    <div class="text-center mb-10">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tentang Kami: {{ $companyName }}
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Tim yang berdedikasi untuk membuat hal-hal luar
                            biasa.</p>
                    </div>

                    {{-- Members --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        @forelse ($teamMembers as $member)
                            <div
                                class="scale-100 p-6 bg-gray-100 dark:bg-gray-900/50 dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-lg flex flex-col">
                                <div>
                                    <div
                                        class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" class="w-7 h-7 stroke-red-500">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                    <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ $member->name }}</h2>
                                    <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                        {{ $member->position }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center md:col-span-2 lg:col-span-3 text-gray-500 dark:text-gray-400">Tim
                                belum terbentuk.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
