<x-app-layout>
    <div class="flex-1 space-y-10">
        <section
            class="relative overflow-hidden rounded-[28px] border border-slate-200/70 bg-gradient-to-br from-sky-50 via-white to-emerald-50 p-6 shadow-xl ring-1 ring-white/40 dark:border-slate-800 dark:bg-gradient-to-br dark:from-slate-950 dark:via-slate-900 dark:to-sky-950 dark:ring-slate-800">
            <div
                class="pointer-events-none absolute inset-0 opacity-70 [background-image:radial-gradient(circle_at_20%_18%,rgba(14,165,233,.12),transparent_36%),radial-gradient(circle_at_86%_10%,rgba(52,211,153,.14),transparent_34%),linear-gradient(120deg,rgba(14,165,233,.08),rgba(255,255,255,0),rgba(16,185,129,.08))]">
            </div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-3">
                    <p class="text-[11px] uppercase tracking-[0.32em] text-slate-500">Flight deck</p>
                    <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Journey board</h1>
                    <p class="max-w-3xl text-sm text-slate-600 dark:text-slate-300">Aviation-flavored overview that
                        keeps your routes, layovers, and memories in one place.</p>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-700 dark:text-slate-200">
                        <span class="pill-soft">Journeys: {{ $stats['totalTrips'] ?? 0 }}</span>
                        <span class="pill-soft">This year: {{ $stats['tripsThisYear'] ?? 0 }}</span>
                        <span class="pill-soft">Countries: {{ $stats['countriesVisited'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="space-y-6">
            <livewire:dashboard.active-trips />

            <livewire:dashboard.past-trips />
        </div>
    </div>
</x-app-layout>
