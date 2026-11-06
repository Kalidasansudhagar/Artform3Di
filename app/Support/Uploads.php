<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Admin image uploads, stored in public/uploads (the "uploads" disk).
 * Paths are kept relative to public/, e.g. "uploads/products/abc.jpg", so asset() works everywhere.
 */
class Uploads
{
    /** @param  UploadedFile[]  $files */
    public static function store(array $files, string $folder): array
    {
        return array_map(function (UploadedFile $file) use ($folder) {
            $name = Str::lower(Str::random(20)).'.'.$file->extension();
            Storage::disk('uploads')->putFileAs($folder, $file, $name);

            return "uploads/{$folder}/{$name}";
        }, array_values(array_filter($files)));
    }

    /** Deletes uploaded files only; bundled placeholder images under assets/ are never touched. */
    public static function delete(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_string($path) && str_starts_with($path, 'uploads/')) {
                Storage::disk('uploads')->delete(substr($path, strlen('uploads/')));
            }
        }
    }
}
