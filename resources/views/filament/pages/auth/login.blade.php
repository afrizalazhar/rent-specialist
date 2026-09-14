<main class="w-full max-w-md">
    <div class="neu-surface p-8 sm:p-10">
        <div class="mb-8 text-center">
            <h1 class="font-display text-2xl sm:text-3xl font-semibold text-surface-900 tracking-tight">
                {{ config('business.business.name') }}
            </h1>
            <p class="mt-2 text-sm text-surface-600">
                Masuk ke dashboard admin
            </p>
        </div>

        <form wire:submit.prevent="authenticate" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-surface-700 mb-1.5">
                    Email
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    wire:model="data.email"
                    class="neu-input"
                    required
                    autofocus
                    autocomplete="email"
                >
                @error('data.email')
                    <p class="mt-1.5 text-sm text-rust-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-surface-700 mb-1.5">
                    Password
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    wire:model="data.password"
                    class="neu-input"
                    required
                    autocomplete="current-password"
                >
                @error('data.password')
                    <p class="mt-1.5 text-sm text-rust-500">{{ $message }}</p>
                @enderror
            </div>

            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                <span class="relative">
                    <input
                        type="checkbox"
                        name="remember"
                        wire:model="data.remember"
                        class="peer sr-only"
                    >
                    <span class="block w-11 h-6 rounded-full bg-surface-200 shadow-neu-inset-sm transition-colors peer-checked:bg-surface-900"></span>
                    <span class="absolute left-1 top-1 w-4 h-4 rounded-full bg-surface-50 shadow-neu-sm transition-transform peer-checked:translate-x-5"></span>
                </span>
                <span class="text-sm font-medium text-surface-700">Ingat saya</span>
            </label>

            <button
                type="submit"
                class="neu-btn-primary w-full justify-center py-3 text-base"
            >
                Masuk
            </button>
        </form>
    </div>
</main>
