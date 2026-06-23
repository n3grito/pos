# POS System

Sistema POS para administración de ventas, compras, inventario y clientes.

## Requisitos

- PHP 8.3+
- MySQL 8.0+ / MariaDB 10.6+
- Composer 2.x
- Node.js 20+ y NPM
- Apache 2.4+ con mod_rewrite

## Instalación

cd /var/www/pos

# Copiar entorno
cp .env.example .env
# EDITAR .env con datos reales:
#   DB_DATABASE=pos_system
#   DB_USERNAME=pos_user
#   DB_PASSWORD=tu_password_seguro
#   APP_URL=https://tudominio.com  (o http://)
#   APP_ENV=production
#   APP_DEBUG=false

# Generar APP_KEY
php8.3 artisan key:generate

# Instalar dependencias PHP (Composer)
# Si no tienes Composer:
sudo apt install -y composer
composer install --no-dev --optimize-autoloader

# Instalar dependencias frontend y compilar
sudo apt install -y npm
npm install
npm run build

# Cachear config, rutas, vistas
php8.3 artisan config:cache
php8.3 artisan route:cache
php8.3 artisan view:cache

# Migrar y seedear
php8.3 artisan migrate --seed

```bash
git clone https://github.com/tu-usuario/pos.git
cd pos

cp .env.example .env
# Editar .env con credenciales de BD

composer install
npm install && npm run build

php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

## Usuario por defecto

| Email | Contraseña | Rol |
|---|---|---|
| admin@pos.com | admin123 | Admin |

## Despliegue

Ver [DEPLOYMENT.md](DEPLOYMENT.md) para instrucciones detalladas de despliegue en producción.
