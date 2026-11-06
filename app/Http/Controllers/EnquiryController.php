<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Receives the website's quote / contact form and saves it for Admin → Enquiries.
 */
class EnquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Honeypot: real visitors never see or fill the "website" field.
        if ($request->filled('website')) {
            return response()->json(['ok' => true]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[+\d\s()-]{7,}$/'],
            'subject' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'subject.required' => 'Please choose the service you need.',
            'message.min' => 'Please describe your project and how the part will be used.',
        ]);

        Enquiry::query()->create($data + ['status' => 'new']);

        return response()->json(['ok' => true]);
    }
}
