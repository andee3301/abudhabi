<section class="space-y-4">
    <div class="space-y-1">
        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">API tokens</p>
        <p class="text-xs text-slate-500 dark:text-slate-400">Select scopes, generate, and revoke tokens. Keep tokens
            secret.</p>
    </div>

    <div
        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80 dark:bg-slate-900/70 dark:ring-slate-800/80">
        <form class="space-y-4" wire:submit.prevent="create">
            <label class="block text-sm font-medium text-slate-900 dark:text-slate-100">Token name
                <input type="text" wire:model.defer="tokenName"
                    class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900" />
            </label>

            <div class="space-y-2">
                <p class="text-sm font-medium text-slate-900 dark:text-slate-100">Select scopes</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($allowed as $ability)
                        <button type="button" wire:click="toggleAbility('{{ $ability }}')"
                            class="rounded-full px-3 py-1 text-xs font-semibold ring-1 transition {{ in_array($ability, $selected, true) ? 'bg-orange-600 text-white ring-orange-500' : 'bg-slate-50 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700' }}">
                            {{ $ability }}
                        </button>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Scopes control which endpoints your token can
                    call.</p>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="submit"
                    class="rounded-full bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">Generate
                    token</button>
            </div>
        </form>

        @if($plainToken)
            <div
                class="mt-4 rounded-xl bg-orange-50 p-4 text-sm text-orange-800 ring-1 ring-orange-100 dark:bg-slate-800 dark:text-orange-200 dark:ring-slate-700">
                <p class="font-semibold">Copy your new token now</p>
                <p class="mt-1 break-all text-xs">{{ $plainToken }}</p>
                <p class="mt-2 text-xs">You will not be able to see this token again.</p>
            </div>
        @endif
    </div>

    <div
        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80 dark:bg-slate-900/70 dark:ring-slate-800/80">
        <div class="flex items-center justify-between gap-2">
            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Existing tokens</p>
            <span class="pill-soft">{{ $tokens->count() }} active</span>
        </div>

        <div class="mt-3 space-y-3">
            @forelse($tokens as $token)
                <div
                    class="flex items-start justify-between gap-3 rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200 dark:bg-slate-800/50 dark:ring-slate-700">
                    <div class="space-y-1 text-sm text-slate-700 dark:text-slate-200">
                        <p class="font-semibold">{{ $token->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Created
                            {{ $token->created_at->diffForHumans() }} · Last used
                            {{ optional($token->last_used_at)->diffForHumans() ?? 'never' }}</p>
                        <div class="flex flex-wrap gap-2 text-[11px]">
                            @foreach($token->abilities ?? [] as $ability)
                                <span class="pill-soft">{{ $ability }}</span>
                            @endforeach
                        </div>
                    </div>
                    <button type="button" wire:click="revoke({{ $token->id }})"
                        class="text-xs font-semibold text-red-600 hover:text-red-700">Revoke</button>
                </div>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-400">No tokens yet.</p>
            @endforelse
        </div>
    </div>
</section>
