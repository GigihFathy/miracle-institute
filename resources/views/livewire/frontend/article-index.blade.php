@php
    use Illuminate\Support\Str;
@endphp

<div class="min-h-screen bg-slate-50">

    <div class="origin-top scale-[0.93]">

        <section class="w-full border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <x-ui.page-header
                    title="{{ __('general.articles.index.title') }}"
                    subtitle="{{ __('general.articles.index.subtitle') }}"
                />
            </div>
        </section>
    
        <section class="w-full bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">
                            {{ __('general.articles.index.archive_label') }}
                        </div>
                    </div>
    
                    <div class="w-full max-w-md">
                        <input type="search"
                               wire:model.debounce.300ms="search"
                               placeholder="{{ __('general.articles.index.search_placeholder') }}"
                               class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-slate-900">
                    </div>
                </div>
    
                <div class="space-y-3">
                    @forelse($articles as $article)
                        <x-articles.card
                            :article="$article"
                            :href="localized_route('articles.show', $article->id)"
                        />
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">
                            {{ __('general.articles.index.empty') }}
                        </div>
                    @endforelse
                </div>
    
                <div class="mt-6">
                    {{ $articles->links() }}
                </div>
            </div>
        </section>

    </div>
</div>