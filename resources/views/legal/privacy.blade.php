<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.seo')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-[#004777]">
    <div class="flex min-h-screen flex-col">
        <header class="border-b border-slate-200 bg-[#004777] text-white">
            <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ localized_route('explore.dashboard') }}" class="text-sm font-semibold tracking-wide">
                    Miracle Institute
                </a>
                <nav class="flex items-center gap-4 text-sm text-white/85">
                    <a href="{{ localized_route('legal.terms') }}" class="hover:text-white">Terms</a>
                    <a href="{{ localized_route('legal.privacy') }}" class="underline underline-offset-4">Privacy</a>
                </nav>
            </div>
        </header>

        <main class="flex-1 px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
            <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <button
                        type="button"
                        onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='{{ localized_route('explore.dashboard') }}'; }"
                        class="text-sm font-medium text-[#004777] underline underline-offset-4"
                    >
                        Kembali
                    </button>
                </div>
                <article class="space-y-6 text-sm leading-7 text-slate-800">
            <header class="space-y-2 border-b border-[#004777]/15 pb-5">
                <h1 class="text-3xl font-bold text-[#004777]">Kebijakan Privasi</h1>
                <p class="text-[#004777]/75">Terakhir diperbarui: Juni 2026</p>
            </header>

            <p>Miracle Institute berkomitmen melindungi privasi dan keamanan data seluruh pengguna platform.</p>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Data yang Dikumpulkan</h2>
                <p>Platform dapat mengumpulkan informasi berikut:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Nama lengkap;</li>
                    <li>Alamat email;</li>
                    <li>Nomor telepon;</li>
                    <li>Negara atau wilayah domisili;</li>
                    <li>Informasi organisasi atau gereja (jika diberikan);</li>
                    <li>Aktivitas pembelajaran;</li>
                    <li>Riwayat topik pembelajaran;</li>
                    <li>Nilai dan progres belajar;</li>
                    <li>Data perangkat dan browser;</li>
                    <li>Alamat IP;</li>
                    <li>Cookies;</li>
                    <li>Log aktivitas sistem.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Tujuan Penggunaan Data</h2>
                <p>Data digunakan untuk:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Menyediakan layanan pembelajaran;</li>
                    <li>Mengelola akun pengguna;</li>
                    <li>Memantau perkembangan belajar;</li>
                    <li>Menerbitkan sertifikat;</li>
                    <li>Menyediakan dukungan teknis;</li>
                    <li>Meningkatkan kualitas layanan;</li>
                    <li>Menjaga keamanan sistem;</li>
                    <li>Melakukan analisis penggunaan platform;</li>
                    <li>Memenuhi kewajiban hukum yang berlaku.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Pengelola Data</h2>
                <p>Miracle Institute bertindak sebagai penyelenggara layanan dan pengelola utama data pengguna.</p>
                <p>RIG EduTech BINUS University bertindak sebagai pengembang dan pengelola teknologi yang dapat memperoleh akses terhadap data yang diperlukan untuk pemeliharaan, keamanan, pengembangan, dan operasional sistem.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Penyimpanan dan Keamanan Data</h2>
                <p>Kami menerapkan langkah-langkah keamanan yang wajar untuk melindungi data pengguna dari akses tidak sah, kehilangan, penyalahgunaan, perubahan, maupun pengungkapan yang tidak sah.</p>
                <p>Namun tidak ada sistem digital yang dapat dijamin sepenuhnya bebas risiko.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Pembagian Data</h2>
                <p>Kami tidak menjual data pribadi pengguna.</p>
                <p>Data hanya dapat dibagikan kepada:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Penyedia cloud dan infrastruktur teknologi;</li>
                    <li>Penyedia autentikasi dan keamanan;</li>
                    <li>Penyedia layanan analitik;</li>
                    <li>Mitra layanan yang mendukung operasional platform;</li>
                    <li>Otoritas yang berwenang berdasarkan ketentuan hukum.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Transfer Data Internasional</h2>
                <p>Karena pengguna dan infrastruktur teknologi dapat berada di berbagai negara, data dapat diproses atau disimpan pada pusat data yang berlokasi di luar negara tempat pengguna berada.</p>
                <p>Dengan menggunakan platform ini, pengguna menyetujui kemungkinan transfer data tersebut.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Hak Pengguna</h2>
                <p>Pengguna berhak:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Mengakses data pribadinya;</li>
                    <li>Memperbarui informasi akun;</li>
                    <li>Meminta koreksi data;</li>
                    <li>Meminta penghapusan akun;</li>
                    <li>Mengajukan pertanyaan terkait penggunaan data pribadi.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Cookies dan Teknologi Serupa</h2>
                <p>Platform dapat menggunakan cookies, token autentikasi, serta teknologi serupa untuk meningkatkan keamanan, pengalaman pengguna, dan performa layanan.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Retensi Data</h2>
                <p>Data pengguna dapat disimpan selama akun aktif atau selama diperlukan untuk kepentingan operasional, akademik, pelayanan, audit, keamanan sistem, dan kepatuhan hukum.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Perubahan Kebijakan Privasi</h2>
                <p>Kebijakan Privasi ini dapat diperbarui sewaktu-waktu mengikuti perkembangan layanan, teknologi, dan regulasi yang berlaku.</p>
            </section>
                </article>
            </div>
        </main>

        @include('layouts.partials.footer')
    </div>
</body>
</html>
