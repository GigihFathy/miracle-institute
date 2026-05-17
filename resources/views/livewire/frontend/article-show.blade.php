@php
    use Illuminate\Support\Str;

    $heroImage = ! empty($article->image)
        ? asset('storage/' . $article->image)
        : null;
@endphp

<x-articles.editorial-styles />

<div class="min-h-screen bg-slate-50">
    <div class="origin-top">
        <section class="w-full border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ localized_route('articles.index') }}"
                   class="text-sm font-medium text-slate-500 transition hover:text-slate-900">
                    ← {{ __('general.articles.show.back_to_articles') }}
                </a>
            </div>
        </section>
    
        <section class="w-full bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                    <div class="grid grid-cols-1 xl:grid-cols-[0.92fr_1.08fr]">
                        <aside class="space-y-4 p-5 sm:p-6">
                            <div class="space-y-2">
                                <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">
                                    {{ __('general.articles.show.meta') }}
                                </div>
    
                                <h1 class="text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">
                                    {{ $article->title }}
                                </h1>
    
                                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                                    <span>{{ $article->author ?? __('general.articles.show.default_author') }}</span>
                                    @if($article->created_at)
                                        <span>•</span>
                                        <span>{{ $article->created_at->format('d M Y') }}</span>
                                    @endif
                                </div>
                            </div>
    
                            <div class="aspect-[16/10] overflow-hidden rounded-2xl border bg-slate-100">
                                @if($heroImage)
                                    <img src="{{ $heroImage }}"
                                         alt="{{ $article->title }}"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-slate-100">
                                        <span class="text-sm text-slate-400">{{ __('general.articles.show.thumbnail_preview') }}</span>
                                    </div>
                                @endif
                            </div>
    
                            <div class="rounded-2xl border bg-slate-50 p-4">
                                <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">
                                    {{ __('general.articles.show.reading_note') }}
                                </div>
                                <p class="mt-2 text-sm leading-6 text-slate-500">
                                    {{ Str::limit(strip_tags(str_replace(['</p>', '<br>', '</div>', '</h1>', '</h2>', '</h3>'], ' ', $article->content)), 220) }}
                                </p>
    
                            </div>
                        </aside>
    
                        <main class="border-t border-slate-200 bg-slate-50 p-5 sm:p-6 xl:border-l xl:border-t-0">
                            <div class="mb-4 flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">
                                <span>{{ __('general.articles.show.rendered_content') }}</span>
                            </div>
    
                            <x-articles.body :content="$article->content" />
                        </main>
                    </div>
                </div>
            </div>
        </section>
    
        @if($related->count())
            <section class="pb-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('general.articles.show.related_title') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('general.articles.show.related_subtitle') }}</p>
                    </div>
    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach($related as $item)
                            <x-articles.card
                                :article="$item"
                                :href="localized_route('articles.show', $item->id)"
                                :compact="true"
                            />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
</div>