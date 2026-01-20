# Ateco Cables — Starter scaffold

Este ZIP contiene archivos iniciales para arrancar el proyecto Laravel (vistas Blade, modelo Product, migración, factory, seeder, controlador y componentes). Pegá estos archivos en tu proyecto Laravel y ejecutá:

```bash
npm install
npm run dev
composer install
php artisan key:generate
php artisan migrate --seed
php artisan tinker
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@ateco.com',
    'password' => Hash::make('12345678'),
]);

php artisan storage:link
php artisan optimize:clear
php artisan serve

```
