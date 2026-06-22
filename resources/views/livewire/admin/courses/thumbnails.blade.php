<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <x-ui.page-header
        title="{{ __('admin.course_thumbnails.page_title') }}"
        subtitle="{{ __('admin.course_thumbnails.page_subtitle') }}"
    >
        <div class="flex flex-wrap gap-2">
            <a
                href="{{ localized_route('admin.courses.index') }}"
                class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50"
            >
                {{ __('admin.course_thumbnails.actions.back_to_courses') }}
            </a>
        </div>
    </x-ui.page-header>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-500">{{ __('admin.course_thumbnails.stats.total') }}</div>
            <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['total'] }}</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-500">{{ __('admin.course_thumbnails.stats.used') }}</div>
            <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['used'] }}</div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-500">{{ __('admin.course_thumbnails.stats.unused') }}</div>
            <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['unused'] }}</div>
        </div>
    </section>

    <section class="rounded-2xl border bg-white p-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_440px] lg:items-start">
            <div class="max-w-2xl">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.course_thumbnails.upload.title') }}</h2>
                <p class="mt-1 text-sm leading-6 text-slate-500">{{ __('admin.course_thumbnails.upload.subtitle') }}</p>

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 px-4 py-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Format</div>
                        <div class="mt-1 text-sm text-slate-700">JPG, JPEG, PNG, WEBP</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 px-4 py-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Max Size</div>
                        <div class="mt-1 text-sm text-slate-700">4 MB</div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                <form
                    action="{{ localized_route('admin.courses.thumbnails.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-3"
                >
                    @csrf

                    <label class="block text-sm font-medium text-slate-700">
                        {{ __('admin.course_thumbnails.actions.upload') }}
                    </label>

                    <input
                        type="file"
                        name="thumbnail"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="block w-full rounded-xl border border-slate-200 bg-white text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium"
                    >

                    <p class="text-xs leading-5 text-slate-500">
                        Pilih gambar thumbnail baru untuk ditambahkan ke library template topik pembelajaran.
                    </p>

                    @error('thumbnail')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end pt-1">
                        <button
                            type="submit"
                            class="admin-primary-button rounded-xl px-4 py-2 text-sm"
                        >
                            {{ __('admin.course_thumbnails.actions.upload') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border bg-white p-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.course_thumbnails.library.title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('admin.course_thumbnails.library.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($thumbnails as $thumbnail)
                <div class="overflow-hidden rounded-2xl border bg-white">
                    <img
                        src="{{ course_thumbnail_url($thumbnail['path']) }}"
                        alt="{{ $thumbnail['name'] }}"
                        class="h-44 w-full object-cover"
                    >

                    <div class="space-y-3 p-4">
                        <div>
                            <div class="truncate text-sm font-medium text-slate-900">{{ $thumbnail['name'] }}</div>
                            <div class="mt-1 text-xs text-slate-500">
                                {{ $thumbnail['size_kb'] }} KB | {{ date('d M Y H:i', $thumbnail['updated_at']) }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm text-slate-600">
                                {{ trans_choice('admin.course_thumbnails.library.used_by', $thumbnail['usage_count'], ['count' => $thumbnail['usage_count']]) }}
                            </div>

                            @if($thumbnail['usage_count'] > 0)
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        {{ __('admin.course_thumbnails.library.in_use') }}
                                    </span>
                                </div>
                            @else
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="delete('{{ $thumbnail['path'] }}')"
                                        onclick="confirm('{{ __('admin.course_thumbnails.confirm_delete') }}') || event.stopImmediatePropagation()"
                                        class="rounded-lg px-3 py-2 text-sm font-medium text-rose-600 transition hover:bg-rose-50"
                                    >
                                        {{ __('admin.course_thumbnails.actions.delete') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed px-6 py-10 text-center text-slate-500">
                    {{ __('admin.course_thumbnails.empty') }}
                </div>
            @endforelse
        </div>
    </section>
</div>
