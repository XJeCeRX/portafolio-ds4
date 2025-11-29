# Chinos Café — Sistema POS Web

## 📋 Descripción del Proyecto

Sistema de información web con base de datos MySQL para la gestión completa de un punto de venta (POS) en **Chinos Café**. El sistema incluye módulos para ventas, inventario, proveedores y contactos.

---

## 🏗️ ARQUITECTURA DEL SISTEMA

### 1. Arquitectura SGBD Recomendada

**Recomendación:** Arquitectura **Cliente-Servidor** con **MySQL Server** como SGBD relacional.

#### ¿Por qué esta arquitectura?
- **Escalabilidad:** Permite conexiones concurrentes desde múltiples clientes (web browsers, aplicaciones móviles)
- **Integridad de datos:** Transacciones ACID garantizan consistencia
- **Seguridad:** Control de acceso granular por usuario y rol
- **Desempeño:** Índices y optimizaciones para consultas rápidas
- **Replicación:** Capacidad de replicar datos entre sucursales
- **Estándar de la industria:** MySQL es uno de los SGBD más utilizados

#### ¿Es necesario que el SGBD sea multihilo?
**SÍ.** Por las siguientes razones:
- **Concurrencia:** Múltiples usuarios accederán simultáneamente al sistema (vendedores, cajeros, administradores)
- **Eficiencia:** Cada hilo maneja una conexión/sesión sin bloquear otras
- **Performance:** Un SGBD monohilo sería un cuello de botella en sistemas transaccionales
- **MySQL es multihilo** por defecto: Maneja múltiples conexiones de manera eficiente

---

## 🗄️ BASE DE DATOS

### Diagrama Entidad-Relación

```
┌─────────────────┐      ┌──────────────────┐
│  PROVEEDORES    │      │   INVENTARIO     │
├─────────────────┤      ├──────────────────┤
│ id_proveedor PK │◄───┐ │ id_producto PK   │
│ nombre          │     │ nombre           │
│ contacto        │     │ precio_compra   │
│ telefono        │     │ precio_venta    │
│ email           │     │ stock           │
│ direccion       │     │ categoria       │
└─────────────────┘     │ id_proveedor FK │
                         └──────────────────┘
                                │
                                │
                                ▼
                         ┌──────────────────┐
                         │  DETALLE_VENTA   │
                         ├──────────────────┤
                         │ id_detalle PK    │
                         │ id_venta FK      │
                         │ id_producto FK   │
                         │ cantidad        │
                         │ precio_unitario │
                         │ subtotal        │
                         └──────────────────┘
                                │
                                │
                                ▼
                         ┌──────────────────┐
                         │     VENTAS       │
                         ├──────────────────┤
                         │ id_venta PK      │
                         │ numero_factura   │
                         │ cliente         │
                         │ total           │
                         │ tipo_pago       │
                         │ fecha_venta     │
                         └──────────────────┘

┌──────────────────┐
│   CONTACTOS      │
├──────────────────┤
│ id_contacto PK   │
│ nombre          │
│ correo          │
│ mensaje         │
│ fecha_envio     │
└──────────────────┘
```

### Tablas Creadas

1. **proveedores**: Información de proveedores de suministros
2. **inventario**: Catálogo de productos con precios y stock
3. **ventas**: Registro de transacciones de venta
4. **detalle_venta**: Detalles de cada producto en una venta
5. **contactos**: Mensajes del formulario de contacto

---

## 🌐 SERVIDORES CONFIGURADOS

### 1. MySQL Server (Base de Datos)
- **Host:** localhost
- **Puerto:** 3306 (default)
- **Base de datos:** chinos_cafe
- **Usuario:** root (configurable en `config/database.php`)
- **Motor:** InnoDB (soporta transacciones)

### 2. Servidor Web (Apache/Nginx)
- **Tecnología:** PHP 7.4+
- **Documento raíz:** Carpeta del proyecto
- **Configuración:** Requiere extensión mysqli para PHP

### 3. Servidor de Archivos
- Almacenamiento de documentos (tickets, reportes)
- Backups de la base de datos
- Logs del sistema

---

## 📂 ESTRUCTURA DEL PROYECTO

```
chinos_coffe/
├── index.php                 # Página principal
├── contacto.php              # Handler de formulario de contacto
├── ventas.php                # Módulo de ventas (POS)
├── procesar_venta.php        # Procesamiento de ventas
├── inventario.php            # Gestión de inventario
├── procesar_inventario.php   # Procesamiento de inventario
├── proveedores.php           # Gestión de proveedores
├── procesar_proveedor.php    # Procesamiento de proveedores
├── config/
│   └── database.php          # Configuración y conexión DB
├── sql/
│   └── schema.sql            # Script de creación de BD
└── README.md                 # Documentación
```

---

## 🚀 INSTALACIÓN Y CONFIGURACIÓN

### Requisitos Previos
- PHP 7.4 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Servidor web (Apache/Nginx)
- Extensiones PHP: mysqli, mbstring

