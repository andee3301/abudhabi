<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Explore destinations</h2>
                <p class="text-sm text-gray-600">Search a city or country to see prep info.</p>
            </div>
            <form method="GET" action="{{ route('explore.index') }}" class="flex gap-2">
                <input name="q" value="{{ request('q') }}" placeholder="Search location..."
                       class="w-64 rounded-xl border border-white/50 bg-white/70 px-3 py-2 text-sm shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500">
                <button class="rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Search</button>
            </form>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4 rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur">
                <h3 class="text-lg font-semibold text-gray-900">{{ $destination }}</h3>
                <p class="text-sm text-gray-700">{{ $info['summary'] }}</p>
                <div class="rounded-xl bg-gradient-to-br from-indigo-100 via-white to-sky-100 p-4 text-sm text-gray-700">
                    {{ $info['weather'] }} — placeholder weather
                </div>
                {{-- TODO: Replace this mocked data with real API response --}}
            </div>
            <div class="space-y-4">
                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur">
                    <h4 class="text-sm font-semibold text-gray-900">Things to prepare</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-700">
                        @foreach($info['prep'] as $item)
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500"></span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-2xl border border-white/30 bg-white/70 p-5 shadow-lg backdrop-blur">
                    <h4 class="text-sm font-semibold text-gray-900">My prep notes</h4>
                    <textarea rows="5" class="mt-2 w-full rounded-xl border border-white/50 bg-white/60 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Packing, visas, SIM, etc."></textarea>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
