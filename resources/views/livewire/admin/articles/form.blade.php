@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <style>
        .ql-font-inter { font-family: 'Inter', sans-serif; }

        .editor-container {
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .ql-toolbar.ql-snow {
            border: none !important;
            border-bottom: 1px solid #e2e8f0 !important;
            background: #f8fafc;
            padding: 12px !important;
        }

        .ql-container.ql-snow {
            border: none !important;
            min-height: 500px;
            font-size: 16px;
        }

        .ql-editor {
            line-height: 1.8;
            padding: 24px !important;
        }

        .truncate-fix {
            white-space: normal;
            word-break: break-word;
        }

        input, select, textarea {
            transition: all 0.2s ease-in-out;
            outline: none !important;
        }

        input:focus, select:focus {
            border-color: #0f172a !important;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.1);
            background-color: #fff;
        }

        input:hover, select:hover {
            border-color: #cbd5e1;
        }

        input[type="file"]::file-selector-button {
            transition: all 0.2s ease-in-out;
        }

        input[type="file"]::file-selector-button:hover {
            background-color: #1e293b;
        }

        .upload-zone {
            transition: all 0.3s ease;
        }
        .upload-zone:hover {
            border-color: #0f172a;
            background-color: #f8fafc;
        }

        select {
            background-image: none !important;
        }

        .ql-editor h1 { font-size: 2em; font-weight: bold; margin-bottom: 0.5em; }
        .ql-editor h2 { font-size: 1.5em; font-weight: bold; margin-bottom: 0.5em; }
        .ql-editor h3 { font-size: 1.17em; font-weight: bold; margin-bottom: 0.5em; }

        .ql-editor ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
        .ql-editor ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }

        .ql-editor .ql-align-center { text-align: center; }
        .ql-editor .ql-align-right { text-align: right; }
        .ql-editor .ql-align-justify { text-align: justify; }

        .ql-editor li { margin-bottom: 0.25rem; }
    </style>
@endpush

