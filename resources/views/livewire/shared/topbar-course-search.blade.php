<div x-data="{ open: @js(filled(trim($search))) }"
     @keydown.escape.window="open = false"
     class="relative w-full max-w-xl">
    <form method="GET"
          action="{{ localized_route('courses.index') }}"
          @submit="open = false"
          class="relative">
        <input type="search"
               name="search"
               wire:model.live.debounce.250ms="search"
               @focus="open = $event.target.value.trim().length > 0"
               @input="open = $event.target.value.trim().length > 0"
               placeholder="{{ __('general.topbar_search.placeholder') }}"
               class="h-11 w-full rounded-full border border-slate-200 bg-slate-50 pl-11 pr-12 text-sm text-mentor-primary outline-none transition placeholder:text-slate-400 focus:border-mentor-primary focus:bg-white focus:ring-2 focus:ring-mentor-secondary-solid">

        <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
             xmlns="http://www.w3.org/2000/svg"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="1.5">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
        </svg>

        <button type="submit"
                class="absolute right-1.5 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-mentor-primary text-white transition hover:opacity-90">
            <span class="sr-only">{{ __('general.topbar_search.submit') }}</span>
            <svg class="h-4 w-4"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="1.5">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
            </svg>
        </button>
    </form>

    <div x-cloak
         x-show="open"
         x-on:search-submitted.window="open = false"
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute left-0 right-0 z-50 mt-2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.14)]"
         style="display: none;">
        <div class="border-b border-slate-100 px-4 py-3">
            <div class="text-xs font-semibold text-slate-400">
                {{ __('general.topbar_search.preview_title') }}
            </div>
        </div>

        @if($results->isNotEmpty())
            <div class="max-h-[22rem] overflow-y-auto py-2">
                @foreach($results as $course)
                    <a href="{{ localized_route('courses.show', $course->slug) }}"
                       @click="open = false"
                       class="block truncate px-4 py-3 text-sm font-medium text-mentor-primary transition hover:bg-slate-50">
                        {{ $course->title }}
                    </a>
                @endforeach
            </div>
        @else
            <div class="px-4 py-5 text-sm text-slate-500">
                {{ __('general.topbar_search.empty') }}
            </div>
        @endif

        <div class="border-t border-slate-100 bg-slate-50/80 px-4 py-3">
            <button type="submit"
                    @click="open = false; window.dispatchEvent(new CustomEvent('search-submitted'))"
                    class="text-sm font-medium text-mentor-primary transition hover:text-mentor-primary/80">
                {{ __('general.topbar_search.view_all_results', ['query' => trim($search) !== '' ? trim($search) : __('general.topbar_search.all_courses')]) }}
            </button>
        </div>
    </div>
</div>
