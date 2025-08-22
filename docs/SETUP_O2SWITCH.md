# Déploiement sur o2switch (cPanel)

Prérequis
- PHP 8.2+ sélectionné dans cPanel (Select PHP Version)
- Base MySQL créée et utilisateur assigné
- Domaine/Sous-domaine pointant sur public/

Étapes
1) Upload du code (Git ou File Manager). Le DocumentRoot doit pointer sur /public.
2) Configurer .env (DB, APP_URL, MAIL provider API keys).
3) Générer la clé: php artisan key:generate
4) Migrations + seed: php artisan migrate --force
5) Installer les assets (si nécessaire): npm build localement et uploader /public/build (ou utiliser Vite en CDN-free avec prebuild local).

Cron (cPanel → Cron Jobs)
- * * * * * php /home/USER/www/artisan schedule:run >> /dev/null 2>&1
- Optionnel si pas de scheduler: php /home/USER/www/artisan queue:work --once >> /dev/null 2>&1

E-mails (gratuit)
- Mailjet (plan gratuit), Mailgun (sandbox/dev), Brevo/Sendinblue (gratuit), ou SMTP o2switch (peut être limité).
- Recommandé: API HTTP via SDK.

Sauvegardes
- Cron: mysqldump | gzip | upload externe (S3/FTP) ou sauvegarde cPanel.

