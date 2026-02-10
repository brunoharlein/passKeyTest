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

