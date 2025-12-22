<div class="pt-1">
    <button type="button" wire:click="load" wire:target="load" wire:loading.attr="disabled"
        class="w-full rounded-full bg-slate-900 px-4 py-3 text-sm font-semibold text-slate-100 shadow-lg transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-emerald-500 dark:text-slate-950 dark:hover:bg-emerald-400"
        @disabled($disabled) aria-disabled="{{ $disabled ? 'true' : 'false' }}">
        <span wire:loading.remove wire:target="load">{{ $disabled ? 'No more journeys' : 'View more journeys' }}</span>
        <span wire:loading wire:target="load">Loading…</span>
    </button>
</div>
