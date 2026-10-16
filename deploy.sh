#!/usr/bin/env bash
#
# Déploiement de StockFlow sur le serveur (alwaysdata).
# Usage, depuis le dossier du projet sur le serveur :  bash deploy.sh
#
# Le script s'arrête à la première erreur (set -e) et remet toujours le site en ligne à la fin.

set -euo pipefail

cd "$(dirname "$0")"

echo "🚧 Mise en maintenance…"
php artisan down --retry=15 || true
trap 'php artisan up; echo "✅ Site remis en ligne."' EXIT

echo "📥 Récupération du code depuis GitHub (branche main)…"
git pull --ff-only origin main

echo "📦 Installation des dépendances PHP…"
composer install --no-dev --optimize-autoloader --no-interaction

echo "🗄️  Mise à jour de la base de données…"
php artisan migrate --force

echo "🧹 Vidage puis reconstruction des caches…"
php artisan optimize:clear
php artisan optimize

echo "🎉 Déploiement terminé : $(git log -1 --pretty='%h — %s')"