<div class="space-y-6">
    <x-ui.page-header
        title="{{ $articleId ? __('admin.articles.form.edit_title') : __('admin.articles.form.create_title') }}"
        subtitle="{{ __('admin.articles.form.subtitle') }}"
    >
        <div class="flex items-center gap-3">
            <a href="{{ localized_route('admin.articles.index') }}"
               class="rounded-xl border bg-white px-5 py-2.5 text-sm font-medium transition hover:bg-slate-50">
                {{ __('admin.articles.actions.back') }}
            </a>

            <button type="button"
                    wire:click="openPreview"
                    class="rounded-xl border bg-white px-5 py-2.5 text-sm font-medium transition hover:bg-slate-50">
                {{ __('admin.articles.actions.preview') }}
            </button>

            <button form="article-form"
                    class="rounded-xl border border-brand-dark/20 bg-transparent px-5 py-2.5 text-sm font-medium text-brand-dark transition hover:bg-brand/10">
                {{ __('admin.articles.actions.save') }}
            </button>
        </div>
    </x-ui.page-header>

    <form id="article-form" wire:submit.prevent="save" class="space-y-6">
        <section class="space-y-8 rounded-2xl border bg-white p-8">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-center">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-slate-900">{{ __('admin.articles.meta.title') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('admin.articles.meta.subtitle') }}</p>
                </div>

                <div class="flex gap-4">
                    <div class="min-w-[100px] rounded-2xl border bg-slate-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('admin.articles.meta.mode') }}</p>
                        <p class="font-bold text-slate-700">{{ $articleId ? __('admin.articles.meta.mode_edit') : __('admin.articles.meta.mode_create') }}</p>
                    </div>
                    <div class="min-w-[100px] rounded-2xl border bg-slate-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('admin.articles.meta.image') }}</p>
                        <p class="font-bold text-slate-700">{{ $imageFile ? __('admin.articles.meta.selected') : __('admin.articles.meta.none') }}</p>
                    </div>
                    <div class="min-w-[100px] rounded-2xl border bg-slate-50 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('admin.articles.meta.status') }}</p>
                        <p class="font-bold text-slate-700">{{ strtoupper($status) }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="ml-1 text-sm font-semibold text-slate-700">{{ __('admin.articles.form.title_label') }}</label>
                <input
                    wire:model.blur="title"
                    type="text"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3.5 text-slate-900 placeholder:text-slate-400 focus:bg-white"
                    placeholder="{{ __('admin.articles.form.title_placeholder') }}"
                >
                @error('title') <span class="ml-1 text-xs font-medium text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3" x-data="{ uploadMode: 'local' }">
                <div class="space-y-3">
                    <label class="flex items-center gap-2 text-sm font-bold text-slate-800">
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ __('admin.articles.upload.thumbnail_preview') }}
                    </label>

                    <div class="relative flex aspect-video items-center justify-center overflow-hidden rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50 group">
                        @if($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL))
                            <img src="{{ $imageUrl }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" onerror="this.src='https://placehold.co'">
                        @elseif($this->imagePreviewUrl)
                            <img src="{{ $this->imagePreviewUrl }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="p-4 text-center">
                                <svg class="mx-auto mb-2 h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs font-medium text-slate-400">{{ __('admin.articles.upload.waiting_image') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="space-y-4">
                        <div class="flex w-fit rounded-2xl bg-slate-100 p-1">
                            <button type="button" @click="uploadMode = 'local'" :class="uploadMode === 'local' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'" class="rounded-xl px-4 py-2 text-xs font-bold transition">
                                {{ __('admin.articles.upload.local_file') }}
                            </button>
                            <button type="button" @click="uploadMode = 'url'" :class="uploadMode === 'url' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'" class="rounded-xl px-4 py-2 text-xs font-bold transition">
                                {{ __('admin.articles.upload.internet_url') }}
                            </button>
                        </div>

                        <div x-show="uploadMode === 'local'" x-transition class="space-y-3">
                            <label class="ml-1 flex items-center gap-2 text-sm font-bold text-slate-800">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                {{ __('admin.articles.upload.choose_local_file') }}
                            </label>
                            <div class="relative group">
                                <input type="file" wire:model="imageFile" class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0">
                                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 transition group-hover:border-slate-400 focus-within:ring-4 focus-within:ring-slate-900/5">
                                    <div class="flex items-center gap-3">
                                        <div class="rounded-xl border border-slate-100 bg-white p-2 shadow-sm">
                                            <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700">{{ __('admin.articles.upload.select_thumbnail') }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-slate-400">{{ __('admin.articles.upload.browse') }}</span>
                                </div>
                            </div>
                        </div>

                        <div x-show="uploadMode === 'url'" x-transition class="space-y-3">
                            <label class="ml-1 flex items-center gap-2 text-sm font-bold text-slate-800">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.8a3.736 3.736 0 011.897-1.13L15 8.25l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"></path></svg>
                                {{ __('admin.articles.upload.paste_image_url') }}
                            </label>
                            <input wire:model.live="imageUrl"
                                type="url"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm transition outline-none focus:bg-white focus:ring-4 focus:ring-slate-900/5"
                                placeholder="{{ __('admin.articles.upload.image_url_placeholder') }}">
                            <p class="ml-1 text-[10px] text-slate-400">{{ __('admin.articles.upload.url_note') }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="ml-1 flex items-center gap-2 text-sm font-bold text-slate-800">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ __('admin.articles.upload.publication_status') }}
                        </label>
                        <div class="relative group">
                            <select wire:model.live="status" class="w-full cursor-pointer rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-semibold transition appearance-none focus:bg-white">
                                <option value="draft">{{ __('admin.articles.status.draft') }}</option>
                                <option value="active">{{ __('admin.articles.status.active') }}</option>
                                <option value="inactive">{{ __('admin.articles.status.inactive') }}</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-6 rounded-2xl border bg-white p-8">
            <div class="space-y-1">
                <h2 class="text-xl font-bold text-slate-900">{{ __('admin.articles.content.title') }}</h2>
                <p class="text-sm text-slate-500">{{ __('admin.articles.content.subtitle') }}</p>
            </div>

            <div
                class="editor-container"
                x-data="{
                    content: @entangle('content'),
                    initQuill() {
                        const quill = new Quill($refs.editor, {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                    [{ 'color': [] }, { 'background': [] }],
                                    [{ 'align': [] }],
                                    ['link', 'blockquote', 'code-block'],
                                    ['clean']
                                ]
                            }
                        });

                        quill.root.innerHTML = this.content;

                        quill.on('text-change', () => {
                            this.content = quill.root.innerHTML;
                        });
                    }
                }"
                x-init="initQuill()"
                wire:ignore
            >
                <div x-ref="editor"></div>
            </div>

            @error('content') <span class="text-xs font-medium text-red-500">{{ $message }}</span> @enderror
        </section>
    </form>

    @if($showPreview)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">
            <div class="absolute inset-0" wire:click="closePreview"></div>

            <div class="relative z-10 w-full max-h-[90vh] max-w-7xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b p-6">
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('admin.articles.preview.title') }}</h3>
                        <p class="text-sm text-slate-500">
                            {{ __('admin.articles.preview.subtitle') }}
                        </p>
                    </div>

                    <button type="button"
                            wire:click="closePreview"
                            class="text-2xl leading-none text-slate-500 hover:text-black">
                        ✕
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-[0.95fr_1.05fr]">
                    <div class="space-y-4">
                        <div class="aspect-video overflow-hidden rounded-2xl border bg-slate-100">
                            @if($this->imagePreviewUrl)
                                <img src="{{ $this->imagePreviewUrl }}" class="h-full w-full object-cover" alt="Preview">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-sm text-slate-400">
                                    {{ __('admin.articles.preview.thumbnail_preview') }}
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('admin.articles.preview.meta') }}</div>
                            <h4 class="text-3xl font-bold leading-tight">{{ $title ?: __('admin.articles.preview.fallback_title') }}</h4>
                            <div class="text-sm text-slate-500">{{ $author ?: __('admin.articles.preview.fallback_author') }}</div>

                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs">
                                    {{ strtoupper($status) }}
                                </span>
                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs">
                                    {{ $articleId ? __('admin.articles.preview.mode_editing') : __('admin.articles.preview.mode_creating') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-slate-50 p-6">
                        <div class="mb-4 text-xs uppercase tracking-wide text-slate-400">
                            {{ __('admin.articles.preview.rendered_content') }}
                        </div>

                        <article class="prose max-w-none prose-slate ql-editor !p-0">
                            {!! $content ?: '<p style="color:#94a3b8">' . e(__('admin.articles.preview.fallback_content')) . '</p>' !!}
                        </article>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
@endpush