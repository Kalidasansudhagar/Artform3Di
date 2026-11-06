<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin sign in — ARTFORM 3Di</title>
    <link rel="icon" href="{{ asset('assets/icons/favicon.png') }}" type="image/png">
    <script>document.documentElement.classList.replace('no-js', 'js');</script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin bg-[#f3f0ea] text-ink">
    <main class="grid min-h-svh lg:grid-cols-2">
        {{-- Brand panel --}}
        <section class="relative hidden overflow-hidden bg-ink text-bone lg:flex lg:flex-col lg:justify-between lg:p-14 xl:p-20">
            <img src="{{ asset('assets/images/hero/hero-poster.svg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-50">
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/60 to-ink/30"></div>
            <div class="relative">
                <x-logo tone="light" size="lg" />
            </div>
            <div class="relative">
                <p class="mono text-brass-soft">Admin panel</p>
                <p class="display mt-5 max-w-md text-display-3">Where design <em class="text-brass-soft">meets technology.</em></p>
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-bone/60">Manage enquiries, products, projects and site settings for Artform 3Di.</p>
            </div>
        </section>

        {{-- Sign-in form --}}
        <section class="flex items-center justify-center px-5 py-16 sm:px-10">
            <div class="w-full max-w-md">
                <div class="lg:hidden"><x-logo tone="dark" /></div>

                <p class="mono mt-10 text-smoke lg:mt-0">Artform 3Di · Admin</p>
                <h1 class="display mt-4 text-display-3">Sign in</h1>
                <p class="mt-3 text-sm text-ink/60">Use your admin email and password.</p>

                @if (session('status'))
                    <p class="mt-8 flex items-center gap-3 rounded-lg bg-[#2f8350]/10 px-4 py-3 text-sm"><x-icon name="circle-check" class="text-[#2f8350]" />{{ session('status') }}</p>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="mt-10 space-y-6" x-data="{ show: false }">
                    @csrf
                    <x-admin.input name="email" label="Email address" type="email" :value="old('email')" autocomplete="username" required autofocus />

                    <div>
                        <label for="password" class="text-sm font-medium">Password</label>
                        <div class="relative mt-2">
                            <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="current-password"
                                   class="admin-input pr-12" @error('password') aria-invalid="true" @enderror>
                            <button type="button" @click="show = !show" class="absolute top-1/2 right-2 flex h-9 w-9 -translate-y-1/2 items-center justify-center text-smoke hover:text-ink" :aria-label="show ? 'Hide password' : 'Show password'">
                                <x-icon name="eye" x-show="!show" />
                                <x-icon name="eye-off" x-show="show" x-cloak />
                            </button>
                        </div>
                        @error('password')<p class="mt-2 text-xs text-[#b5432f]">{{ $message }}</p>@enderror
                    </div>

                    <label class="flex cursor-pointer items-center gap-3 text-sm text-ink/70">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 accent-ink" @checked(old('remember'))>
                        Keep me signed in on this device
                    </label>

                    <button type="submit" class="btn btn-dark btn-lg w-full">
                        <span>Sign in</span>
                        <span class="btn-icon"><x-icon name="arrow-right" /></span>
                    </button>
                </form>

                <a href="{{ route('home') }}" class="mt-10 inline-flex items-center gap-2 text-sm text-ink/60 transition hover:text-ink"><x-icon name="arrow-left" /> Back to website</a>
            </div>
        </section>
    </main>
</body>
</html>
