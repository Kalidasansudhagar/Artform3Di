{{--
    Client logo (source: public/img/logo.jpg.jpeg), as transparent PNGs.
    tone "light" = warm-white ink for dark backgrounds, "dark" = original black ink for light backgrounds.
--}}
@props(['tone' => 'light', 'size' => 'md'])

<img src="{{ asset("assets/images/brand/logo-{$tone}.png") }}" alt="ARTFORM 3Di" width="452" height="533" decoding="async"
     {{ $attributes->class([
         'block w-auto select-none',
         'h-12' => $size === 'sm',
         'h-[60px] lg:h-[72px]' => $size === 'md',
         'h-28 lg:h-32' => $size === 'lg',
     ]) }}>
