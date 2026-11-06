<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'fields' => Setting::FIELDS,
            'values' => Setting::values() + collect(Setting::FIELDS)->map(fn ($f, $key) => config("artform.{$key}"))->all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = collect(Setting::FIELDS)->mapWithKeys(fn ($field, $key) => [
            $this->input($key) => ['nullable', 'string', 'max:500', ...match ($field[1]) {
                'email' => ['email'],
                'url' => ['url'],
                default => [],
            }],
        ])->all();
        $data = $request->validate($rules);

        foreach (array_keys(Setting::FIELDS) as $key) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $data[$this->input($key)] ?? null]);
        }

        return back()->with('status', 'Settings saved. The website now shows the updated details.');
    }

    /** Form input name for a setting key ("contact.phone" → "contact_phone"). */
    private function input(string $key): string
    {
        return str_replace('.', '_', $key);
    }
}
