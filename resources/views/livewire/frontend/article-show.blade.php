<div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
    <article class="xl:col-span-3 space-y-5">
        <div class="rounded-2xl bg-white border p-6">
            <a href="{{ route('articles.index') }}" class="text-sm underline">← Back to articles</a>

            <h1 class="text-3xl font-bold mt-4">{{ $article->title }}</h1>
            <p class="text-sm text-slate-500 mt-2">By {{ $article->author }}</p>

            <div class="prose max-w-none mt-6">
                {!!$article->content !!}
            </div>
        </div>

        @if($article->image)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-2xl bg-white border overflow-hidden">
                    <img src="{{ asset('images/test.png') }}"
                            class="w-full h-64 object-cover"
                            alt="">
                </div>
            </div>
        @endif
    </article>

    <aside class="space-y-4">
        <div class="rounded-2xl bg-white border p-5">
            <h2 class="font-semibold mb-3">Related articles</h2>
            <div class="space-y-3">
                @forelse($related as $item)
                    <a href="{{ route('articles.show', $item->id) }}"
                       class="block rounded-xl border p-3 hover:bg-slate-50">
                        <div class="font-medium text-sm">{{ $item->title }}</div>
                        <div class="text-xs text-slate-500">{{ $item->author }}</div>
                    </a>
                @empty
                    <div class="text-sm text-slate-500">No related articles.</div>
                @endforelse
            </div>
        </div>
    </aside>
</div>