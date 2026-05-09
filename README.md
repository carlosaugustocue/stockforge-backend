# Inventario Logística — Backend API

**Proyecto Nuclear 3 — Corporación Universitaria Alexander von Humboldt (CUE)**

Sistema de gestión de inventario y logística para centros de distribución.
Backend construido con Laravel + Sanctum siguiendo principios SOLID y arquitectura de Monolito Modular.

---

## Requisitos

| Herramienta | Versión mínima |
|-------------|----------------|
| PHP         | 8.3            |
| Composer    | 2.x            |
| MySQL       | 8.0            |

---

## Instalación paso a paso

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd inventario-logistica-backend

# 2. Instalar dependencias PHP
composer install

# 3. Copiar y configurar el archivo de entorno
cp .env.example .env
# Editar .env con tus credenciales de MySQL

# 4. Generar clave de aplicación
php artisan key:generate

# 5. Crear la base de datos en MySQL
mysql -u root -p -e "CREATE DATABASE inventario_logistica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Ejecutar migraciones (crea las tablas)
php artisan migrate

# 7. Ejecutar seeders (crea roles y usuarios de prueba)
php artisan db:seed

# 8. Iniciar el servidor de desarrollo
php artisan serve
# La API estará disponible en: http://localhost:8000/api
```

---

## Endpoints disponibles

### Rutas públicas (sin token)

| Método | Ruta            | Body                    | Descripción    |
|--------|-----------------|-------------------------|----------------|
| POST   | /api/auth/login | `email`, `password`     | Iniciar sesión |

**Ejemplo request:**
```json
POST /api/auth/login
{
  "email": "admin@inventario.test",
  "password": "Admin1234!"
}
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Inicio de sesión exitoso.",
  "data": {
    "usuario": {
      "id": 1,
      "nombre": "Administrador",
      "email": "admin@inventario.test",
      "activo": true,
      "rol": "administrador",
      "creado_en": "2026-01-01 00:00:00"
    },
    "token": "1|abc123...",
    "rol": "administrador"
  }
}
```

### Rutas protegidas (`Authorization: Bearer {token}`)

| Método | Ruta               | Roles permitidos | Descripción                  |
|--------|--------------------|------------------|------------------------------|
| POST   | /api/auth/logout   | Todos            | Cerrar sesión                |
| GET    | /api/auth/me       | Todos            | Ver usuario actual           |
| POST   | /api/auth/usuarios | administrador    | Crear nuevo usuario          |

---

## Tabla de roles y permisos

| Rol                   | Login | Ver /me | Crear usuarios |
|-----------------------|-------|---------|----------------|
| administrador         | ✅    | ✅      | ✅             |
| gerencia              | ✅    | ✅      | ❌             |
| jefe_produccion       | ✅    | ✅      | ❌             |
| encargado_inventarios | ✅    | ✅      | ❌             |

---

## Usuarios de prueba (seeders)

| Email                       | Contraseña    | Rol                   |
|-----------------------------|---------------|-----------------------|
| admin@inventario.test       | Admin1234!    | administrador         |
| gerencia@inventario.test    | Gerencia1234! | gerencia              |
| produccion@inventario.test  | Prod1234!     | jefe_produccion       |
| inventarios@inventario.test | Inv1234!      | encargado_inventarios |

---

## Ejecutar los tests

```bash
# Tests del módulo Auth
php artisan test --filter=AuthTest

# Todos los tests con detalle
php artisan test --verbose
```

---

## Arquitectura

```
app/
├── Modules/
│   └── Auth/
│       ├── Controllers/    ← Recibe peticiones HTTP
│       ├── Requests/       ← Valida datos de entrada
│       ├── Services/       ← Lógica de negocio (SOLID-SRP)
│       ├── Repositories/   ← Acceso a datos (SOLID-DIP)
│       └── Resources/      ← DTO / serialización JSON
├── Models/
└── Shared/
    ├── Traits/             ← ApiResponseTrait
    └── Middleware/         ← CheckRole (RBAC)
```

## Principios SOLID aplicados

| Principio | Aplicación |
|-----------|-----------|
| SRP | Service (negocio), Repository (datos), Controller (HTTP) tienen una sola responsabilidad |
| DIP | AuthService depende de UserRepositoryInterface, no de la implementación concreta |
| OCP | Nuevos módulos se agregan en `app/Modules/` sin modificar los existentes |
