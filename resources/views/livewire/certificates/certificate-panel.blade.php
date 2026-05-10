<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl scale-[0.9] origin-top px-4 py-4 sm:px-6 lg:px-8">

        {{-- HERO --}}
        <section class="border-b border-slate-200 bg-white">
            <div class="px-4 py-8 sm:px-6 lg:px-8">
                <div class="max-w-2xl space-y-2.5">
                    <div class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-[11px] font-medium uppercase tracking-[0.18em] text-slate-500">
                        Student Certificate Center
                    </div>

                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Your Learning Certificates
                    </h1>

                    <p class="max-w-xl text-sm leading-6 text-slate-600">
                        Seluruh sertifikat pembelajaran course yang telah berhasil diselesaikan akan muncul di halaman ini.
                    </p>
                </div>
            </div>
        </section>

        {{-- CONTENT --}}
        <section class="py-5">
            {{-- FILTER --}}
            <div class="mb-4 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 lg:flex-row lg:items-center lg:gap-4">

                {{-- SEARCH --}}
                <div class="min-w-0 flex-1">
                    <input type="search"
                        wire:model.debounce.300ms="search"
                        placeholder="Search certificate number..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm outline-none transition focus:border-slate-900 focus:bg-white">
                </div>

                {{-- PER PAGE --}}
                <div class="flex shrink-0 lg:ml-auto">
                    <select wire:model.live="perPage"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm outline-none focus:border-slate-900 lg:w-auto">
                        <option value="9">9 Per Page</option>
                        <option value="12">12 Per Page</option>
                        <option value="24">24 Per Page</option>
                    </select>
                </div>

            </div>

            {{-- GRID --}}
            <div class="grid grid-cols-[repeat(auto-fit,minmax(230px,1fr))] gap-3">
                @forelse($certificates as $certificate)
                    <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-sm">

                        {{-- HEADER --}}
                        <div class="border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white p-4">
                            <div class="space-y-2">
                                <div class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-emerald-700">
                                    {{ $certificate->status }}
                                </div>

                                <h2 class="text-sm font-semibold leading-5 text-slate-900">
                                    {{ $certificate->course?->title ?? 'Course Certificate' }}
                                </h2>
                            </div>
                        </div>

                        {{-- BODY --}}
                        <div class="space-y-3 p-4">
                            <div class="space-y-1">
                                <div class="text-[10px] font-medium uppercase tracking-[0.16em] text-slate-400">
                                    Issued Date
                                </div>

                                <div class="text-sm font-medium text-slate-700">
                                    {{ $certificate->issued_at?->format('d F Y') ?? '-' }}
                                </div>
                            </div>

                            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3">
                                <div class="text-[10px] uppercase tracking-[0.14em] text-slate-400">
                                    Certificate ID
                                </div>

                                <div class="mt-1 break-all text-sm font-medium text-slate-700">
                                    {{ $certificate->certificate_number }}
                                </div>
                            </div>

                            @if($certificate->file_path)
                                <a href="{{ route('certificates.download', $certificate->id) }}"
                                   class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                                    Download Certificate
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full">
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100">
                                <svg class="h-6 w-6 text-slate-400"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                </svg>
                            </div>

                            <h3 class="text-base font-semibold text-slate-900">
                                No Certificates Yet
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                Sertifikat akan tersedia setelah kamu menyelesaikan course pembelajaran.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            <div class="mt-5">
                {{ $certificates->links() }}
            </div>
        </section>

    </div>
</div>