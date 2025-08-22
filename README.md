# Logistique Secours

Application web (Laravel + Blade/Tailwind) pour la gestion de stock d'une association de secourisme et de sauvetage.

Objectifs clés
- Gestion de stock (entrées, sorties, transferts, inventaires)
- Seuils d’alerte par produit et notifications e‑mail
- QR codes pour étagères/emplacements et lots
- PWA utilisable sur PC, tablette, téléphone (scan QR côté client)

Contraintes d’hébergement
- Déploiement sur o2switch (shared cPanel)
- Pas de process persistants → queue driver: database + cron (artisan schedule:run)

Stack technique
- PHP 8.2+, Composer, Laravel 11+
- MySQL/MariaDB
- Tailwind CSS, Alpine.js (ou Livewire au besoin)
- Envoi e‑mail via provider gratuit (Mailjet/Mailgun dev tiers gratuit, à confirmer)

Structure (prévisionnelle)
- app/Domain/* → logique métier par bounded context (Product, Inventory, Location, Notification)
- app/Http/Controllers/* → contrôleurs fins (mince) orientés use‑cases
- app/Actions/* → application services (orchestrateurs)
- app/Models/* → Eloquent models
- app/Jobs/* → envoi e‑mail/notifications (queue: database)
- app/Policies/* → rôles & permissions
- resources/views/* → Blade + composants UI
- resources/js/* → scan QR (zxing-js / html5-qrcode)
- database/migrations/* → schéma DB
- tests/* → tests unitaires et HTTP
- docs/* → docs d’archi, décisions (ADR), procédures

Roadmap MVP
1. Initialisation Laravel + config .env.example
2. Migrations: products, locations, stocks, stock_movements, users, notifications
3. CRUD produits/locations, mouvements de stock
4. Dashboard seuils bas
5. QR: génération + scan côté client
6. Notifications e‑mail (Mailjet ou équivalent gratuit) via queue DB + cron
7. PWA (manifest + service worker)

Déploiement o2switch (exemple cron)
- php /home/USER/www/artisan schedule:run >> /dev/null 2>&1

Licences et crédits
- QR: zxing-js ou html5-qrcode (MIT)

---

Démarrage local (Windows PowerShell)
1) Installer PHP 8.2+ et Composer
   - PHP: https://windows.php.net/download/ (ou via winget/choco) et ajoutez php.exe au PATH
   - Composer: https://getcomposer.org/Composer-Setup.exe
2) Créer le projet Laravel dans ce dossier (quand PHP/Composer sont prêts)
   - composer create-project laravel/laravel .
   - php artisan key:generate
3) Configurer l’environnement
   - Copier .env → renseigner DB_* (MySQL/MariaDB), APP_URL, MAIL_*
4) Lancer le serveur local
   - php artisan serve
5) Compiler le front (optionnel pour Tailwind)
   - npm install
   - npm run dev

Notifications e‑mail (gratuit)
- Par défaut, viser Mailjet (plan gratuit) ou Brevo/Sendinblue.
- En production o2switch: utiliser l’API HTTP (clé API dans .env), et la queue database + cron.

Prochaines actions (ordre proposé)
- Une fois Laravel installé: créer migrations (products, locations, stocks, stock_movements, notifications)
- Implémenter CRUD + dashboard seuils, puis QR et notifications
- Ajouter PWA (manifest + service worker)

---

Support multi‑device
- UI mobile‑first (Tailwind), responsive pour tablette/desktop.
- Scan QR via navigateur mobile (zxing-js ou html5-qrcode).

