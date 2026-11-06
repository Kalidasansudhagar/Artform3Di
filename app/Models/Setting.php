<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Key/value site settings edited in Admin → Settings.
 * Keys use dot notation matching config('artform.*'), e.g. "contact.phone" or "social.Instagram".
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    /** Editable settings: key => [label, input type]. */
    public const FIELDS = [
        'contact.phone' => ['Phone / WhatsApp number', 'tel'],
        'contact.email' => ['Business email', 'email'],
        'contact.address' => ['Studio address', 'textarea'],
        'contact.hours' => ['Business hours', 'text'],
        'social.Instagram' => ['Instagram URL', 'url'],
        'social.LinkedIn' => ['LinkedIn URL', 'url'],
        'social.YouTube' => ['YouTube URL', 'url'],
        'social.Facebook' => ['Facebook URL', 'url'],
    ];

    public static function values(): array
    {
        return static::query()->pluck('value', 'key')->all();
    }
}
