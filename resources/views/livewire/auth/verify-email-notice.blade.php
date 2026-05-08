<div class="max-w-md mx-auto bg-white border rounded-2xl p-6 space-y-5">
    <div>
        <h1 class="text-2xl font-bold">Verify your email</h1>
        <p class="text-sm text-gray-500">
            Cek inbox email kamu. Jika belum menerima, kirim ulang link verifikasi.
        </p>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <button wire:click="resend" class="w-full bg-primary text-white rounded-lg py-2 font-medium">
        Resend verification email
    </button>
</div>