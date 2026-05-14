# Manual Técnico — Arquitectura del Sistema

**Proyecto:** Next1 E-Commerce  
**Cliente:** Grupo Next1 E.A.S — [next1.com.py](https://next1.com.py)  
**Desarrollado por:** [Webparaguay](https://webparaguay.com)  
**Stack:** Laravel 12 · Livewire · Alpine.js · Tailwind CSS

---

## Índice

1. [Stack tecnológico](#1-stack-tecnológico)
2. [Estructura de directorios](#2-estructura-de-directorios)
3. [Base de datos — Esquema completo](#3-base-de-datos--esquema-completo)
4. [Rutas del sistema](#4-rutas-del-sistema)
5. [Componentes Livewire](#5-componentes-livewire)
6. [Servicios de terceros](#6-servicios-de-terceros)
7. [Sistema de pagos](#7-sistema-de-pagos)
8. [Sistema de envíos](#8-sistema-de-envíos)
9. [Sistema de diseño (Tailwind)](#9-sistema-de-diseño-tailwind)
10. [Autenticación y seguridad](#10-autenticación-y-seguridad)
11. [Assets y build pipeline](#11-assets-y-build-pipeline)
12. [Variables de entorno](#12-variables-de-entorno)

---

## 1. Stack tecnológico

| Capa | Tecnología | Versión |
|------|-----------|---------|
| Backend | PHP | 8.2+ |
| Framework | Laravel | 12 |
| Frontend reactivo | Livewire | 3 |
| Interactividad JS | Alpine.js | 3 |
| Estilos | Tailwind CSS | 3 |
| Build tool | Vite | 7 |
| DB local | SQLite | — |
| DB producción | MySQL | — (Plesk) |
| Autenticación social | Laravel Socialite | — |
| Autenticación 2FA | Custom (TOTP + email code) | — |

**Servidor de producción:**
- URL: `https://next1.com.py`
- SSH: `ssh -p 53931 nextcomp@177.251.252.12`
- Ruta: `/var/www/vhosts/next1.com.py/httpdocs/`
- Panel: Plesk con nginx
- Node.js: no disponible en servidor (assets se compilan localmente)

---

## 2. Estructura de directorios

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/              # 18 controladores del panel admin
│   │   ├── Auth/               # Controladores de autenticación
│   │   └── ...                 # Controladores públicos
│   └── Middleware/
├── Livewire/                   # 5 componentes Livewire
│   ├── Cart.php
│   ├── CheckoutForm.php
│   ├── NewsletterForm.php
│   ├── PredictiveSearch.php
│   └── WishlistButton.php
├── Models/                     # 32 modelos Eloquent
└── Services/                   # Integraciones con terceros
    ├── AexService.php
    ├── AnalyticsService.php
    ├── BancardService.php
    ├── HCaptchaService.php
    ├── PagoparService.php
    └── SmtpEmailService.php

resources/
├── css/
│   └── app.css                 # Tailwind base + componentes custom
├── js/
│   └── app.js
└── views/
    ├── admin/                  # Vistas del panel admin
    ├── auth/                   # Vistas de autenticación
    ├── livewire/               # Vistas de componentes Livewire
    ├── partials/               # Header, footer, layouts parciales
    ├── products/               # Catálogo y detalle de producto
    └── ...                     # Páginas públicas (home, checkout, etc.)

database/
└── migrations/                 # 28 migraciones en total

routes/
└── web.php                     # Todas las rutas del sistema

public/
└── build/                      # Assets compilados (generados con `npm run build`)

docs/                           # Documentación del proyecto
```

---

## 3. Base de datos — Esquema completo

### Migraciones en orden cronológico

| Archivo | Tabla(s) creada(s) / modificada(s) |
|---------|-----------------------------------|
| `0001_01_01_000000` | `users`, `password_reset_tokens`, `sessions` |
| `0001_01_01_000001` | `cache`, `cache_locks` |
| `0001_01_01_000002` | `jobs`, `job_batches`, `failed_jobs` |
| `2026_04_11_000001` | `profiles` |
| `2026_04_11_000002` | `categories`, `brands`, `tags`, `product_tag` |
| `2026_04_11_000003` | `products`, `product_images` |
| `2026_04_11_000004` | `orders`, `order_items` |
| `2026_04_11_000005` | `invoices`, `billing_data` |
| `2026_04_11_000006` | `user_addresses`, `wishlists` |
| `2026_04_11_000007` | `site_contents`, `newsletter_subscribers`, `promo_banners`, `hero_slides`, `campaigns`, `gift_cards`, `seo_settings`, `pages` |
| `2026_04_11_000008` | `shipping_settings`, `payment_settings`, `analytic_settings` |
| `2026_04_11_000009` | `media_files`, `media_usages` |
| `2026_04_13_*` | `users` → agrega `google_id` |
| `2026_04_13_*` | `carts` |
| `2026_04_16_*` | `orders` → agrega campos de transferencia bancaria |
| `2026_04_17_*` | `newsletter_subscribers` → agrega campos de verificación |
| `2026_04_19_*` | `campaigns` → agrega `category_id`, `brand_id` |
| `2026_04_20_*` | `users` → agrega 2FA fields |
| `2026_04_20_*` | `two_factor_codes` |
| `2026_04_20_*` | `users` → agrega campos de seguridad (intentos fallidos, bloqueo) |
| `2026_04_20_*` | `admin_audit_logs` |
| `2026_04_20_*` | `promo_banners` → agrega overlay de imagen |
| `2026_04_20_*` | `email_templates` |
| `2026_04_22_*` | `orders` → agrega campos de Pagopar |
| `2026_04_22_*` | `orders` → actualiza enum de estados |
| `2026_04_22_*` | `products` → agrega `descripcion_corta`, `descripcion_larga` |
| `2026_04_23_*` | `payment_settings` → agrega `discount_percent`, `discount_label` |
| `2026_05_12_*` | `campaigns` → agrega `badge_color` |

### Modelos principales y sus relaciones

```
User
 ├── hasOne  Profile
 ├── hasMany Order
 ├── hasMany Cart
 ├── hasMany UserAddress
 └── belongsToMany Product (wishlists)

Product
 ├── belongsTo  Category
 ├── belongsTo  Brand
 ├── hasMany    ProductImage
 ├── belongsToMany Tag
 └── belongsToMany Campaign

Order
 ├── belongsTo  User (nullable — invitados)
 ├── hasMany    OrderItem
 └── belongsTo  GiftCard (nullable)

Category
 ├── belongsTo  Category (parent — subcategorías)
 └── hasMany    Category (children)

Campaign
 ├── belongsTo  Category (nullable — filtro de campaña)
 ├── belongsTo  Brand    (nullable — filtro de campaña)
 └── belongsToMany Product

MediaFile
 └── hasMany MediaUsage
```

### Estados de un pedido

```
pendiente → pendiente_transferencia → confirmado → procesando → enviado → entregado
                                                                        → cancelado
```

---

## 4. Rutas del sistema

### Rutas públicas

| Método | URI | Controlador | Descripción |
|--------|-----|-------------|-------------|
| GET | `/` | `HomeController@index` | Home con slider, campañas, productos |
| GET | `/productos` | `ProductController@index` | Catálogo con filtros |
| GET | `/productos/{slug}` | `ProductController@show` | Detalle de producto |
| GET | `/api/search` | `ProductController@search` | Búsqueda predictiva (AJAX) |
| GET | `/checkout` | `CheckoutController@index` | Checkout (Livewire) |
| POST | `/checkout/shipping` | `CheckoutController@shipping` | Calcular costo envío |
| POST | `/checkout/confirmar` | `CheckoutController@confirm` | Confirmar pedido |
| GET | `/checkout/gracias/{order}` | `CheckoutController@thanks` | Página de confirmación |
| POST | `/webhooks/bancard` | `CheckoutController@bancardWebhook` | Webhook de Bancard |
| POST | `/webhooks/pagopar` | `CheckoutController@pagoparWebhook` | Webhook de Pagopar |
| GET | `/pagopar/retorno` | `CheckoutController@pagoparReturn` | Retorno desde Pagopar |
| GET | `/mi-cuenta/*` | `AccountController` | Pedidos, direcciones, perfil |
| GET | `/nosotros` | `PageController@about` | Página "Quiénes somos" |
| GET | `/faq` | `PageController@faq` | FAQ |
| GET | `/terminos` | `PageController@terms` | Términos y condiciones |
| GET | `/privacidad` | `PageController@privacy` | Política de privacidad |
| GET | `/gift-cards` | `PageController@giftCards` | Info de gift cards |

> Los webhooks de Bancard y Pagopar están excluidos del middleware CSRF.

### Rutas del panel admin (`/admin`)

| Sección | URI base | Acceso |
|---------|----------|--------|
| Dashboard | `/admin` | admin + vendedor |
| Productos | `/admin/productos` | admin + vendedor |
| Categorías | `/admin/categorias` | admin + vendedor |
| Marcas | `/admin/marcas` | admin + vendedor |
| Etiquetas | `/admin/tags` | admin + vendedor |
| Pedidos | `/admin/pedidos` | admin + vendedor |
| Marketing | `/admin/marketing/*` | admin + vendedor |
| Gift Cards | `/admin/gift-cards` | admin + vendedor |
| Usuarios | `/admin/usuarios` | **solo admin** |
| Multimedia | `/admin/media` | **solo admin** |
| Contenido | `/admin/contenido` | **solo admin** |
| Configuración | `/admin/configuracion/*` | **solo admin** |
| Auditoría | `/admin/auditoria` | **solo admin** |

**Middlewares aplicados a todas las rutas admin:**
- `auth` — usuario autenticado
- `role:admin,vendedor` — rol válido
- `admin.timeout` — sesión con timeout de inactividad
- `admin.audit` — registro de acciones en `admin_audit_logs`

---

## 5. Componentes Livewire

### Cart.php

Maneja el carrito de compras reactivo.

**Fuente de datos:**
- **Usuario autenticado:** modelo `Cart` en base de datos
- **Invitado:** `session('cart')` (array serializado)

**Métodos clave:**
- `addItem($productId, $qty)` — agrega ítem, guarda `original_price` desde DB
- `removeItem($productId)` — elimina ítem
- `updateQty($productId, $qty)` — actualiza cantidad
- `loadItems()` — carga ítems y ejecuta `enrichWithOriginalPrice()` para ítems sin `original_price`
- `enrichWithOriginalPrice(array $items)` — consulta DB para agregar precio original a ítems existentes

**Eventos Livewire emitidos:**
- `cart-updated` — escuchado por el header para actualizar contador

---

### CheckoutForm.php

Componente de checkout en 3 pasos.

**Pasos:**

| Paso | Contenido |
|------|-----------|
| 1 | Dirección: seleccionar guardada o ingresar nueva, departamento/ciudad |
| 2 | Envío: muestra costo calculado o "Gratis" si aplica umbral |
| 3 | Pago: métodos disponibles, badge de descuento, confirmación |

**Propiedades principales:**
- `$step` — paso actual (1/2/3)
- `$shippingMethod` — `envio` o `pickup`
- `$paymentMethod` — `bancard`, `pagopar`, `transferencia`
- `$giftCardCode`, `$giftCardDiscount`
- `$paymentDiscount` — descuento por medio de pago (%)
- `$addressMode` — `saved` o `new`

**Lógica de descuentos en el resumen:**
1. Descuento de campaña (aplicado sobre el precio del producto)
2. Descuento de gift card (monto fijo deducido del subtotal)
3. Descuento por medio de pago (% sobre el total — configurado en admin)

---

### PredictiveSearch.php

Búsqueda en tiempo real con debounce.

- Escucha cambios en `$query` (propiedad wire:model)
- Llama a `ProductController@search` vía Livewire
- Muestra dropdown con resultados (imagen, nombre, precio)
- Se cierra al hacer clic fuera (Alpine.js `@click.outside`)

---

### WishlistButton.php

Toggle de wishlist para usuarios autenticados.

- Requiere autenticación (redirige a login si invitado)
- Guarda en tabla `wishlists`
- Emite evento `wishlist-updated`

---

### NewsletterForm.php

Formulario de suscripción al newsletter.

- Verifica duplicados en `newsletter_subscribers`
- Envía email de verificación vía `SmtpEmailService`
- Usa hCaptcha si está configurado

---

## 6. Servicios de terceros

### BancardService.php

Integración con **Bancard VPOS** (pasarela de tarjetas de crédito/débito paraguaya).

```
Configuración en: Admin → Config Pagos → Bancard
Tabla: payment_settings (provider = 'bancard')
```

**Flujo:**
1. Al confirmar pedido con método `bancard`, se llama a `createPayment()`
2. El servicio genera un token MD5: `md5(privateKey + shopProcessId + amount + currency)`
3. Se solicita a Bancard la URL del iframe de pago
4. El iframe se abre sobre la página de checkout
5. Bancard notifica el resultado vía webhook POST a `/webhooks/bancard`

**Entornos:**
- Sandbox: `https://vpos.infonet.com.py:8888`
- Producción: `https://vpos.infonet.com.py`

---

### PagoparService.php

Integración con **Pagopar** (wallet paraguayo — integración certificada en 3 pasos).

```
Configuración en: Admin → Config Pagos → Pagopar
Tabla: payment_settings (provider = 'pagopar')
```

**Tokens (SHA1):**
| Token | Fórmula |
|-------|---------|
| `tokenOrden` | `SHA1(privateKey + idPedido + monto)` |
| `tokenConsulta` | `SHA1(privateKey + 'CONSULTA')` |
| `tokenReversar` | `SHA1(privateKey + 'PEDIDO-REVERSAR')` |
| `tokenWebhook` | `SHA1(privateKey + hashPedido)` |

**Flujo completo (certificado):**

```
Paso 1 — Crear orden
  createOrder() → POST a API Pagopar → retorna hashPedido + URL de pago
  Usuario es redirigido a CHECKOUT_URL/hashPedido

Paso 2 — Notificación webhook
  Pagopar POST a /webhooks/pagopar con resultado y hashPedido
  El sistema verifica tokenWebhook y actualiza estado del pedido
  Responde con el payload 'resultado' → requerido por Pagopar para certificar

Paso 3 — Consulta de estado
  Tras recibir webhook, el sistema llama a queryOrder(hashPedido)
  Verifica el estado final de la transacción
  → Requerido por Pagopar como evidencia de implementación completa
```

**Constantes:**
- `API_BASE`: `https://api.pagopar.com/api`
- `CHECKOUT_URL`: `https://www.pagopar.com/pagos/`
- `FORMA_PAGO`: `9` (tarjetas Bancard)

---

### AnalyticsService.php

Genera los scripts HTML de tracking según configuración.

**Proveedores soportados:**
- Google Tag Manager (GTM)
- Google Analytics 4 (GA4)
- Meta Pixel (Facebook)

```
Configuración en: Admin → Analytics
Tabla: analytic_settings
```

---

### SmtpEmailService.php

Envío de emails transaccionales con plantillas editables.

```
Configuración en: Admin → Email / SMTP
Tabla: smtp_settings + email_templates
```

**Plantillas disponibles:**
- Verificación de email (registro)
- Confirmación de pedido
- Cambio de estado del pedido
- Verificación de newsletter
- Recuperación de contraseña

**Sintaxis de variables:** `{{nombre_variable}}` reemplazadas en `renderTemplate()`

---

### HCaptchaService.php

Protección anti-bots en formularios públicos.

```
Configuración en: Admin → hCaptcha
Tabla: hcaptcha_settings
```

- Se desactiva automáticamente si no hay credenciales configuradas
- Verifica token POST `h-captcha-response` contra API de hCaptcha
- Aplicado en: newsletter, registro, checkout (configurable por formulario)

---

### AexService.php

Integración con **AEX** para cotización automática de envíos (implementación parcial).

- Autenticación con usuario/contraseña en sandbox/producción
- Método `getToken()` para obtener token de sesión
- Método `validate()` para validar disponibilidad de zona

---

## 7. Sistema de pagos

### Métodos disponibles

| Método | Proveedor | Estado |
|--------|-----------|--------|
| Tarjeta de crédito/débito | Bancard VPOS | Activo en producción |
| Wallet / QR | Pagopar | Activo en producción (certificado) |
| Transferencia bancaria | Interno | Siempre disponible |
| Criptomonedas | Coinbase / CoinsPaid | UI lista, integración pendiente |

### Descuento por medio de pago

Cada método puede tener un descuento configurado desde el panel admin.

```
Admin → Config Pagos → [método] → discount_percent + discount_label
Tabla: payment_settings
```

El descuento se muestra como badge "X% OFF" en el paso 3 del checkout y se descuenta del total en el resumen lateral.

### Transferencia bancaria

- El cliente sube un comprobante (JPG/PNG/PDF) al confirmar el pedido
- El comprobante se almacena en `storage/app/public/receipts/`
- El admin verifica el comprobante y confirma el pedido manualmente
- Datos bancarios (banco, titular, cuenta, RUC) configurables desde admin

### Gift Cards

- Se crean desde Admin → Gift Cards con un código y un saldo inicial
- El cliente ingresa el código en el paso 3 del checkout
- El sistema valida el código, verifica saldo y aplica el descuento
- El saldo se descuenta de la gift card al confirmar el pedido

---

## 8. Sistema de envíos

```
Configuración en: Admin → Config Envíos
Tabla: shipping_settings
```

### Tipos de envío

| Tipo | Descripción |
|------|-------------|
| Envío propio | Tarifas por departamento + tarifas custom por ciudad |
| Envío gratis | Umbral de monto configurable (barra de progreso en checkout) |
| Retiro en tienda | Sin costo, dirección configurable desde admin |
| AEX | Cotización automática (integración parcial) |

### Lógica de cálculo

1. Se determina el departamento y ciudad del cliente (Paso 1 del checkout)
2. Se verifica si el departamento tiene envío habilitado
3. Si la ciudad tiene tarifa custom → se usa esa tarifa
4. Si no → se usa la tarifa base del departamento
5. Si el total del carrito supera el umbral de envío gratis → costo = 0
6. Si el cliente elige retiro en tienda → costo = 0

### Ciudades deshabilitadas

El admin puede deshabilitar ciudades específicas dentro de un departamento activo. Las ciudades deshabilitadas no aparecen en el selector del checkout.

---

## 9. Sistema de diseño (Tailwind)

### Paleta de colores

| Variable | Valor HSL | Uso |
|----------|-----------|-----|
| `primary` | 207 60% 28% (azul marino) | Botones, textos destacados, fondo admin |
| `secondary` / `accent` | 28 80% 52% (naranja) | Botones secundarios, acentos |
| `muted` | 210 20% 94% | Fondos suaves, separadores |
| `destructive` | 0 84% 60% (rojo) | Errores, badges de oferta |
| `background` | 210 25% 98% | Fondo general |
| `foreground` | 207 60% 28% | Texto principal |
| `border` | 210 20% 88% | Bordes y divisores |

Las variables se definen en `resources/css/app.css` como custom properties CSS y se referencian en `tailwind.config.js`.

### Tipografía

| Uso | Fuente |
|-----|--------|
| Cuerpo (`font-sans`) | Open Sans |
| Títulos (`font-heading`) | Roboto |

Ambas fuentes se cargan vía Google Fonts en los layouts Blade.

### Componentes CSS custom (en `app.css`)

| Clase | Descripción |
|-------|-------------|
| `.btn-primary` | Botón azul marino |
| `.btn-accent` | Botón naranja |
| `.product-card` | Card de producto con hover lift |
| `.section-title` | Encabezado de sección con barra de color + triángulo |
| `.category-list-item` | Ítem de lista de categorías |
| `.input-field` | Campo de formulario estilizado |
| `.badge-oferta` | Badge rojo "OFERTA" |
| `.badge-destacado` | Badge ámbar "DESTACADO" |
| `.badge-nuevo` | Badge azul "NUEVO" |
| `.badge-custom` | Badge personalizado (color primario) |
| `.badge-discount` | Badge de descuento (rojo, destructive) |

### Regla crítica — Clases dinámicas

> **Tailwind purga clases que no están escritas literalmente en el código.**

Si necesitás generar clases con valores dinámicos (ej: colores de campañas), usar `style` inline en vez de clases Tailwind:

```blade
{{-- MAL — Tailwind no incluirá bg-[#ff0000] en el build --}}
<div class="bg-[{{ $color }}]">

{{-- BIEN — Inline style siempre funciona --}}
<div style="background-color: {{ $color }}">
```

Para hover effects dinámicos, usar `onmouseover/onmouseout` con inline JS.

---

## 10. Autenticación y seguridad

### Flujos de autenticación

| Flujo | Descripción |
|-------|-------------|
| Registro clásico | Email + contraseña + verificación de email |
| Login con Google | Laravel Socialite → `SocialAuthController` |
| 2FA admin | Código de 6 dígitos vía email al iniciar sesión admin |
| Admin timeout | Sesión admin expira por inactividad (configurable) |

### Roles de usuario

| Rol | Permisos |
|-----|----------|
| `admin` | Acceso total al panel admin |
| `vendedor` | Panel admin sin usuarios, multimedia, contenido ni configuración |
| `cliente` | Solo tienda pública y cuenta personal |

### Seguridad adicional

- **hCaptcha:** protección anti-bots en formularios públicos
- **Admin Audit Log:** todas las acciones del panel admin se registran en `admin_audit_logs` (tabla, acción, ID del registro, IP, user agent)
- **Rate limiting:** aplicado en rutas de login y API
- **Bloqueo de cuenta:** tras N intentos fallidos de login, la cuenta se bloquea temporalmente (campos `failed_login_attempts`, `locked_until` en tabla `users`)
- **CSRF:** todos los formularios excepto webhooks externos

---

## 11. Assets y build pipeline

### Desarrollo local

```bash
# Terminal 1
php artisan serve          # Backend en http://localhost:8000

# Terminal 2
npm run dev                # Vite HMR — actualización en tiempo real
```

### Build para producción

El servidor de producción **no tiene Node.js**, por lo que el build se realiza localmente:

```bash
# 1. Compilar assets
npm run build
# Genera: public/build/ (manifest.json + assets con hash)

# 2. Subir al servidor
scp -P 53931 -r public/build nextcomp@177.251.252.12:/var/www/vhosts/next1.com.py/httpdocs/public/

# 3. En el servidor — limpiar caché de vistas
php artisan view:cache
```

> **Importante:** Hacer el build ANTES de hacer `git pull` en el servidor, o inmediatamente después, ya que las vistas Blade referencian los assets con hash del `manifest.json`.

### Archivos de entrada Vite

```javascript
// vite.config.js
input: ['resources/css/app.css', 'resources/js/app.js']
```

---

## 12. Variables de entorno

### Entorno local (`.env`)

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

FILESYSTEM_DISK=public

GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### Entorno producción (`.env` en servidor)

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

GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://next1.com.py/auth/google/callback
```

> Las credenciales de pasarelas de pago (Bancard, Pagopar) se configuran desde el panel admin y se guardan en la tabla `payment_settings`, **no en el `.env`**.

---

## Notas arquitectónicas importantes

### Carrito dual (DB + sesión)

El carrito funciona de dos formas según el estado del usuario:

```php
// Usuario autenticado → DB
Cart::where('user_id', auth()->id())->get()

// Invitado → sesión
session('cart', [])
```

El método `getCart()` en `CheckoutForm` y el componente `Cart.php` manejan ambos casos de forma transparente. Al hacer login, el carrito de sesión se migra a DB.

### Subcategorías flyout en sidebar

Las subcategorías usan `position: fixed` con coordenadas calculadas vía `getBoundingClientRect()` en Alpine.js para escapar contenedores con `overflow: hidden`. Esto es necesario porque el sidebar del catálogo tiene overflow oculto por el scroll interno.

### Formularios Livewire

No anidar tags `<form>` en HTML — Livewire usa un único form por componente. Para enviar a un form diferente, usar el atributo `form="id"` en los botones submit.

### Precios con descuento

- `original_price` en ítems de carrito: se guarda al agregar al carrito y se enriquece desde DB para ítems anteriores vía `enrichWithOriginalPrice()`
- `price` en la tabla `products`: precio de venta actual (ya con descuento si aplica)
- `original_price` en la tabla `products`: precio antes del descuento (para mostrar tachado)
