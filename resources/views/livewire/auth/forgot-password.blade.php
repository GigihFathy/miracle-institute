<div class="max-w-md mx-auto bg-white border rounded-2xl p-6 space-y-5">
    <div>
        <h1 class="text-2xl font-bold">Forgot Password</h1>
        <p class="text-sm text-gray-500">Masukkan email untuk menerima link reset.</p>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <input type="email" required autocomplete="email" wire:model.debounce.300ms="email"
                   placeholder="Email" class="w-full border rounded-lg px-4 py-2">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full bg-primary text-white rounded-lg py-2 font-medium">
            Send reset link
        </button>
    </form>

    <div class="text-sm text-center">
        <a href="{{ route('login') }}" class="underline">Back to login</a>
    </div>
</div>