<div class="relative w-full max-w-xl">
    <form method="GET"
          action="{{ localized_route('courses.index') }}"
          class="relative">
        <input type="search"
               name="search"
               value="{{ trim($search) }}"
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
</div>
