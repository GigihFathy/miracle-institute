<div class="space-y-5 lg:px-36">
    <x-ui.page-header
        title="Articles"
        subtitle="News, updates, and learning notes."
    />

    <div class="rounded-2xl bg-white border p-4">
        <input type="search"
               wire:model.debounce.300ms="search"
               placeholder="Cari artikel..."
               class="w-full md:w-1/2 border rounded-xl px-4 py-2">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($articles as $article)
            <article class="rounded-2xl bg-white border overflow-hidden flex flex-col">
                <div class="aspect-[16/9] bg-slate-100"></div>

                <div class="p-5 flex-1 flex flex-col gap-3">
                    <div>
                        <h2 class="font-semibold text-lg">{{ $article->title }}</h2>
                        <p class="text-sm text-slate-500">{{ $article->author }}</p>
                    </div>

                    <p class="text-sm text-slate-600">
                        {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 120) }}
                    </p>

                    <div class="mt-auto">
                        <a href="{{ route('articles.show', $article->id) }}"
                           class="inline-flex px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">
                            Read more
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <x-ui.empty-state
                title="Belum ada artikel aktif"
                description="Coba lagi nanti atau cek kategori lain."
            />
        @endforelse
    </div>

    <div>{{ $articles->links() }}</div>
</div>