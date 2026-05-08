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
            border-color: #0f172a !important; /* Slate 900 */
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
            background-color: #1e293b; /* Slate 800 */
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


        /* PREVIEW */
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
    {{-- Header --}}
    <x-ui.page-header
        title="{{ $articleId ? 'Edit Article' : 'New Article' }}"
        subtitle="Manajemen konten artikel dengan editor profesional."
    >
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.articles.index') }}"
               class="px-5 py-2.5 rounded-xl border text-sm font-medium bg-white hover:bg-slate-50 transition">
                Back
            </a>

            <button type="button"
                    wire:click="openPreview"
                    class="px-5 py-2.5 rounded-xl border text-sm font-medium bg-white hover:bg-slate-50 transition">
                Preview
            </button>

            <button form="article-form"
                    class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">
                Save Article
            </button>
        </div>
    </x-ui.page-header>

    <form id="article-form" wire:submit.prevent="save" class="space-y-6">
        {{-- Metadata Section --}}
        <section class="rounded-2xl bg-white border p-8 space-y-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-slate-900">General Information</h2>
                    <p class="text-sm text-slate-500">Konfigurasi dasar untuk identitas artikel Anda.</p>
                </div>

                <div class="flex gap-4">
                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-[100px]">
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Mode</p>
                        <p class="font-bold text-slate-700">{{ $articleId ? 'Edit' : 'Create' }}</p>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-[100px]">
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Image</p>
                        <p class="font-bold text-slate-700">{{ $imageFile ? 'Selected' : 'None' }}</p>
                    </div>
                    <div class="rounded-2xl border bg-slate-50 p-4 min-w-[100px]">
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Status</p>
                        <p class="font-bold text-slate-700">{{ strtoupper($status) }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700 ml-1">Article Title</label>
                <input wire:model.blur="title"
                    type="text"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 text-slate-900 placeholder:text-slate-400 focus:bg-white"
                    placeholder="Masukkan judul artikel yang menarik...">
                @error('title') <span class="text-xs text-red-500 font-medium ml-1">{{ $message }}</span> @enderror
            </div>



            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ uploadMode: 'local' }">
                <div class="space-y-3">
                    <label class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Thumbnail Preview
                    </label>
                    <div class="aspect-video rounded-3xl border-2 border-dashed border-slate-200 overflow-hidden bg-slate-50 flex items-center justify-center relative group">
                        @if($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL))
                            <img src="{{ $imageUrl }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" onerror="this.src='https://placehold.co'">
                        @elseif($this->imagePreviewUrl)
                            <img src="{{ $this->imagePreviewUrl }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="text-center p-4">
                                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-slate-400 text-xs font-medium">Waiting for Image...</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="space-y-4">
                        <div class="flex p-1 bg-slate-100 rounded-2xl w-fit">
                            <button type="button" @click="uploadMode = 'local'" :class="uploadMode === 'local' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'" class="px-4 py-2 rounded-xl text-xs font-bold transition">Local File</button>
                            <button type="button" @click="uploadMode = 'url'" :class="uploadMode === 'url' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'" class="px-4 py-2 rounded-xl text-xs font-bold transition">Internet URL</button>
                        </div>

                        <div x-show="uploadMode === 'local'" x-transition class="space-y-3">
                            <label class="text-sm font-bold text-slate-800 flex items-center gap-2 ml-1">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                Choose Local File
                            </label>
                            <div class="relative group">
                                <input type="file" wire:model="imageFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="border border-slate-200 bg-slate-50 rounded-2xl px-5 py-4 flex items-center justify-between transition group-hover:border-slate-400 focus-within:ring-4 focus-within:ring-slate-900/5">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-white rounded-xl shadow-sm border border-slate-100">
                                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700">Select Thumbnail...</span>
                                    </div>
                                    <span class="text-xs font-bold text-slate-400">Browse</span>
                                </div>
                            </div>
                        </div>

                        <div x-show="uploadMode === 'url'" x-transition class="space-y-3">
                            <label class="text-sm font-bold text-slate-800 flex items-center gap-2 ml-1">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.8a3.736 3.736 0 011.897-1.13L15 8.25l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"></path></svg>
                                Paste Image URL
                            </label>
                            <input wire:model.live="imageUrl" 
                                type="url"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:bg-white focus:ring-4 focus:ring-slate-900/5 transition outline-none"
                                placeholder="https://example.com">
                            <p class="text-[10px] text-slate-400 ml-1">Pastikan URL berakhir dengan .jpg, .png, atau .webp</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-sm font-bold text-slate-800 flex items-center gap-2 ml-1">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Publication Status
                        </label>
                        <div class="relative group">
                            <select wire:model.live="status" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm font-semibold appearance-none focus:bg-white transition cursor-pointer">
                                <option value="draft">Save as Draft</option>
                                <option value="active">Publish Now (Active)</option>
                                <option value="inactive">Disabled (Hidden)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-5 pointer-events-none text-slate-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                        </div>
                    </div>
                </div>
            </div>


        </section>

        {{-- Content Editor Section --}}
        <section class="rounded-2xl bg-white border p-8 space-y-6">
            <div class="space-y-1">
                <h2 class="text-xl font-bold text-slate-900">Content Editor</h2>
                <p class="text-sm text-slate-500">Tulis isi artikel secara detail dengan editor teks di bawah.</p>
            </div>

            <div class="editor-container" 
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
                 wire:ignore>
                <div x-ref="editor"></div>
            </div>
            @error('content') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
        </section>
    </form>
    @if($showPreview)
        <div class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4">
            <div class="absolute inset-0" wire:click="closePreview"></div>
    
            <div class="relative z-10 w-full max-w-7xl bg-white rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <div>
                        <h3 class="text-lg font-semibold">Article Preview</h3>
                        <p class="text-sm text-slate-500">
                            Preview lebar untuk mengecek struktur, thumbnail, dan isi artikel.
                        </p>
                    </div>
    
                    <button type="button"
                            wire:click="closePreview"
                            class="text-slate-500 hover:text-black text-2xl leading-none">
                        ✕
                    </button>
                </div>
    
                <div class="p-6 grid grid-cols-1 xl:grid-cols-[0.95fr_1.05fr] gap-6">
                    <div class="space-y-4">
                        <div class="aspect-video rounded-2xl overflow-hidden border bg-slate-100">
                            @if($this->imagePreviewUrl)
                                <img src="{{ $this->imagePreviewUrl }}" class="w-full h-full object-cover" alt="Preview">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-sm">
                                    Thumbnail preview
                                </div>
                            @endif
                        </div>
    
                        <div class="space-y-2">
                            <div class="text-xs uppercase tracking-wide text-slate-400">Meta</div>
                            <h4 class="text-3xl font-bold leading-tight">{{ $title ?: 'Article title' }}</h4>
                            <div class="text-sm text-slate-500">{{ $author ?: 'Author' }}</div>
    
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs bg-slate-100">
                                    {{ strtoupper($status) }}
                                </span>
                                <span class="inline-flex px-2 py-1 rounded-full text-xs bg-slate-100">
                                    {{ $articleId ? 'Editing' : 'Creating' }}
                                </span>
                            </div>
                        </div>
                    </div>
    
                    <div class="rounded-2xl border bg-slate-50 p-6">
                        <div class="text-xs uppercase tracking-wide text-slate-400 mb-4">
                            Rendered Content
                        </div>

                        <article class="prose max-w-none prose-slate ql-editor !p-0">
                            {!!$content ?: '<p style="color:#94a3b8">Article preview will appear here.</p>' !!}
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
