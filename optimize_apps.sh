#!/bin/bash

# Navigasi ke direktori Laravel
cd /home3/sipandaw/sipandawa

# Menjalankan perintah Artisan untuk membersihkan cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Menyegarkan cache konfigurasi dan rute
php artisan config:cache
php artisan route:cache
php artisan view:cache

#optimize filament
php artisan filament:optimize
