<div class="w-full">
    <div class="overflow-hidden rounded-2xl border border-[#004777]/10 bg-white">
        <div class="overflow-x-auto">
            <table {{ $attributes->merge(['class' => 'w-full min-w-full text-sm']) }}>
                {{ $slot }}
            </table>
        </div>
    </div>
</div>