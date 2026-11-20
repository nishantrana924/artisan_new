<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.turnstile.secret_key');

        // Allow bypass if Turnstile is not configured or in unit testing
        if (empty($secretKey) || app()->environment('testing')) {
            return;
        }

        if (empty($value) || !is_string($value)) {
            $fail('Please complete the Cloudflare security verification.');
            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secretKey,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            if (!$response->successful()) {
                Log::warning('Cloudflare Turnstile siteverify HTTP error: ' . $response->status());
                return;
            }

            $data = $response->json();

            if (!isset($data['success']) || $data['success'] !== true) {
                Log::info('Cloudflare Turnstile verification rejected: ' . json_encode($data['error-codes'] ?? []));
                $fail('Security check failed. Please verify you are human and try again.');
            }
        } catch (\Throwable $e) {
            Log::error('Cloudflare Turnstile connection error: ' . $e->getMessage());
            // Fail open on network timeouts to prevent dropping legitimate customers
            return;
        }
    }
}