### Pasos de Instalación

1. **Instalar XAMPP/WAMP/LAMP**
   ```bash
   # Linux
   sudo apt-get install apache2 php mysql-server
   
   # Windows
   # Descargar e instalar XAMPP desde https://www.apachefriends.org/
   ```

2. **Copiar archivos**
   ```bash
   cp -r chinos_coffe /var/www/html/
   ```

3. **Crear base de datos**
   ```bash
   mysql -u root -p < sql/schema.sql
   ```

4. **Configurar credenciales**
   - Editar `config/database.php` con sus credenciales de MySQL

5. **Iniciar servidores**
   ```bash
   # Apache
   sudo systemctl start apache2
   
   # MySQL
   sudo systemctl start mysql
   ```

6. **Acceder al sistema**
   ```
   http://localhost/chinos_coffe/
   ```

---

## 🔐 SEGURIDAD

### Implementado
- **Prepared Statements:** Previene inyección SQL
- **Sanitización de entradas:** htmlspecialchars() y trim()
- **Validación de formularios:** Campos requeridos
- **Transacciones:** Integridad de datos en operaciones críticas
- **Protección básica del frontend:** Bloqueo de F12, clic derecho

### Recomendaciones Adicionales
- Implementar autenticación de usuarios
- Encriptar contraseñas (bcrypt)
- HTTPS para producción
- CSRF tokens en formularios
- Límites de intentos de login
- Backups automáticos de BD

---

## 💡 FUNCIONALIDADES DEL SISTEMA

### 1. Página Principal (index.php)
- Diseño moderno con Tailwind CSS
- Animaciones AOS
- Formulario de contacto funcional
- Navegación entre módulos

### 2. Módulo de Ventas (ventas.php)
- Interfaz tipo carrito de compras
- Selección de productos disponibles
- Cálculo automático de totales
- Múltiples métodos de pago
- Generación de facturas
- Actualización automática de stock
- Historial de ventas

### 3. Módulo de Inventario (inventario.php)
- Agregar/editar productos
- Control de stock
- Alertas de stock bajo (<10 unidades)
- Asociación con proveedores
- Precio de compra y venta
- Categorización de productos

### 4. Módulo de Proveedores (proveedores.php)
- Registro de proveedores
- Información de contacto
- Contador de productos por proveedor
- Datos de facturación

### 5. Sistema de Contactos
- Formulario en página principal
- Almacenamiento en BD
- Confirmación de envío

---

## 📊 INTERCAMBIO DE DATOS

### Conexión Web ↔ Base de Datos

```
┌─────────────────────────────────────────┐
│         NAVEGADOR (Cliente)             │
└─────────────────┬───────────────────────┘
                  │ HTTP Requests
                  ▼
┌─────────────────────────────────────────┐
│         PHP (Servidor Web)              │
│  ├─ ventas.php                          │
│  ├─ inventario.php                      │
│  ├─ proveedores.php                     │
│  └─ config/database.php                 │
└─────────────────┬───────────────────────┘
                  │ MySQL Protocol
                  ▼
┌─────────────────────────────────────────┐
│         MySQL Server                     │
│  └─ Base de datos: chinos_cafe          │
└─────────────────────────────────────────┘
```

### Flujo de Datos en Ventas
1. Usuario selecciona productos en `ventas.php`
2. Al confirmar venta, se llama `procesar_venta.php`
3. Se abre transacción en MySQL
4. Se inserta registro en `ventas`
5. Se insertan registros en `detalle_venta`
6. Se actualiza `inventario` (stock)
7. Se confirma transacción
8. Redirección con mensaje de éxito

---

## 🎨 TECNOLOGÍAS UTILIZADAS

- **Frontend:**
  - HTML5, CSS3
  - Tailwind CSS (framework CSS)
  - JavaScript (Vanilla)
  - AOS (animaciones)
  - Font Awesome (iconos)
  - Google Fonts (Poppins)

- **Backend:**
  - PHP 7.4+
  - MySQL
  - Arquitectura MVC simplificada

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Error: "Cannot connect to database"
- Verificar que MySQL esté corriendo
- Revisar credenciales en `config/database.php`
- Comprobar que la BD existe

### Error: "Call to undefined function mysqli_connect()"
- Instalar extensión PHP mysqli
- Reiniciar servidor web

### Pagina en blanco
- Habilitar `display_errors` en php.ini
- Revisar logs de Apache/PHP

---

## 📝 NOTAS ADICIONALES

### Ventajas de MySQL como SGBD
- Gratuito y open source
- Gran comunidad y documentación
- Alto rendimiento
- Compatible con múltiples plataformas
- Herramientas de backup integradas

### Optimizaciones Implementadas
- Índices en campos clave (fecha, factura, stock)
- Prepared statements para queries
- Transacciones para operaciones críticas
- Consultas JOIN optimizadas

---

## 📞 SOPORTE

Para preguntas o problemas con el sistema, contactar al equipo de desarrollo.

---

© 2024 Chinos Café — Sistema POS Web


