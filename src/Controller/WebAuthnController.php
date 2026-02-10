<?php

namespace App\Controller;

use App\Security\WebAuthnHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class WebAuthnController extends AbstractController
{
    // mémoire “fake DB” (prototypage)
    private static array $store = [];

    #[Route('/webauthn/register/start', methods: ['POST'])]
    public function registerStart(Request $request, WebAuthnHelper $helper): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $email = $data['email'] ?? 'test@asso.fr';

        $userId = bin2hex(random_bytes(16));

        $options = $helper->createRegistrationOptions($userId, $email);

        return $this->json($options);
    }

    #[Route('/webauthn/register/finish', methods: ['POST'])]
    public function registerFinish(Request $request, WebAuthnHelper $helper): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $credential = $data['credential'] ?? null;

        if (!$credential) return $this->json(['ok' => false, 'error' => 'missing credential'], 400);

        $clientDataJSON = $helper->base64UrlDecode($credential['response']['clientDataJSON']);
        if (!$helper->validateChallenge($clientDataJSON)) {
            return $this->json(['ok' => false, 'error' => 'challenge invalid/expired'], 400);
        }

        // On stocke ce qu’il faut pour login (proto)
        #self::$store['credential_id'] = $credential['rawId']; n'aime pas stocker dans la RAM
        $request->getSession()->set('credential_id', $credential['rawId']);


        return $this->json(['ok' => true]);
    }

    #[Route('/webauthn/login/start', methods: ['POST'])]
public function loginStart(Request $request, WebAuthnHelper $helper): JsonResponse
{
    $credentialId = $request->getSession()->get('credential_id');

    if (!$credentialId) {
        return $this->json(['ok' => false, 'error' => 'no credential registered'], 400);
    }

    $allow = [[
        'type' => 'public-key',
        'id' => $credentialId
    ]];

    return $this->json($helper->createLoginOptions($allow));
}


    #[Route('/webauthn/login/finish', methods: ['POST'])]
    public function loginFinish(Request $request, WebAuthnHelper $helper): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $credential = $data['credential'] ?? null;

        if (!$credential) return $this->json(['ok' => false, 'error' => 'missing credential'], 400);

        $clientDataJSON = $helper->base64UrlDecode($credential['response']['clientDataJSON']);
        if (!$helper->validateChallenge($clientDataJSON)) {
            return $this->json(['ok' => false, 'error' => 'challenge invalid/expired'], 400);
        }

        // Proto : on ne vérifie pas encore crypto signature
        return $this->json(['ok' => true, 'token' => 'DEMO_JWT']);
    }
}

