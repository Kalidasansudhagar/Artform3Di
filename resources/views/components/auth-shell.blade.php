{{-- Split layout for account pages: artwork panel (desktop) beside the form. --}}
@props(['image', 'caption' => 'Crafted layer by layer'])

<section class="grid min-h-[100svh] bg-paper lg:grid-cols-12">
    <div class="relative hidden overflow-hidden bg-ink lg:col-span-5 lg:block">
        <img src="{{ asset($image) }}" alt="" width="1200" height="1500" class="absolute inset-0 h-full w-full object-cover" fetchpriority="high">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/20 to-ink/40"></div>
        <div class="absolute inset-x-0 bottom-0 p-12 text-bone xl:p-16">
            <p class="mono text-brass-soft">ARTFORM 3Di · Members</p>
            <p class="display mt-5 max-w-sm text-display-4">{{ $caption }}</p>
        </div>
    </div>

    <div class="flex items-center px-5 pt-[calc(var(--header-h)+3rem)] pb-20 sm:px-8 lg:col-span-7 lg:px-16 xl:px-28">
        <div class="mx-auto w-full max-w-md lg:mx-0">
            {{ $slot }}
        </div>
    </div>
</section>
