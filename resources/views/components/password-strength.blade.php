{{-- Four-step strength meter for the `form.password` field of an authForm. --}}
<div class="mt-3" x-show="form.password" x-cloak aria-live="polite">
    <div class="flex gap-1.5">
        @for ($i = 1; $i <= 4; $i++)
            <span class="h-1 flex-1 rounded-full transition-colors duration-300"
                  :class="strength >= {{ $i }} ? (strength >= 3 ? 'bg-[#5f7a55]' : 'bg-brass') : 'bg-ink/10'"></span>
        @endfor
    </div>
    <p class="mt-2 text-xs text-smoke">Password strength: <span class="text-ink" x-text="strengthLabel"></span></p>
</div>
