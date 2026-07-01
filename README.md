# Buzón de Quejas, Sugerencias y Reportes — Solariega Cenit

Sistema interno para recibir quejas, sugerencias, incidentes y reportes de los
empleados de Solariega Cenit, con un formulario público (accesible por QR) y
un portal administrativo para dar seguimiento.

Stack: Laravel 13 · Inertia.js v3 · Vue 3 · TypeScript · Tailwind CSS v4 ·
shadcn-vue · Lucide Icons · MySQL/MariaDB.

## Instalación local

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Edita `.env` con los datos de tu base de datos MySQL/MariaDB y tu SMTP real:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=buzon_solariega
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.tu-proveedor.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="buzon@solariegacenit.com"
MAIL_FROM_NAME="Buzón Solariega Cenit"

# Correo que recibe cada nueva solicitud
BUZON_MAIL_TO=buzon@solariegacenit.com
```

Crea la base de datos y ejecuta las migraciones con datos de ejemplo:

```bash
php artisan migrate --seed
php artisan storage:link
```

El seeder crea un usuario administrador para el primer acceso:

- **Correo:** `admin@solariegacenit.com`
- **Contraseña:** `password`

> ⚠️ Cambia esta contraseña de inmediato (o crea un nuevo administrador y
> desactiva este) antes de exponer el sistema en producción.

Levanta el entorno de desarrollo:

```bash
composer dev
```

Esto corre en paralelo `php artisan serve`, la cola (`queue:listen`, usada
para el envío de correo) y `npm run dev`. La app queda disponible en
`http://localhost:8000/reportar` (formulario público) y
`http://localhost:8000/login` (panel administrativo).

## Rutas principales

| Ruta | Descripción | Acceso |
|---|---|---|
| `/reportar` | Formulario público de quejas/sugerencias/reportes | Público |
| `/login` | Acceso al panel | Público |
| `/dashboard` | Resumen y gráficas | Autenticado |
| `/solicitudes` | Listado y filtros | Autenticado |
| `/solicitudes/{id}` | Detalle y seguimiento interno | Autenticado |
| `/reportes` | Filtros avanzados + exportación Excel/CSV/PDF | Autenticado |
| `/usuarios` | Alta/edición de cuentas del panel | Solo administradores |
| `/configuracion/qr` | Enlace público + código QR descargable | Autenticado |

## Comandos útiles

```bash
php artisan test          # Pest
vendor/bin/pint           # Estilo de código PHP
vendor/bin/phpstan analyse # Análisis estático (Larastan)
npm run lint               # ESLint
npm run types:check        # vue-tsc
npm run build               # Build de producción de assets
```

## Despliegue en VPS (Nginx)

1. Clona el proyecto y configura `.env` como se indicó arriba, con
   `APP_ENV=production`, `APP_DEBUG=false` y `APP_URL=https://buzon.solariegacenit.com`.
2. Instala dependencias sin las de desarrollo y compila los assets:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

3. Genera la key (si no existe) y migra la base de datos:

   ```bash
   php artisan key:generate
   php artisan migrate --force --seed
   php artisan storage:link
   ```

4. Da permisos de escritura al servidor web sobre `storage` y
   `bootstrap/cache` (los adjuntos privados viven en
   `storage/app/private/buzon/{folio}/`):

   ```bash
   chown -R www-data:www-data storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

5. Cachea configuración, rutas y vistas:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. Configura un worker de colas persistente (Supervisor) para que el correo
   de notificación se envíe en segundo plano:

   ```
   php artisan queue:work --sleep=3 --tries=3
   ```

7. Configuración básica de Nginx:

   ```nginx
   server {
       listen 80;
       server_name buzon.solariegacenit.com;
       root /var/www/formularioSolariega/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/run/php/php8.3-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

   Agrega HTTPS (por ejemplo con Certbot) apuntando el subdominio
   `buzon.solariegacenit.com` al VPS.

## Notas de seguridad

- Los adjuntos se guardan en `storage/app/private/...` (disco `local`), fuera
  del directorio público; solo se pueden descargar autenticado desde
  `/solicitudes/{id}/adjuntos/{adjunto}`.
- El formulario público está limitado a 5 envíos por minuto por IP
  (`RateLimiter::for('reportar', ...)` en `AppServiceProvider`).
- Las solicitudes anónimas nunca almacenan nombre ni contacto, sin importar
  lo que el navegador envíe (`StoreRequestRequest::prepareForValidation`).
- Solo los usuarios con rol `admin` pueden gestionar `/usuarios`; las cuentas
  desactivadas (`is_active = false`) pierden acceso al panel de inmediato.
