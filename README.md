# POS System

Sistema POS para administración de ventas, compras, inventario y clientes.

## Requisitos

- PHP 8.3+
- MySQL 8.0+ / MariaDB 10.6+
- Composer 2.x
- Node.js 20+ y NPM
- Apache 2.4+ con mod_rewrite

## Instalación

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
