<div class="max-w-md mx-auto bg-white border rounded-2xl p-6 space-y-5">
    <div>
        <h1 class="text-2xl font-bold">Login</h1>
        <p class="text-sm text-gray-500">Masuk untuk melanjutkan.</p>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @error('oauth')
        <div class="rounded-lg bg-red-50 text-red-700 px-4 py-3 text-sm">
            {{ $message }}
        </div>
    @enderror

    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <input
                type="email"
                required
                autocomplete="email"
                wire:model.debounce.300ms="email"
                placeholder="Email"
                class="w-full border rounded-lg px-4 py-2"
            >
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <input
                type="password"
                required
                minlength="8"
                autocomplete="current-password"
                wire:model.debounce.300ms="password"
                placeholder="Password"
                class="w-full border rounded-lg px-4 py-2"
            >
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="remember">
            Remember me
        </label>

        <button type="submit" class="w-full bg-primary text-white rounded-lg py-2 font-medium">
            Login
        </button>
    </form>

    <div class="space-y-3">
        <div class="flex items-center justify-between text-sm">
            <a href="{{ route('password.request') }}" class="underline">Forgot password?</a>
            <a href="{{ route('register') }}" class="underline">Create account</a>
        </div>
    </div>
</div>