# ADR-0001: Stack et contraintes d'hébergement

Statut: accepté
Date: 2025-08-22

Contexte
- Hébergement imposé: o2switch (shared cPanel).
- Besoin: application stock multi-device (PC/tablette/mobile), notifications e‑mail, QR, PWA.

Décision
- Framework: Laravel 11 (PHP 8.2+), Blade + Tailwind, Alpine.js.
- Base: MySQL/MariaDB.
- E-mails: provider gratuit via API (Mailjet/Brevo). Pas de SMTP natif si limité.
- File d’attente: driver database (+ table jobs), traitement via cron (schedule:run) et queue:work --once.
- QR: génération côté serveur (lib PHP) ; scan côté client (zxing-js/html5-qrcode).
- PWA: manifest + service worker (cache basique et installabilité).

Conséquences
- Pas de workers persistants ni WebSockets: pas de temps réel fort ; on utilise cron ou polling.
- Déploiement simple via cPanel ; build front effectué en local puis upload /public/build si besoin.
- Architecture modulaire (Domain/Actions/Jobs) pour évolutivité.
