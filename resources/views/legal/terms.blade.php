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
                    <a href="{{ localized_route('legal.terms') }}" class="underline underline-offset-4">Terms</a>
                    <a href="{{ localized_route('legal.privacy') }}" class="hover:text-white">Privacy</a>
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
                <h1 class="text-3xl font-bold text-[#004777]">Syarat dan Ketentuan Penggunaan</h1>
                <p class="text-[#004777]/75">Terakhir diperbarui: Juni 2026</p>
            </header>

            <p>Selamat datang di Miracle Institute.</p>
            <p>Miracle Institute merupakan platform pembelajaran dan pemuridan berbasis digital yang menyediakan program pembelajaran, pelatihan, mentoring, komunitas belajar, dan pengembangan kepemimpinan Kristen bagi peserta dari berbagai negara, budaya, bahasa, dan denominasi gereja.</p>
            <p>Dengan mengakses atau menggunakan platform ini, pengguna dianggap telah membaca, memahami, dan menyetujui seluruh ketentuan yang berlaku.</p>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Status Platform</h2>
                <p>Platform ini diselenggarakan oleh Miracle Institute sebagai penyedia layanan pembelajaran dan pemuridan.</p>
                <p>Teknologi platform, perangkat lunak, desain sistem, kode program, arsitektur aplikasi, dan komponen teknologi terkait dikembangkan serta dimiliki hak ciptanya oleh RIG EduTech BINUS University.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Registrasi dan Akun Pengguna</h2>
                <p>Pengguna wajib memberikan informasi yang benar, akurat, dan terkini saat melakukan registrasi.</p>
                <p>Pengguna bertanggung jawab menjaga keamanan akun, kata sandi, dan seluruh aktivitas yang dilakukan melalui akun tersebut.</p>
                <p>Miracle Institute berhak menolak, membatasi, menangguhkan, atau menghapus akun yang terbukti memberikan informasi palsu atau melanggar ketentuan penggunaan.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Penggunaan yang Diperbolehkan</h2>
                <p>Platform digunakan untuk tujuan:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Pembelajaran dan pengembangan diri;</li>
                    <li>Pemuridan dan pelayanan;</li>
                    <li>Diskusi akademik dan rohani;</li>
                    <li>Pengembangan kepemimpinan Kristen;</li>
                    <li>Aktivitas lain yang sejalan dengan tujuan platform.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Larangan Penggunaan</h2>
                <p>Pengguna dilarang:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Menggunakan platform untuk aktivitas yang melanggar hukum;</li>
                    <li>Menyebarkan ujaran kebencian, diskriminasi, pelecehan, atau perundungan;</li>
                    <li>Mengganggu keamanan dan stabilitas sistem;</li>
                    <li>Mengakses akun pengguna lain tanpa izin;</li>
                    <li>Menyebarkan malware atau aktivitas siber yang merugikan;</li>
                    <li>Menyalin, menjual, atau mendistribusikan materi tanpa izin;</li>
                    <li>Melakukan reverse engineering, dekompilasi, scraping, cloning, reproduksi sistem, atau upaya lain untuk memperoleh teknologi platform.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Perbedaan Doktrin dan Denominasi</h2>
                <p>Platform ini melayani peserta dari berbagai denominasi dan tradisi gereja.</p>
                <p>Materi yang tersedia dimaksudkan sebagai sarana pembelajaran dan pertumbuhan rohani. Tidak semua pandangan yang disampaikan harus dipahami sebagai representasi resmi seluruh gereja atau denominasi Kristen.</p>
                <p>Setiap pengguna diharapkan menghormati keberagaman pandangan teologis dan menjaga interaksi yang sehat serta membangun.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Hak Kekayaan Intelektual</h2>
                <h3 class="text-lg font-semibold text-slate-950">Hak Teknologi</h3>
                <p>Seluruh teknologi platform, termasuk namun tidak terbatas pada:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Source code;</li>
                    <li>Database structure;</li>
                    <li>Learning Management System;</li>
                    <li>User interface dan user experience;</li>
                    <li>Framework sistem;</li>
                    <li>Integrasi aplikasi;</li>
                    <li>Dokumentasi teknis;</li>
                    <li>Algoritma dan fitur digital;</li>
                </ul>
                <p>merupakan hak cipta dan kekayaan intelektual RIG EduTech BINUS University.</p>

                <h3 class="text-lg font-semibold text-slate-950">Hak Konten</h3>
                <p>Materi pembelajaran, modul, video, artikel, kurikulum, bahan pelatihan, sertifikat, logo, dan konten lain yang diterbitkan melalui platform merupakan hak milik Miracle Institute atau pihak yang memberikan lisensi kepada Miracle Institute.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Pengembangan dan Perubahan Layanan</h2>
                <p>Karena platform masih terus dikembangkan, Miracle Institute dan RIG EduTech BINUS University dapat:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Menambah fitur baru;</li>
                    <li>Mengubah tampilan sistem;</li>
                    <li>Memperbarui teknologi;</li>
                    <li>Mengintegrasikan layanan pihak ketiga;</li>
                    <li>Mengubah model pembelajaran;</li>
                    <li>Menghentikan fitur tertentu;</li>
                </ul>
                <p>tanpa pemberitahuan sebelumnya apabila diperlukan untuk peningkatan layanan, keamanan, atau kepatuhan terhadap peraturan yang berlaku.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Batas Tanggung Jawab</h2>
                <p>Platform disediakan sebagaimana adanya (as is) dan sesuai ketersediaan (as available).</p>
                <p>Miracle Institute maupun RIG EduTech BINUS University tidak menjamin bahwa layanan akan selalu bebas dari gangguan, kesalahan sistem, kehilangan data, serangan siber, atau gangguan teknis lainnya yang berada di luar kendali yang wajar.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Penutupan Akun</h2>
                <p>Pengguna dapat mengajukan penghapusan akun.</p>
                <p>Miracle Institute berhak menangguhkan atau menghapus akun yang melanggar ketentuan ini tanpa kewajiban memberikan kompensasi.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Perubahan Ketentuan</h2>
                <p>Ketentuan ini dapat diperbarui sewaktu-waktu mengikuti perkembangan layanan, teknologi, kebutuhan organisasi, maupun perubahan regulasi.</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Pedoman Komunitas</h2>
                <p>Setiap pengguna diharapkan:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Menghormati sesama peserta;</li>
                    <li>Menghargai perbedaan denominasi dan tradisi gereja;</li>
                    <li>Menjaga etika komunikasi;</li>
                    <li>Menghindari perdebatan yang menyerang pribadi;</li>
                    <li>Menggunakan platform secara bertanggung jawab.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-slate-950">Disclaimer Pelayanan</h2>
                <p>Materi yang tersedia bertujuan untuk:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Pendidikan Kristen;</li>
                    <li>Pemuridan;</li>
                    <li>Pengembangan kepemimpinan;</li>
                    <li>Pembinaan rohani.</li>
                </ul>
                <p>Materi yang tersedia tidak dimaksudkan sebagai:</p>
                <ul class="list-disc space-y-1 pl-6">
                    <li>Nasihat hukum;</li>
                    <li>Nasihat medis;</li>
                    <li>Konseling psikologis profesional;</li>
                    <li>Pengganti pendampingan pastoral lokal;</li>
                    <li>Pengganti keputusan pribadi maupun organisasi.</li>
                </ul>
                <p>Pengguna tetap bertanggung jawab atas setiap keputusan yang diambil berdasarkan informasi yang diperoleh melalui platform.</p>
            </section>
                </article>
            </div>
        </main>

        @include('layouts.partials.footer')
    </div>
</body>
</html>
