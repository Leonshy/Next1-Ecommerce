# Next1 E-Commerce

Plataforma de comercio electrónico desarrollada para **Grupo Next1 E.A.S** — [next1.com.py](https://next1.com.py)

Desarrollado por [Webparaguay](https://webparaguay.com)

Construida sobre Laravel 12 con Livewire, Alpine.js y Tailwind CSS.

---

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.2 · Laravel 12 |
| Frontend reactivo | Livewire 4 · Alpine.js 3 |
| Estilos | Tailwind CSS 3 |
| Build tool | Vite 7 |
| DB local | SQLite |
| DB producción | MySQL (Plesk) |
| Autenticación | Laravel Breeze + Laravel Socialite (Google) |

---

## Requisitos

- PHP >= 8.2 con extensiones: `pdo_sqlite`, `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`
- Composer
- Node.js >= 18 + npm
- SQLite (local) / MySQL (producción)

---

## Instalación local

```bash
# 1. Clonar el repositorio
git clone git@github.com:Leonshy/Next1-Ecommerce.git
cd Next1-Ecommerce

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate

# 5. Ejecutar migraciones y seeders
php artisan migrate

# 6. Crear el enlace simbólico de storage
php artisan storage:link
```

### Variables de entorno mínimas (`.env` local)

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

FILESYSTEM_DISK=public
```

### Iniciar el servidor de desarrollo

```bash
# Terminal 1 — Backend
php artisan serve

# Terminal 2 — Frontend (watch + HMR)
npm run dev
```

Acceder en: **http://localhost:8000**

---

## Compilar assets para producción

> El servidor de producción usa Node 11/12, que no es compatible con Vite. Los assets **se compilan localmente** y se suben al servidor.

```bash
npm run build
```

Esto genera `public/build/`. Subir esa carpeta al servidor con SCP antes de hacer el deploy.

---

## Estructura del proyecto

```
app/
├── Http/
│   └── Controllers/
│       ├── Admin/          # Controladores del panel de administración
│       └── ...             # Controladores del frontend
├── Livewire/               # Componentes Livewire
│   ├── CheckoutForm.php    # Checkout en 3 pasos
│   ├── Cart.php            # Carrito reactivo
│   ├── PredictiveSearch.php
│   ├── WishlistButton.php
│   └── NewsletterForm.php
├── Models/                 # Modelos Eloquent
└── Services/               # Servicios de terceros
    ├── BancardService.php
    ├── PagoparService.php
    ├── SmtpEmailService.php
    ├── AnalyticsService.php
    └── HCaptchaService.php

resources/
├── views/
│   ├── admin/              # Vistas del panel admin
│   ├── livewire/           # Vistas de componentes Livewire
│   ├── partials/           # Header, footer, etc.
│   └── ...                 # Páginas públicas

database/
└── migrations/             # Todas las migraciones

routes/
└── web.php                 # Todas las rutas
```

---

## Funcionalidades

### Tienda pública

- **Catálogo de productos** con filtros por categoría, marca, rango de precio y búsqueda en tiempo real (Livewire)
- **Detalle de producto** con galería de imágenes, variantes y stock
- **Carrito** persistente (DB para usuarios autenticados, sesión para invitados)
- **Wishlist** (lista de deseos) para usuarios autenticados
- **Checkout en 3 pasos** (Livewire): dirección → envío → pago
  - Direcciones guardadas por usuario
  - Cálculo de costo de envío por zona/departamento/ciudad
  - Banner de progreso hacia envío gratis
- **Gift Cards** con código y balance aplicable en checkout
- **Descuentos por medio de pago** (% configurable por proveedor desde el admin)
- **Campañas de descuento** por categoría o marca
- **Hero slider** con imágenes, overlay y enlace configurable desde admin
- **Banners promocionales** con imagen y overlay
- **Newsletter** con verificación de email
- **Modo mantenimiento** configurable desde admin

### Autenticación

- Registro con verificación de email
- Login con Google (Socialite)
- Autenticación en 2 factores (2FA) para admin
- Sesión segura con timeout para el panel admin

### Panel de administración (`/admin`)

Roles: `admin` (acceso total) · `vendedor` (acceso parcial, sin config ni usuarios)

| Sección | Descripción |
|---------|-------------|
| **Dashboard** | Métricas: ventas, pedidos, usuarios, stock bajo |
| **Productos** | CRUD completo, import/export CSV y Excel, gestión de imágenes, precios de oferta, stock |
| **Categorías** | Con subcategorías anidadas |
| **Marcas** | CRUD |
| **Etiquetas** | CRUD |
| **Pedidos** | Listado, detalle, cambio de estado, descarga de comprobante |
| **Usuarios** | CRUD de usuarios y roles (solo admin) |
| **Multimedia** | Biblioteca de medios centralizada con media picker |
| **Campañas** | Descuentos por porcentaje o monto fijo, filtrado por categoría/marca |
| **Banners** | Banners promocionales con imagen y overlay |
| **Hero Slides** | Slides del carousel del home |
| **Newsletter** | Lista de suscriptores + exportación CSV |
| **Gift Cards** | Creación, recarga y estado de gift cards |
| **Info Tienda** | Logo, nombre, redes sociales, datos de contacto |
| **Páginas** | Quiénes somos, FAQ, Términos, Política de privacidad |
| **Config Envíos** | Zonas por departamento/ciudad, envío gratis, retiro en tienda |
| **Config Pagos** | Credenciales de pasarelas + descuentos por método |
| **Config SEO** | Meta tags por página |
| **Analytics** | GA4, Meta Pixel, Google Tag Manager |
| **Email / SMTP** | Configuración SMTP + editor de plantillas de email |
| **hCaptcha** | Protección anti-bots en formularios |
| **Auditoría** | Log de acciones del admin |
| **Mantenimiento** | Activar modo mantenimiento con mensaje personalizado |

---

## Pasarelas de pago

### Bancard VPOS
- Activo en producción
- Configuración: `public_key`, `private_key`, entorno sandbox/producción
- Flujo: al confirmar el pedido se abre un iframe de Bancard sobre la página

### Transferencia Bancaria
- Siempre disponible como fallback
- Datos bancarios configurables desde admin (banco, titular, cuenta, RUC)
- El cliente sube el comprobante (JPG/PNG/PDF) al confirmar el pedido
- Comprobantes almacenados en `storage/app/public/receipts/`

### Pagopar
- Integración completa certificada (3 pasos aprobados)
- Crear orden, webhook de notificación y consulta de estado
- Webhook en `/webhooks/pagopar`
- Credenciales y entorno (sandbox/producción) configurables desde admin

### Coinbase Commerce / CoinsPaid
- UI de configuración de credenciales disponible
- Integración funcional pendiente de implementar

---

## Configuración de envíos

Desde **Admin → Configuración → Envíos**:

- **Envío propio**: habilitar/deshabilitar por departamento, con tarifas base y tarifas custom por ciudad/distrito
- **Ciudades inactivas**: deshabilitar ciudades específicas dentro de un departamento
- **Envío gratis**: umbral de monto mínimo configurable (se muestra barra de progreso en checkout)
- **Retiro en tienda**: opción sin costo en el checkout
- **AEX**: cotización automática de envíos (integración parcial)

---

## Deploy a producción

### Datos del servidor

- **URL:** https://next1.com.py
- **SSH:** `ssh -p 53931 nextcomp@177.251.252.12`
- **Ruta del proyecto:** `/var/www/vhosts/next1.com.py/httpdocs/`
- **DB:** MySQL, base `nextcomp_db`, usuario `nextcomp_usr`
- **Panel:** Plesk con nginx

### Proceso de deploy (código)

```bash
# 1. Local: compilar assets si hubo cambios en CSS/JS/Alpine
npm run build
scp -P 53931 -r public/build nextcomp@177.251.252.12:/var/www/vhosts/next1.com.py/httpdocs/public/

# 2. Entrar al servidor
ssh -p 53931 nextcomp@177.251.252.12

# 3. Traer cambios del repositorio
cd /var/www/vhosts/next1.com.py/httpdocs
git pull origin main

# 4. Correr migraciones pendientes (nunca migrate:fresh en producción)
php artisan migrate

# 5. Limpiar y regenerar cachés
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### Proceso de deploy (datos local → producción)

Solo usar cuando se quiere llevar datos cargados localmente (productos, categorías, etc.):

```bash
# Local: exportar datos en formato MySQL
php artisan db:export-mysql
# Genera: mysql_data.sql

# Subir el SQL al servidor
scp -P 53931 mysql_data.sql nextcomp@177.251.252.12:/tmp/

# Subir archivos de media si cambiaron
scp -P 53931 storage/app/public/media/* nextcomp@177.251.252.12:/var/www/vhosts/next1.com.py/httpdocs/storage/app/public/media/

# En el servidor: importar datos
mysql -h localhost -u nextcomp_usr -p'PASS' nextcomp_db < /tmp/mysql_data.sql

# Verificar permisos de media
chmod 755 /var/www/vhosts/next1.com.py/httpdocs/storage/app/public/media/
```

> **Importante:** Producción tiene datos reales (pedidos, usuarios, stock). Nunca importar datos de local a producción a menos que sea intencional.

### Variables de entorno en producción

Verificar en el `.env` del servidor antes de cada deploy:

```dotenv
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
APP_URL=https://next1.com.py

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=nextcomp_db
DB_USERNAME=nextcomp_usr
DB_PASSWORD=...

FILESYSTEM_DISK=public
```

---

## Modelos principales

| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| `User` | `users` | Usuarios con roles (`admin`, `vendedor`, `cliente`) |
| `Product` | `products` | Productos con precio, `original_price`, stock, slug |
| `Category` | `categories` | Categorías con subcategorías |
| `Brand` | `brands` | Marcas |
| `Order` + `OrderItem` | `orders`, `order_items` | Pedidos y sus ítems |
| `Cart` | `carts` | Carrito persistente por usuario |
| `UserAddress` | `user_addresses` | Direcciones guardadas |
| `ShippingSetting` | `shipping_settings` | Config de envíos y zonas |
| `PaymentSetting` | `payment_settings` | Config de pasarelas + descuento % |
| `GiftCard` | `gift_cards` | Gift cards con balance |
| `Campaign` | `campaigns` | Campañas de descuento |
| `PromoBanner` + `HeroSlide` | — | Marketing visual |
| `MediaFile` | `media_files` | Biblioteca de medios centralizada |
| `SiteContent` | `site_contents` | Contenido de páginas (key→metadata JSON) |
| `EmailTemplate` | `email_templates` | Plantillas de email editables |

---

## Flujo del checkout

El componente Livewire `CheckoutForm` maneja 3 pasos:

```
Paso 1: Dirección
  ├── Seleccionar dirección guardada o ingresar nueva
  ├── Departamento y ciudad (filtrados por zonas activas)
  └── Opción retiro en tienda

Paso 2: Envío
  └── Muestra costo calculado o "Gratis" si aplica el umbral

Paso 3: Pago
  ├── Muestra métodos disponibles según config admin
  ├── Badge "X% OFF" en métodos con descuento configurado
  └── Abre iframe Bancard o muestra datos de transferencia

Resumen (sidebar, siempre visible)
  ├── Ítems del carrito
  ├── Banner de progreso hacia envío gratis
  ├── Descuento Gift Card (si aplica)
  ├── Descuento por medio de pago (si aplica)
  ├── Costo de envío
  └── Total
```

### Estados de un pedido

`pendiente` → `pendiente_transferencia` → `confirmado` → `procesando` → `enviado` → `entregado` / `cancelado`

---

## Notas de desarrollo

- **Assets**: Tailwind compila classes en build. No usar clases con valores dinámicos en PHP/Blade (`bg-[${color}]`); usar clases completas o configurar `safelist`.
- **Colores custom**: definidos en `tailwind.config.js` (ej: `background`, `foreground`, `destructive`). No en variables CSS.
- **Cart**: usuarios autenticados → modelo `Cart` en DB; invitados → `session('cart')`. El método `getCart()` en `CheckoutForm` maneja ambos casos.
- **Formularios Livewire**: no anidar `<form>` HTML. Para submit a form diferente usar el atributo `form="id"`.
- **Subcategorías flyout**: en sidebar del home usa `position: fixed` con coordenadas calculadas via Alpine.js `getBoundingClientRect()` para escapar contenedores con `overflow: hidden`.

---

## Repositorio

**GitHub:** [github.com/Leonshy/Next1-Ecommerce](https://github.com/Leonshy/Next1-Ecommerce)  
**Rama principal:** `main`
