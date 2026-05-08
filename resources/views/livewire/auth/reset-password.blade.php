<div class="max-w-md mx-auto bg-white border rounded-2xl p-6 space-y-5">
    <div>
        <h1 class="text-2xl font-bold">Reset Password</h1>
        <p class="text-sm text-gray-500">Buat password baru.</p>
    </div>

    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <input type="email" required autocomplete="email" wire:model.debounce.300ms="email"
                   placeholder="Email" class="w-full border rounded-lg px-4 py-2">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="password" required minlength="8" autocomplete="new-password"
                   wire:model.debounce.300ms="password" placeholder="New password"
                   class="w-full border rounded-lg px-4 py-2">
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="password" required minlength="8" autocomplete="new-password"
                   wire:model.debounce.300ms="password_confirmation"
                   placeholder="Confirm new password" class="w-full border rounded-lg px-4 py-2">
            @error('password_confirmation') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full bg-primary text-white rounded-lg py-2 font-medium">
            Reset password
        </button>
    </form>
</div>