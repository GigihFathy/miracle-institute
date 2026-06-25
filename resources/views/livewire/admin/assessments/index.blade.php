<div class="space-y-6">
    <x-ui.page-header
        title="{{ __('admin.assessments.page_title') }}"
        subtitle="{{ __('admin.assessments.page_subtitle') }}"
    >
        <div class="flex items-center gap-2">
            @if($selectedCourse)
                <a href="{{ localized_route('admin.courses.index') }}"
                   class="rounded-xl border px-4 py-2 text-sm">
                    {{ __('admin.course_thumbnails.actions.back_to_courses') }}
                </a>
            @endif

            @if($selectedAssessment)
                <button wire:click="edit('{{ $selectedAssessment->id }}')"
                    class="admin-edit-button rounded-xl border px-4 py-2 text-sm">
                    {{ __('admin.assessments.actions.edit') }}
                </button>
                <button wire:click="confirmDelete('{{ $selectedAssessment->id }}')"
                    class="admin-delete-button rounded-xl px-4 py-2 text-sm">
                    {{ __('admin.assessments.actions.delete') }}
                </button>

                <button wire:click="createQuestion"
                    class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 text-sm transition">
                    {{ __('admin.question_manager.actions.create') }}
                </button>
            @else
                <button wire:click="create"
                    class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 text-sm transition">
                    {{ __('admin.assessments.actions.create') }}
                </button>
            @endif
        </div>
    </x-ui.page-header>

    @if($selectedCourse)
        <div class="rounded-2xl border bg-white p-5 space-y-4">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-500">{{ __('admin.assessments.table.course') }}</div>
                <div class="text-xl font-semibold text-slate-900">{{ $selectedCourse->title }}</div>
            </div>

            @if($selectedAssessment)
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">{{ __('admin.assessments.table.assessment') }}</div>
                        <div class="mt-1 font-semibold">{{ $selectedAssessment->title }}</div>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">{{ __('admin.question_manager.stats.passing_grade') }}</div>
                        <div class="mt-1 font-semibold">{{ $selectedAssessment->passing_grade }}</div>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4">
                        <div class="text-xs text-slate-500">{{ __('admin.question_manager.stats.questions') }}</div>
                        <div class="mt-1 font-semibold">{{ $selectedAssessment->questions->count() }}</div>
                        <div class="mt-1 text-xs text-slate-500">
                            {{ __('admin.assessments.summary.question_count', [
                                'count' => $selectedAssessment->question_limit
                                    ? min($selectedAssessment->question_limit, $selectedAssessment->questions->count())
                                    : $selectedAssessment->questions->count(),
                            ]) }}
                        </div>
                    </div>
                </div>

                @if($selectedAssessment->available_from)
                    <div class="flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        @if($selectedAssessment->isAvailable())
                            Soal <strong>sudah dapat diakses</strong> sejak {{ $selectedAssessment->available_from->translatedFormat('d M Y H:i') }}.
                        @else
                            Soal baru dapat diakses mulai <strong>{{ $selectedAssessment->available_from->translatedFormat('d M Y H:i') }}</strong>. Peserta belum bisa mengerjakan soal atau mengklaim sertifikat.
                        @endif
                    </div>
                @endif

                <x-ui.table-shell class="table-auto">
                    <table class="w-full text-sm">
                        <thead class="admin-table-head text-left">
                            <tr>
                                <th class="p-4">Urutan Soal</th>
                                <th class="p-4">Pertanyaan</th>
                                <th class="p-4">Opsi</th>
                                <th class="p-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selectedAssessment->questions as $question)
                                <tr class="border-t align-top">
                                    <td class="p-4">
                                        <div class="font-medium text-slate-900">{{ $question->question }}</div>
                                    </td>
                                    <td class="space-y-1 p-4 text-xs text-slate-700">
                                        @foreach($question->options as $option)
                                            <div class="{{ $option->is_correct ? 'font-semibold text-emerald-600' : '' }}">
                                                {{ $option->is_correct ? '✓' : '•' }} {{ $option->option_text }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="p-4 text-center">{{ $question->sort_order }}</td>
                                    <td class="p-4">
                                        <div class="flex flex-wrap gap-2">
                                            <div class="relative group">
                                                <button wire:click="editQuestion('{{ $question->id }}')"
                                                    class="admin-edit-button inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                                                    title="{{ __('admin.question_manager.actions.edit') }}">
                                                    <span class="sr-only">{{ __('admin.question_manager.actions.edit') }}</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a2.25 2.25 0 1 1 3.182 3.182L10.582 17.13a4.5 4.5 0 0 1-1.897 1.13L6 19l.74-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 4.487ZM16.862 4.487 19.5 7.125" />
                                                    </svg>
                                                </button>
                                                <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border border-brand-dark/20 bg-white px-2 py-1 text-[11px] font-medium text-brand-dark opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                                    {{ __('admin.question_manager.actions.edit') }}
                                                </span>
                                            </div>

                                            <div class="relative group">
                                                <button wire:click="deleteQuestion('{{ $question->id }}')"
                                                    class="admin-delete-button inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                                                    title="{{ __('admin.question_manager.actions.delete') }}">
                                                    <span class="sr-only">{{ __('admin.question_manager.actions.delete') }}</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084a2.25 2.25 0 0 1-2.245-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                                <span class="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md border border-brand-dark/20 bg-white px-2 py-1 text-[11px] font-medium text-brand-dark opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                                    {{ __('admin.question_manager.actions.delete') }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-slate-500">
                                        {{ __('admin.question_manager.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-ui.table-shell>
            @else
                <div class="rounded-2xl border border-dashed bg-slate-50 p-6 text-slate-600">
                    Belum ada assessment untuk topik pembelajaran ini.
                </div>
            @endif
        </div>
    @endif

    @if($showModal)
        <div
            wire:click.self="$set('showModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div class="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-2xl bg-white shadow-xl">

                <div class="flex items-center justify-between border-b p-5">
                    <h2 class="text-lg font-semibold">
                        {{ $editingId ? __('admin.assessments.modal.edit_title') : __('admin.assessments.modal.create_title') }}
                    </h2>

                    <button type="button" wire:click="$set('showModal', false)" class="text-slate-500 hover:text-black">
                        ✕
                    </button>
                </div>

                <div class="space-y-4 overflow-y-auto p-5">
                    <div class="rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
                        <span class="font-semibold text-rose-500">*</span> menandakan field wajib diisi.
                    </div>

                    @if($errors->any())
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Periksa kembali field yang wajib diisi.
                        </div>
                    @endif

                    @if($selectedCourse)
                        <input
                            value="{{ $selectedCourse->title }}"
                            disabled
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-600"
                        >
                        @error('course_id')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    @else
                        <div class="space-y-1">
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                Topik pembelajaran <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="course_id" class="w-full rounded-xl border px-4 py-2">
                            <option value="">{{ __('admin.assessments.form.select_course') }}</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" @disabled(!$editingId && $course->assessment)>
                                    {{ $course->title }}</option>
                            @endforeach
                            </select>
                            @error('course_id')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="space-y-1">
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Judul Assessment <span class="text-rose-500">*</span>
                        </label>
                        <input wire:model="title"
                            class="w-full rounded-xl border px-4 py-2"
                            placeholder="{{ __('admin.assessments.form.title_placeholder') }}">
                        @error('title')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                Passing Grade <span class="text-rose-500">*</span>
                            </label>
                            <input wire:model="passing_grade" type="number"
                                class="w-full rounded-xl border px-4 py-2"
                                placeholder="{{ __('admin.assessments.form.passing_grade_placeholder') }}">
                            @error('passing_grade')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                Soal ditampilkan
                            </label>
                            <input wire:model="question_limit" type="number"
                                class="w-full rounded-xl border px-4 py-2"
                                placeholder="{{ __('admin.assessments.form.question_limit_placeholder') }}">
                            @error('question_limit')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="status"
                            class="w-full rounded-xl border px-4 py-2">
                            <option value="active">{{ __('admin.assessments.status.active') }}</option>
                            <option value="inactive">{{ __('admin.assessments.status.inactive') }}</option>
                            <option value="draft">{{ __('admin.assessments.status.draft') }}</option>
                        </select>
                        @error('status')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                            Tersedia mulai tanggal
                        </label>
                        <input wire:model="available_from" type="datetime-local"
                            class="w-full rounded-xl border px-4 py-2">
                        <p class="mt-1 text-[11px] text-slate-500">
                            Kosongkan jika soal dapat diakses kapan saja. Jika diisi, peserta tidak bisa mengerjakan soal atau mengklaim sertifikat sebelum tanggal ini.
                        </p>
                        @error('available_from')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t bg-slate-50 p-5">
                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="rounded-xl border px-4 py-2">
                        {{ __('admin.assessments.actions.cancel') }}
                    </button>

                    <button wire:click="save"
                        class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 transition">
                        {{ __('admin.assessments.actions.save') }}
                    </button>
                </div>

            </div>
        </div>
    @endif

    @if($questionModalOpen)
        <div
            wire:click.self="$set('questionModalOpen', false)"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div class="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-2xl bg-white shadow-xl">

                <div class="flex items-center justify-between border-b p-5">
                    <h2 class="text-lg font-semibold">
                        {{ $questionEditingId ? __('admin.question_manager.modal.edit_title') : __('admin.question_manager.modal.create_title') }}
                    </h2>

                    <button type="button" wire:click="$set('questionModalOpen', false)" class="text-slate-500 hover:text-black">
                        ✕
                    </button>
                </div>

                <div class="space-y-4 overflow-y-auto p-5">
                    <div class="rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
                        <span class="font-semibold text-rose-500">*</span> menandakan field wajib diisi.
                    </div>

                    @if($errors->any())
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Periksa kembali field pertanyaan yang wajib diisi.
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                Pertanyaan <span class="text-rose-500">*</span>
                            </label>
                            <textarea wire:model="question_text"
                                rows="4"
                                class="w-full rounded-xl border px-4 py-2"
                                placeholder="{{ __('admin.question_manager.form.question_placeholder') }}"></textarea>
                            @error('question_text')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <div class="text-sm font-medium">{{ __('admin.question_manager.form.options_title') }} <span class="text-rose-500">*</span></div>

                            @foreach($question_options as $i => $opt)
                                <div class="flex items-center gap-3">
                                    <button
                                        type="button"
                                        wire:click="$set('question_correctIndex', {{ $i }})"
                                        class="flex h-5 w-5 items-center justify-center rounded border {{ $question_correctIndex === $i ? 'bg-emerald-600 text-white' : 'bg-white' }}"
                                    >
                                        @if($question_correctIndex === $i) ✓ @endif
                                    </button>

                                    <input type="text"
                                        wire:model="question_options.{{ $i }}.option_text"
                                        class="w-full rounded-lg border px-3 py-2"
                                        placeholder="{{ __('admin.question_manager.form.option_placeholder', ['number' => $i + 1]) }}">
                                </div>
                                @error('question_options.' . $i . '.option_text')
                                    <p class="text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            @endforeach
                            @error('question_correctIndex')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                Nomor Urut Soal
                            </label>
                            <input wire:model="question_sort_order"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border px-4 py-2"
                                placeholder="{{ __('admin.question_manager.form.sort_order_placeholder') }}">
                            <p class="mt-1 text-[11px] leading-relaxed text-slate-500">
                                Menentukan posisi soal di dalam assessment. Saat membuat soal baru, sistem otomatis mengisi nomor urut berikutnya dari soal terakhir.
                            </p>
                            @error('question_sort_order')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t bg-slate-50 p-5">
                    <button type="button" wire:click="$set('questionModalOpen', false)"
                        class="rounded-xl border px-4 py-2">
                        {{ __('admin.question_manager.actions.cancel') }}
                    </button>

                    <button wire:click="saveQuestion"
                        class="admin-primary-button rounded-xl border border-brand-dark/20 px-4 py-2 transition">
                        {{ __('admin.question_manager.actions.save') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ __('admin.assessments.delete.title') }}
                </h2>
                <p class="mt-2 text-sm text-slate-600">
                    {{ __('admin.assessments.delete.subtitle') }}
                </p>

                <div class="mt-6 flex justify-end gap-2">
                    <button
                        type="button"
                        wire:click="$set('showDeleteModal', false)"
                        class="rounded-xl border px-4 py-2"
                    >
                        {{ __('admin.assessments.actions.cancel') }}
                    </button>

                    <button wire:click="delete" class="admin-delete-button rounded-xl px-4 py-2">
                        {{ __('admin.assessments.actions.delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
