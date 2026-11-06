<?php

namespace App\Support;

/**
 * Contact links built from the phone and address in Admin → Settings
 * (config('artform.contact.*'), applied per request by ApplySiteSettings).
 */
class Contact
{
    public static function phone(): ?string
    {
        return config('artform.contact.phone');
    }

    public static function address(): ?string
    {
        return config('artform.contact.address');
    }

    /** International digits for tel: and wa.me links; 10-digit Indian numbers get the 91 prefix. */
    public static function phoneDigits(?string $phone = null): ?string
    {
        $digits = preg_replace('/\D/', '', (string) ($phone ?? self::phone()));

        return match (true) {
            $digits === '' => null,
            strlen($digits) === 10 => '91'.$digits,
            default => $digits,
        };
    }

    public static function phoneUrl(): ?string
    {
        $digits = self::phoneDigits();

        return $digits ? 'tel:+'.$digits : null;
    }

    public static function whatsappUrl(?string $phone = null): ?string
    {
        $digits = self::phoneDigits($phone);

        return $digits ? 'https://wa.me/'.$digits : null;
    }

    public static function mapsUrl(): ?string
    {
        return self::address() ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode(self::address()) : null;
    }
}
