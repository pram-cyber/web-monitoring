<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="#" class="text-xl font-bold text-green-600 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        SmartWaste
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-sm font-medium text-gray-500">
                    Halo, <span class="text-gray-800 font-bold">{{ Auth::user()->name }}</span>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 text-sm font-semibold rounded-md hover:bg-red-100 transition">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>