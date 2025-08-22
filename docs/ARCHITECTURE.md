# Architecture

Vision d'ensemble modulaire (compatible o2switch):

- Frontend (Blade/Tailwind, mobile-first) + PWA pour installation et cache basique.
- Backend Laravel (Controllers minces → Actions/Domain pour la logique).
- Queue driver = database (pas de workers persistants). Traitement via cron (schedule:run + queue:work --once).
- E-mails via provider gratuit (p. ex. Mailjet/Mailgun/Sendinblue en offre gratuite) avec SDK/API HTTP.
- Base: MySQL/MariaDB. Sauvegardes planifiées via cron + export externe.

Bounded contexts (Domain)
- Product: produits, seuils d’alerte, unités, photos.
- Location: emplacements/étagères, QR liés aux URL.
- Inventory: stock par produit/localisation, mouvements (in/out/transfer), inventaire périodique.
- Notification: règles d’alerte (seuil), déduplication, e‑mails.
- User/Access: rôles (admin, gestionnaire, lecture), audit.

Patrons
- Actions (app/Actions): orchestrent un cas d’usage (ex: CreateStockMovementAction) en appelant Domain services.
- Services (app/Domain/*/Services): logique métier réutilisable/testable.
- Repositories (optionnel) si besoin de découpler Eloquent.
- Jobs (app/Jobs): offload e‑mail/exports.

Décisions clés
- Pas de realtime WebSocket (non adapté shared) → refresh poll/cron si nécessaire.
- Déduplication d’alertes: table dédiée ou champs sur product_stock + timestamp.
- QR: encode URL courte /l/{id}. Scan ouvre fiche.

Sécurité
- Auth Laravel, rôles via Policies/Gates.
- CSRF/HTTPS activés, validation stricte des inputs, rate limit.

