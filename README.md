# passKeyTest

# Passkeys Symfony 

Ce dépôt contient un prototype d’authentification **sans mot de passe** basé sur **WebAuthn / Passkeys** avec :

- Backend : **Symfony (API)**
- Front de test : **HTML + JavaScript natif**
- Flux :
  - `/webauthn/register/start`
  - `/webauthn/register/finish`
  - `/webauthn/login/start`
  - `/webauthn/login/finish`

## Objectif du projet
Démontrer qu’il est possible de remplacer les mots de passe par des Passkeys tout en gardant une stack Symfony.

## Lancer le projet
```bash
composer install
php -S 127.0.0.1:8000 -t public

sous le navigateur :
http://localhost:8000/test.html


Pour tester (ouvrir un autre terminal)
curl -i -X POST http://127.0.0.1:8000/webauthn/register/start \
  -H "Content-Type: application/json" \
  -d '{"email":"test@asso.fr"}'

vous devez obtenir :
HTTP/1.1 200 OK
Host: 127.0.0.1:8000
Connection: close
X-Powered-By: PHP/8.4.6
Cache-Control: max-age=0, must-revalidate, private
Date: Tue, 10 Feb 2026 19:34:05 GMT
Content-Type: application/json
X-Robots-Tag: noindex
Expires: Tue, 10 Feb 2026 19:34:05 GMT
Set-Cookie: PHPSESSID=1b58f8561a4a166b0c45deb38dbde33e; path=/; httponly; samesite=lax

{"challenge":"R6y8bkxbPZCcBVunekrDn7FDFWrzpf1sqXJhqjiX2P0","rp":{"name":"Passkeys Demo","id":"localhost"},"user":{"id":"Y2IxMDdhOGFkMTZhZTM3ZWUyNzk5Zjc2NWNiYWQzZGY","name":"test@asso.fr","displayName":"test@asso.fr"},"pubKeyCredParams":[{"type":"public-key","alg":-7}],"timeout":60000}% 

