@props([
    'article',
    'href',
    'compact' => false,
])

@php
    use Illuminate\Support\Str;

    $excerptLimit = $compact ? 90 : 140;
@endphp

<a href="{{ $href }}"
   class="group block overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-200 hover:border-slate-300 hover:shadow-sm">

    <div class="flex flex-col sm:flex-row">

        {{-- IMAGE --}}
        <div class="relative sm:w-[240px] md:w-[260px] lg:w-[280px] shrink-0">

            <div class="h-[180px] sm:h-full overflow-hidden bg-slate-100">
                @if(!empty($article->image))
                    <img src="{{ asset('storage/' . $article->image) }}"
                         alt="{{ $article->title }}"
                         class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]">
                @else
                    <div class="flex h-full w-full items-center justify-center">
                        <svg width="120"
                             height="68"
                             viewBox="0 0 280 158"
                             fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <rect width="280" height="158" fill="#e6e9ee"/>
                        </svg>
                    </div>
                @endif
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="flex min-w-0 flex-1 flex-col justify-between p-4 sm:p-5">

            <div class="space-y-3">

                <div class="flex flex-wrap items-center gap-1.5 text-[10px] font-medium uppercase tracking-[0.14em] text-slate-400">
                    <span>{{ $article->created_at?->format('d M Y') }}</span>
                    <span>•</span>
                    <span>{{ $article->author ?? 'Editorial Team' }}</span>
                </div>


                <h3 class="line-clamp-2 text-[15px] sm:text-base font-semibold leading-6 text-slate-900">
                    {{ Str::limit($article->title, $compact ? 60 : 88) }}
                </h3>

                <p class="line-clamp-3 text-[13px] leading-6 text-slate-500">
                    {{ Str::limit(strip_tags(str_replace(['</p>', '</div>', '<br>', '<br />', '</h1>', '</h2>', '</h3>',], ' ', $article->content)), $excerptLimit) }}
                </p>

            </div>

            <div class="mt-4">
                <span class="inline-flex items-center rounded-lg bg-slate-900 px-2.5 py-1 text-[11px] font-medium text-white">
                    Read article
                </span>
            </div>
        </div>

    </div>
</a>