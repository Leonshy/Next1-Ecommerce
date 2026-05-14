# Manual del Administrador

**Plataforma:** Next1 E-Commerce  
**URL del panel:** [next1.com.py/admin](https://next1.com.py/admin)  
**Desarrollado por:** [Webparaguay](https://webparaguay.com)

---

## Índice

1. [Acceso al panel](#1-acceso-al-panel)
2. [Roles y permisos](#2-roles-y-permisos)
3. [Dashboard](#3-dashboard)
4. [Productos](#4-productos)
5. [Categorías](#5-categorías)
6. [Marcas](#6-marcas)
7. [Etiquetas](#7-etiquetas)
8. [Pedidos](#8-pedidos)
9. [Usuarios](#9-usuarios)
10. [Multimedia](#10-multimedia)
11. [Marketing](#11-marketing)
12. [Configuración de envíos](#12-configuración-de-envíos)
13. [Configuración de pagos](#13-configuración-de-pagos)
14. [Configuración SEO](#14-configuración-seo)
15. [Analytics](#15-analytics)
16. [Email / SMTP](#16-email--smtp)
17. [hCaptcha](#17-hcaptcha)
18. [Contenido de páginas](#18-contenido-de-páginas)
19. [Info de la tienda](#19-info-de-la-tienda)
20. [Auditoría](#20-auditoría)
21. [Modo mantenimiento](#21-modo-mantenimiento)

---

## 1. Acceso al panel

### Iniciar sesión

1. Ir a `https://next1.com.py/admin`
2. Ingresar email y contraseña
3. Si tenés **2FA activado**, se enviará un código de 6 dígitos a tu email — ingresarlo en la siguiente pantalla

> La sesión del panel admin expira por inactividad. Si ves la pantalla de login sin haber cerrado sesión, es normal — volvé a ingresar tus credenciales.

### Cerrar sesión

Hacer clic en tu nombre de usuario en la esquina superior derecha → **Cerrar sesión**.

---

## 2. Roles y permisos

El sistema tiene dos roles para el panel admin:

| Rol | Acceso |
|-----|--------|
| **Admin** | Acceso total a todas las secciones |
| **Vendedor** | Productos, Categorías, Marcas, Etiquetas, Pedidos, Marketing — **sin** Usuarios, Multimedia, Contenido ni Configuración |

Los roles se asignan desde **Admin → Usuarios** (solo disponible para administradores).

---

## 3. Dashboard

La pantalla de inicio del panel muestra un resumen del estado de la tienda:

| Métrica | Descripción |
|---------|-------------|
| Ventas del día / mes | Monto total de pedidos confirmados |
| Pedidos pendientes | Pedidos que requieren atención |
| Usuarios registrados | Total de clientes registrados |
| Productos con stock bajo | Productos con stock ≤ umbral configurado |

También se muestran los últimos pedidos recibidos con acceso rápido a su detalle.

---

## 4. Productos

### Listado de productos

En **Admin → Productos** se muestra la tabla con todos los productos. Desde aquí podés:

- **Buscar** por nombre o SKU
- **Filtrar** por categoría, marca o estado
- **Editar** cualquier producto
- **Activar/desactivar** con el toggle de estado
- **Eliminar** (se pide confirmación)

### Crear / editar un producto

**Información básica:**

| Campo | Descripción |
|-------|-------------|
| Nombre | Nombre del producto (genera el slug automáticamente) |
| SKU | Código interno del producto |
| Categoría | Categoría principal (puede ser subcategoría) |
| Marca | Marca del producto |
| Etiquetas | Tags para filtrado y SEO |
| Estado | Activo / Inactivo |
| Destacado | Aparece en sección "Destacados" del home |
| Nuevo | Aparece con badge "NUEVO" |

**Precios y stock:**

| Campo | Descripción |
|-------|-------------|
| Precio | Precio de venta actual |
| Precio original | Precio antes del descuento (aparece tachado si es mayor al precio) |
| Stock | Cantidad disponible |
| Stock mínimo | Umbral para alerta de stock bajo en el dashboard |

**Descripciones:**

| Campo | Descripción |
|-------|-------------|
| Descripción corta | Resumen breve que aparece en la ficha del producto (texto plano) |
| Descripción larga | Descripción completa con editor WYSIWYG (acepta formato HTML) |

**Imágenes:**

- Cargar múltiples imágenes desde la biblioteca de medios o subir directamente
- La primera imagen es la imagen principal
- Reordenar arrastrando las miniaturas
- Eliminar imágenes individuales con el botón ×

### Importar productos desde CSV/Excel

1. Ir a **Admin → Productos → Importar**
2. Descargar la plantilla de ejemplo
3. Completar los datos en la plantilla
4. Subir el archivo

**Columnas de la plantilla:**

| Columna | Requerido | Descripción |
|---------|-----------|-------------|
| `nombre` | Sí | Nombre del producto |
| `sku` | No | Código interno |
| `precio` | Sí | Precio de venta (sin símbolo de moneda) |
| `precio_original` | No | Precio antes del descuento |
| `stock` | Sí | Cantidad disponible |
| `categoria` | Sí | Nombre exacto de la categoría |
| `marca` | No | Nombre exacto de la marca |
| `descripcion_corta` | No | Texto corto del producto |
| `descripcion_larga` | No | Descripción completa (acepta HTML) |
| `estado` | No | `activo` o `inactivo` (default: activo) |

> Los productos importados no incluyen imágenes — subir las imágenes manualmente después de la importación.

### Exportar productos

1. Ir a **Admin → Productos → Exportar**
2. Seleccionar formato: CSV o Excel
3. Se descarga el archivo con todos los productos activos

### Acciones masivas

Seleccionar varios productos con los checkboxes y elegir una acción:
- **Activar seleccionados**
- **Desactivar seleccionados**
- **Eliminar seleccionados**

---

## 5. Categorías

En **Admin → Categorías** se gestionan las categorías y subcategorías.

### Crear categoría

| Campo | Descripción |
|-------|-------------|
| Nombre | Nombre de la categoría |
| Categoría padre | Dejar vacío para categoría principal; seleccionar una para crear subcategoría |
| Imagen | Imagen representativa (aparece en el sidebar del catálogo) |
| Estado | Activo / Inactivo |

> Las subcategorías aparecen en el flyout del sidebar al hacer hover sobre la categoría padre.

### Ordenar categorías

Las categorías se muestran en el orden en que están en la tabla. Para reordenar, arrastrar las filas (si está habilitado) o editar el campo de orden.

---

## 6. Marcas

En **Admin → Marcas** se gestionan las marcas de los productos.

| Campo | Descripción |
|-------|-------------|
| Nombre | Nombre de la marca |
| Logo | Imagen del logo |
| Estado | Activo / Inactivo |

Las marcas activas aparecen como filtro en el catálogo de productos.

---

## 7. Etiquetas

En **Admin → Etiquetas** se crean tags para organizar y filtrar productos.

| Campo | Descripción |
|-------|-------------|
| Nombre | Nombre del tag |
| Slug | Generado automáticamente |

Las etiquetas se asignan a productos desde el formulario de edición del producto.

---

## 8. Pedidos

### Listado de pedidos

En **Admin → Pedidos** se muestran todos los pedidos ordenados por fecha (más recientes primero). Filtros disponibles:

- Estado del pedido
- Método de pago
- Rango de fechas

### Estados del pedido

| Estado | Descripción |
|--------|-------------|
| `pendiente` | Pedido recibido, esperando pago |
| `pendiente_transferencia` | El cliente indicó que pagó por transferencia y subió comprobante |
| `confirmado` | Pago verificado |
| `procesando` | En preparación |
| `enviado` | Despachado |
| `entregado` | Entregado al cliente |
| `cancelado` | Cancelado |

### Detalle de un pedido

Al hacer clic en un pedido se ve:

- **Datos del cliente:** nombre, email, teléfono
- **Dirección de entrega** o datos de retiro en tienda
- **Ítems del pedido:** productos, cantidades, precios
- **Resumen de costos:** subtotal, descuentos, envío, total
- **Método de pago:** con estado de la transacción
- **Comprobante de transferencia** (si aplica): se puede ver y descargar
- **Historial de estados:** con fecha y usuario que realizó cada cambio

### Cambiar estado de un pedido

1. Abrir el detalle del pedido
2. Seleccionar el nuevo estado en el selector
3. Hacer clic en **Guardar** — el sistema registra el cambio en el historial

### Descargar comprobante

Si el cliente pagó por transferencia, el comprobante aparece en el detalle del pedido. Hacer clic en **Descargar comprobante**.

---

## 9. Usuarios

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Usuarios** se gestionan todos los usuarios registrados.

### Listado

- Ver todos los usuarios con su rol, fecha de registro y último acceso
- Buscar por nombre o email
- Filtrar por rol

### Editar usuario

| Campo | Descripción |
|-------|-------------|
| Nombre | Nombre completo |
| Email | Dirección de correo |
| Rol | `admin`, `vendedor` o `cliente` |
| Contraseña | Dejar vacío para no cambiarla |
| Estado | Activo / Bloqueado |

> **Importante:** No eliminar usuarios que tengan pedidos asociados — los pedidos perderían la referencia al cliente. En su lugar, desactivar la cuenta.

---

## 10. Multimedia

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Multimedia** se gestiona la biblioteca centralizada de imágenes.

### Subir archivos

1. Hacer clic en **Subir archivos** o arrastrar imágenes al área de carga
2. Los archivos se almacenan en `storage/app/public/media/`
3. Se generan URL públicas accesibles desde `/storage/media/`

### Usar la biblioteca en otros formularios

En cualquier formulario con campo de imagen (productos, banners, slides, etc.) al hacer clic en el campo de imagen se abre el **Media Picker** — un modal con la biblioteca completa para seleccionar una imagen existente o subir una nueva.

### Eliminar archivos

Al eliminar un archivo de la biblioteca se verifica si está en uso. Si está referenciado por algún producto, banner u otro elemento, se advierte antes de eliminar.

---

## 11. Marketing

### Campañas de descuento

En **Admin → Marketing → Campañas** se crean campañas de descuento que se muestran en el home.

| Campo | Descripción |
|-------|-------------|
| Nombre | Nombre interno de la campaña |
| Título público | Título que se muestra en el home |
| Color del título | Color del badge del título (picker de color) |
| Tipo de descuento | Porcentaje o monto fijo |
| Valor | Porcentaje (ej: 20) o monto fijo |
| Categoría | Aplicar descuento solo a productos de esta categoría |
| Marca | Aplicar descuento solo a productos de esta marca |
| Banner | Imagen de fondo de la campaña en el home |
| Fecha inicio / fin | Período de vigencia |
| Estado | Activo / Inactivo |

> Si se especifica tanto categoría como marca, se aplica solo a productos que pertenezcan a **ambas**.

### Banners promocionales

En **Admin → Marketing → Banners** se gestionan los banners que aparecen en distintas secciones de la tienda.

| Campo | Descripción |
|-------|-------------|
| Título | Texto principal del banner |
| Subtítulo | Texto secundario |
| Imagen | Imagen de fondo |
| Overlay | Color y opacidad del overlay sobre la imagen |
| URL de enlace | A dónde lleva al hacer clic |
| Posición | Ubicación en la tienda |
| Estado | Activo / Inactivo |

### Hero Slides

En **Admin → Marketing → Hero Slides** se configura el slider principal del home.

| Campo | Descripción |
|-------|-------------|
| Título | Texto principal del slide |
| Subtítulo | Texto secundario |
| Imagen | Imagen de fondo del slide |
| Overlay | Oscurecimiento sobre la imagen (0–100%) |
| Texto del botón | Texto del CTA |
| URL del botón | Destino del CTA |
| Orden | Posición en el slider |
| Estado | Activo / Inactivo |

### Newsletter

En **Admin → Marketing → Newsletter** se visualiza la lista de suscriptores verificados.

- Ver email, fecha de suscripción y estado de verificación
- **Exportar CSV** con todos los suscriptores verificados para usar en herramientas de email marketing externas (Mailchimp, etc.)

### Gift Cards

En **Admin → Gift Cards** se crean y gestionan las tarjetas de regalo.

| Campo | Descripción |
|-------|-------------|
| Código | Código único que el cliente ingresa en el checkout |
| Saldo inicial | Monto inicial de la gift card |
| Saldo actual | Saldo restante (se actualiza automáticamente al usarse) |
| Estado | Activa / Usada / Desactivada |

**Flujo de uso:**
1. El administrador crea una gift card con código y saldo
2. Entrega el código al cliente (por email, físicamente, etc.)
3. El cliente ingresa el código en el paso de pago del checkout
4. El saldo se descuenta del total del pedido
5. Si el pedido supera el saldo, el cliente paga la diferencia con otro método

---

## 12. Configuración de envíos

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Configuración → Envíos** se configura todo lo relacionado con el cálculo de costos de envío.

### Envío gratis

| Campo | Descripción |
|-------|-------------|
| Monto mínimo para envío gratis | Ej: 500000 — pedidos por encima de este monto no pagan envío |

Dejar en 0 para desactivar el envío gratis.

### Retiro en tienda

| Campo | Descripción |
|-------|-------------|
| Habilitado | Activar/desactivar la opción en el checkout |
| Dirección de la tienda | Dirección que se muestra al cliente al elegir retiro |

### Departamentos y tarifas

Para cada departamento del país:

| Campo | Descripción |
|-------|-------------|
| Habilitado | Si está deshabilitado, ese departamento no aparece en el checkout |
| Tarifa base | Costo de envío para todas las ciudades del departamento |
| Ciudades con tarifa custom | Ciudades con precio diferente a la tarifa base |
| Ciudades deshabilitadas | Ciudades que no reciben envíos (no aparecen en el checkout) |

**Para agregar tarifa custom de ciudad:**
1. En el departamento correspondiente, hacer clic en **Agregar ciudad**
2. Seleccionar la ciudad y escribir la tarifa
3. Guardar

---

## 13. Configuración de pagos

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Configuración → Pagos** se configuran las pasarelas de pago y los descuentos por método.

### Bancard VPOS

| Campo | Descripción |
|-------|-------------|
| Habilitado | Mostrar/ocultar este método en el checkout |
| Entorno | Sandbox (pruebas) o Producción |
| Public Key | Clave pública proporcionada por Bancard |
| Private Key | Clave privada proporcionada por Bancard |
| Descuento % | Porcentaje de descuento para pedidos pagados con este método |
| Etiqueta del descuento | Texto que aparece en el checkout (ej: "Pagá con tarjeta y ahorrá 10%") |

### Pagopar

| Campo | Descripción |
|-------|-------------|
| Habilitado | Mostrar/ocultar este método en el checkout |
| Entorno | Sandbox (pruebas) o Producción |
| Public Key | Token público de Pagopar |
| Private Key | Token privado de Pagopar |
| Descuento % | Porcentaje de descuento para pagos con Pagopar |
| Etiqueta del descuento | Texto descriptivo del descuento |

### Transferencia bancaria

| Campo | Descripción |
|-------|-------------|
| Habilitado | Siempre disponible como fallback (recomendado dejar activo) |
| Banco | Nombre del banco |
| Titular de la cuenta | Nombre del titular |
| Número de cuenta | Número de cuenta bancaria |
| RUC / CI | Identificación fiscal |
| Alias / CBU | Dato adicional si aplica |

### Datos bancarios en el checkout

Los datos de la cuenta bancaria se muestran al cliente cuando selecciona "Transferencia bancaria" en el paso de pago. El cliente sube el comprobante antes de confirmar el pedido.

---

## 14. Configuración SEO

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Configuración → SEO** se configuran los meta tags para cada página principal.

| Campo | Descripción |
|-------|-------------|
| Página | Home, Catálogo, Detalle de producto, etc. |
| Meta título | Título que aparece en la pestaña del navegador y en Google |
| Meta descripción | Descripción en resultados de búsqueda (max. 160 caracteres) |
| Open Graph imagen | Imagen para compartir en redes sociales |

> Para el detalle de producto, el título y descripción se generan automáticamente desde el nombre y descripción del producto. Los campos SEO aquí son para páginas generales.

---

## 15. Analytics

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Analytics** se configuran las herramientas de seguimiento.

| Herramienta | Campo | Descripción |
|-------------|-------|-------------|
| Google Tag Manager | GTM ID | Ej: `GTM-XXXXXXX` |
| Google Analytics 4 | Measurement ID | Ej: `G-XXXXXXXXXX` |
| Meta Pixel | Pixel ID | ID numérico del pixel de Facebook |

Los scripts se insertan automáticamente en el `<head>` de todas las páginas públicas una vez configurados.

> Si usás Google Tag Manager, es preferible configurar GA4 y Meta Pixel desde GTM en vez de desde aquí, para evitar scripts duplicados.

---

## 16. Email / SMTP

> Esta sección solo está disponible para el rol **Admin**.

### Configuración SMTP

En **Admin → Email → Configuración SMTP** se ingresan los datos del servidor de correo:

| Campo | Ejemplo |
|-------|---------|
| Host | `smtp.gmail.com` |
| Puerto | `587` |
| Encriptación | `tls` |
| Usuario | `tienda@next1.com.py` |
| Contraseña | Contraseña de la cuenta de correo |
| Nombre del remitente | `Next1 E-Commerce` |
| Email del remitente | `no-reply@next1.com.py` |

Después de guardar, usar el botón **Enviar email de prueba** para verificar la configuración.

### Plantillas de email

En **Admin → Email → Plantillas** se editan los templates de los emails transaccionales.

**Plantillas disponibles:**

| Plantilla | Cuándo se envía |
|-----------|----------------|
| Verificación de email | Al registrarse un nuevo usuario |
| Confirmación de pedido | Al confirmar un pedido |
| Cambio de estado | Cuando cambia el estado de un pedido |
| Newsletter | Al suscribirse al newsletter |
| Recuperación de contraseña | Al solicitar reseteo de contraseña |

**Variables disponibles en las plantillas:**

Las variables se escriben entre llaves dobles: `{{nombre_variable}}`

| Variable | Descripción |
|----------|-------------|
| `{{nombre}}` | Nombre del cliente |
| `{{email}}` | Email del cliente |
| `{{numero_pedido}}` | Número del pedido |
| `{{total}}` | Total del pedido |
| `{{estado}}` | Estado actual del pedido |
| `{{link}}` | URL de acción (verificar email, ver pedido, etc.) |

El editor muestra una previsualización en tiempo real del email renderizado.

---

## 17. hCaptcha

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → hCaptcha** se configura la protección anti-bots.

| Campo | Descripción |
|-------|-------------|
| Site Key | Clave pública de hCaptcha (se pone en el formulario) |
| Secret Key | Clave privada para verificar en el servidor |
| Formularios protegidos | Seleccionar cuáles formularios requieren pasar el captcha |

Si no se configura, el captcha se desactiva automáticamente en todos los formularios.

---

## 18. Contenido de páginas

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Contenido** se edita el contenido de las páginas estáticas del sitio.

| Página | Descripción |
|--------|-------------|
| Quiénes somos | Texto de la página "Nosotros" |
| FAQ | Preguntas frecuentes |
| Términos y condiciones | Texto legal |
| Política de privacidad | Texto legal |

Cada página tiene un editor WYSIWYG que permite formatear el texto con negrita, listas, títulos, enlaces, etc.

---

## 19. Info de la tienda

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Contenido → Info Tienda** se configuran los datos generales de la tienda que aparecen en el header, footer y emails.

| Campo | Descripción |
|-------|-------------|
| Nombre de la tienda | Aparece en el title del sitio y emails |
| Logo | Logo principal (SVG o PNG con fondo transparente recomendado) |
| Favicon | Ícono de la pestaña del navegador |
| Teléfono | WhatsApp / teléfono de contacto |
| Email de contacto | Email que aparece en el footer |
| Dirección | Dirección física de la tienda |
| Facebook | URL del perfil de Facebook |
| Instagram | URL del perfil de Instagram |
| WhatsApp | Número con código de país (ej: 595981234567) |

---

## 20. Auditoría

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Auditoría** se registran todas las acciones realizadas en el panel admin.

Cada registro incluye:
- **Usuario** que realizó la acción
- **Acción** (crear, editar, eliminar, etc.)
- **Sección** afectada (productos, pedidos, usuarios, etc.)
- **ID del registro** afectado
- **Fecha y hora**
- **IP** desde donde se realizó la acción
- **User agent** (navegador)

Los registros son de solo lectura — no se pueden editar ni eliminar desde el panel.

---

## 21. Modo mantenimiento

> Esta sección solo está disponible para el rol **Admin**.

En **Admin → Mantenimiento** se puede activar el modo mantenimiento de la tienda.

| Campo | Descripción |
|-------|-------------|
| Activar mantenimiento | Muestra la página de mantenimiento a todos los visitantes |
| Mensaje personalizado | Texto que se muestra al cliente (ej: "Estamos realizando mejoras...") |
| Imagen de fondo | Imagen opcional para la página de mantenimiento |

> **Importante:** Cuando el modo mantenimiento está activo, el panel admin sigue siendo accesible para usuarios con rol `admin` o `vendedor`. Los clientes ven la página de mantenimiento en lugar de la tienda.

---

## Guía rápida de tareas frecuentes

### Agregar un producto nuevo

1. **Admin → Productos → Nuevo producto**
2. Completar nombre, categoría, marca, precio y stock
3. Subir imágenes desde la biblioteca
4. Completar descripciones
5. Activar el producto → **Guardar**

### Procesar un pedido con transferencia

1. **Admin → Pedidos** → abrir el pedido
2. Verificar el comprobante de pago adjunto
3. Cambiar estado a `confirmado` → **Guardar**
4. Al preparar el paquete → cambiar a `procesando`
5. Al despachar → cambiar a `enviado`
6. Al entregar → cambiar a `entregado`

### Crear una campaña de descuento

1. **Admin → Marketing → Campañas → Nueva campaña**
2. Ingresar nombre, título público, color, tipo y valor de descuento
3. Seleccionar la categoría o marca a la que aplica
4. Subir imagen de banner
5. Configurar fechas de vigencia
6. Activar → **Guardar**

### Actualizar el slider del home

1. **Admin → Marketing → Hero Slides**
2. Crear nuevo slide o editar uno existente
3. Subir imagen, escribir título y subtítulo
4. Configurar el botón (texto + URL)
5. Ajustar el orden si hay múltiples slides
6. Activar → **Guardar**

### Subir nuevas imágenes a la biblioteca

1. **Admin → Multimedia → Subir**
2. Seleccionar imágenes desde el equipo (múltiples a la vez)
3. Las imágenes quedan disponibles inmediatamente en el Media Picker

### Exportar suscriptores del newsletter

1. **Admin → Marketing → Newsletter**
2. Hacer clic en **Exportar CSV**
3. Se descarga el archivo con todos los emails verificados

---

## Contacto y soporte técnico

Para consultas técnicas o reportar problemas:

**Webparaguay**  
[webparaguay.com](https://webparaguay.com)
