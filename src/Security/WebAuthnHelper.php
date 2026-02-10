<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RequestStack;

class WebAuthnHelper
{
    public function __construct(private RequestStack $requestStack) {}

    public function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64UrlDecode(string $data): string
    {
        $pad = strlen($data) % 4;
        if ($pad) {
            $data .= str_repeat('=', 4 - $pad);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function generateChallenge(): string
    {
        $challenge = random_bytes(32);
        $session = $this->requestStack->getSession();
        $session->set('webauthn.challenge', $challenge);
        $session->set('webauthn.challenge_time', time());

        return $this->base64UrlEncode($challenge);
    }

    public function validateChallenge(string $clientDataJSONDecoded): bool
    {
        $session = $this->requestStack->getSession();
        $stored = $session->get('webauthn.challenge');
        $time = $session->get('webauthn.challenge_time');

        if (!$stored || !$time) return false;
        if (time() - $time > 60) return false;

        $clientData = json_decode($clientDataJSONDecoded, true);
        if (!is_array($clientData) || !isset($clientData['challenge'])) return false;

        $clientChallenge = $this->base64UrlDecode($clientData['challenge']);

        return hash_equals($stored, $clientChallenge);
    }

    public function createRegistrationOptions(string $userId, string $email): array
    {
        return [
            'challenge' => $this->generateChallenge(),
            'rp' => ['name' => 'Passkeys Demo', 'id' => 'localhost'],
            'user' => [
                'id' => $this->base64UrlEncode($userId),
                'name' => $email,
                'displayName' => $email
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],
            ],
            'timeout' => 60000,
        ];
    }

    public function createLoginOptions(array $allowCredentials): array
    {
        return [
            'challenge' => $this->generateChallenge(),
            'allowCredentials' => $allowCredentials,
            'timeout' => 60000,
        ];
    }
}
