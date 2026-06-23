# Manual de Despliegue — POS System

## Requisitos del Servidor

- Ubuntu 22.04 LTS o superior
- Apache 2.4+ con mod_rewrite habilitado
- PHP 8.3 o superior con extensiones: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `xml`, `zip`
- MySQL 8.0+ o MariaDB 10.6+
- Composer 2.x
- Node.js 20+ y NPM (solo para compilar assets)

---

## 1. Despliegue por SSH (recomendado)

### 1.1 Conectarse al servidor

```bash
ssh usuario@midominio.com
```

### 1.2 Instalar dependencias del sistema

```bash
sudo apt update
sudo apt install -y apache2 mysql-server php8.3 php8.3-cli php8.3-common \
  php8.3-mysql php8.3-xml php8.3-curl php8.3-mbstring php8.3-zip \
  php8.3-bcmath php8.3-gd php8.3-intl composer unzip git
```

### 1.3 Configurar MySQL

```bash
sudo mysql -u root
```

```sql
CREATE DATABASE pos_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pos_user'@'localhost' IDENTIFIED BY 'contraseña_segura';
GRANT ALL PRIVILEGES ON pos_system.* TO 'pos_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 1.4 Clonar el proyecto

```bash
cd /var/www
sudo git clone https://github.com/tu-usuario/pos.git
sudo chown -R $USER:www-data pos
cd pos
```

### 1.5 Configurar entorno

```bash
cp .env.example .env
nano .env
```

Editar las siguientes líneas en `.env`:

```env
APP_NAME="POS System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://midominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=pos_user
DB_PASSWORD=contraseña_segura
```

> **Importante:** Generar una nueva `APP_KEY` en producción:
> ```bash
> php artisan key:generate
> ```

### 1.6 Instalar dependencias PHP

```bash
composer install --no-dev --optimize-autoloader
```

### 1.7 Compilar assets

```bash
npm install --ignore-scripts
npm run build
```

### 1.8 Migrar base de datos y seeders

```bash
php artisan migrate --seed
```

### 1.9 Optimizar Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 1.10 Configurar permisos

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache public
```

### 1.11 Configurar Apache

```bash
sudo nano /etc/apache2/sites-available/pos.conf
```

```apache
<VirtualHost *:80>
    ServerName midominio.com
    ServerAdmin admin@midominio.com
    DocumentRoot /var/www/pos/public

    <Directory /var/www/pos/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/pos_error.log
    CustomLog ${APACHE_LOG_DIR}/pos_access.log combined
</VirtualHost>
```

Habilitar el sitio y módulos:

```bash
sudo a2ensite pos.conf
sudo a2dissite 000-default.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 1.12 Configurar HTTPS (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d midominio.com
```

### 1.13 Programar tareas (Cron)

Agregar el scheduler de Laravel al crontab:

```bash
crontab -e
```

```cron
* * * * * cd /var/www/pos && php artisan schedule:run >> /dev/null 2>&1
```

---

## 2. Despliegue por FTP

> **Nota:** FTP es menos seguro que SSH. Se recomienda SFTP/FTPS.

### 2.1 Preparar archivos localmente

```bash
# Compilar assets localmente
npm install --ignore-scripts
npm run build

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Limpiar archivos innecesarios
rm -rf node_modules .git tests
```

### 2.2 Subir archivos vía FTP

Usando FileZilla o cualquier cliente FTP:

1. Conectar al servidor (Host: midominio.com, Usuario: ftp_user, Contraseña: ***)
2. Subir TODO el contenido del proyecto a `/public_html/` o `/var/www/pos/`
3. Asegurarse de subir la carpeta `vendor/`, `storage/` y `bootstrap/cache/`

### 2.3 Configurar permisos en servidor

```bash
chmod -R 775 storage bootstrap/cache
chmod -R 775 public
```

### 2.4 Configurar Apache en servidor

Solicitar al administrador del servidor que configure el VirtualHost apuntando a la carpeta `public/`.

### 2.5 Base de datos

1. Exportar base de datos local:
   ```bash
   mysqldump -u root -p pos_system > pos_backup.sql
   ```
2. Importar en servidor vía phpMyAdmin o línea de comandos:
   ```bash
   mysql -u pos_user -p pos_system < pos_backup.sql
   ```

---

## 3. Post-Despliegue

### 3.1 Verificar instalación

```bash
php artisan about
```

### 3.2 Crear enlace simbólico de storage

```bash
php artisan storage:link
```

### 3.3 Usuario predeterminado (seeder)

| Email | Contraseña | Rol |
|-------|-----------|------|
| admin@pos.com | admin123 | Admin |

> **Cambiar contraseña inmediatamente después del primer inicio de sesión.**

### 3.4 Configuración adicional

- Configurar moneda por defecto en `/currencies`
- Establecer IVA global en `General Settings`
- Configurar formato de recibo térmico en `Settings → Receipt`

---

## 4. Mantenimiento

### Actualizar código vía SSH

```bash
cd /var/www/pos
git pull origin main
composer install --no-dev --optimize-autoloader
npm install --ignore-scripts
npm run build
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Respaldar base de datos

```bash
# Agregar al crontab para respaldo diario
0 3 * * * mysqldump -u pos_user -p'contraseña' pos_system > /backups/pos_$(date +\%Y\%m\%d).sql
```

### Logs

Los errores se registran en:
- Laravel: `/var/www/pos/storage/logs/laravel.log`
- Apache: `/var/log/apache2/pos_error.log`

---

## 5. Solución de Problemas

| Problema | Causa | Solución |
|----------|-------|----------|
| Error 500 | Permisos incorrectos | `chmod -R 775 storage bootstrap/cache` |
| Página en blanco | APP_DEBUG=false | Revisar `storage/logs/laravel.log` |
| Error 404 en rutas | mod_rewrite deshabilitado | `sudo a2enmod rewrite && sudo systemctl restart apache2` |
| No carga CSS/JS | Assets no compilados | Ejecutar `npm run build` |
| Error de conexión BD | Credenciales incorrectas | Verificar `.env` |
