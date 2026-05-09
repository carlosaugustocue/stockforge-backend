# Daluzed — Documentación de la API REST

**Proyecto Nuclear 3 · Corporación Universitaria Alexander von Humboldt (CUE)**

Esta documentación describe cómo conectar cualquier cliente frontend (web, móvil, etc.)
con el backend de inventario de Daluzed Pastelería.

---

## Información general

| Parámetro         | Valor                          |
|-------------------|--------------------------------|
| URL base          | `http://127.0.0.1:8000/api`    |
| Formato de datos  | JSON                           |
| Autenticación     | Bearer Token (Laravel Sanctum) |
| Expiración token  | 30 minutos                     |
| Encoding          | UTF-8                          |

---

## Cabeceras obligatorias

Todas las peticiones deben incluir:

```
Content-Type: application/json
Accept:       application/json
```

Las rutas protegidas requieren además:

```
Authorization: Bearer {token}
```

---

## Formato de respuestas

El backend siempre responde en JSON con esta estructura:

### Respuesta exitosa

```json
{
  "success": true,
  "message": "Mensaje descriptivo de la operación",
  "data": { }
}
```

### Respuesta de error

```json
{
  "success": false,
  "message": "Descripción del error",
  "errors": { }
}
```

> El campo `errors` solo aparece en errores de validación (HTTP 422).

---

## Códigos HTTP utilizados

| Código | Significado                                      |
|--------|--------------------------------------------------|
| 200    | OK — operación exitosa                           |
| 201    | Created — recurso creado correctamente           |
| 401    | Unauthorized — credenciales inválidas o sin token|
| 403    | Forbidden — sin permisos para esta acción        |
| 422    | Unprocessable — error de validación en los datos |
| 500    | Server Error — error interno del servidor        |

---

## Módulo de Autenticación

Base: `/api/auth`

---

### 1. Login

Autentica al usuario y retorna un token de acceso.

```
POST /api/auth/login
```

**Acceso:** Público — no requiere token.

**Body:**

```json
{
  "email": "admin@inventario.test",
  "password": "Admin1234!"
}
```

| Campo      | Tipo   | Requerido | Validación          |
|------------|--------|-----------|---------------------|
| `email`    | string | ✅        | formato email válido |
| `password` | string | ✅        | mínimo 6 caracteres |

**Respuesta exitosa — HTTP 200:**

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
    "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz123456",
    "rol": "administrador"
  }
}
```

> Guardar el campo `token` para usarlo en todas las peticiones posteriores.
> Guardar el campo `rol` para dirigir al usuario a su vista correspondiente.

**Errores posibles:**

| HTTP | `message`                                        | Causa                                 |
|------|--------------------------------------------------|---------------------------------------|
| 401  | `Credenciales incorrectas.`                      | Email o contraseña inválidos          |
| 401  | `Cuenta bloqueada. Intente en X minuto(s).`      | Superó 5 intentos fallidos            |
| 403  | `Usuario inactivo. Contacte al administrador.`   | Cuenta desactivada por administrador  |
| 422  | Mensajes de validación por campo                 | Campos vacíos o con formato inválido  |

**Ejemplo de error 422:**

```json
{
  "success": false,
  "message": "El correo electrónico no tiene un formato válido.",
  "errors": {
    "email": ["El correo electrónico no tiene un formato válido."]
  }
}
```

---

### 2. Logout

Revoca el token del usuario autenticado y cierra la sesión.

```
POST /api/auth/logout
```

**Acceso:** Requiere token.

**Headers:**
```
Authorization: Bearer {token}
```

**Body:** ninguno.

**Respuesta exitosa — HTTP 200:**

```json
{
  "success": true,
  "message": "Sesión cerrada exitosamente.",
  "data": null
}
```

> Después del logout el token queda inválido permanentemente.
> El frontend debe eliminar el token guardado localmente.

**Errores posibles:**

| HTTP | Causa                              |
|------|------------------------------------|
| 401  | Token inválido, expirado o ausente |

---

### 3. Datos del usuario autenticado

Retorna el perfil del usuario dueño del token.

```
GET /api/auth/me
```

**Acceso:** Requiere token.

**Headers:**
```
Authorization: Bearer {token}
```

**Body:** ninguno.

**Respuesta exitosa — HTTP 200:**

```json
{
  "success": true,
  "message": "Datos del usuario autenticado.",
  "data": {
    "id": 1,
    "nombre": "Administrador",
    "email": "admin@inventario.test",
    "activo": true,
    "rol": "administrador",
    "creado_en": "2026-01-01 00:00:00"
  }
}
```

**Errores posibles:**

| HTTP | Causa                              |
|------|------------------------------------|
| 401  | Token inválido, expirado o ausente |

---

### 4. Crear usuario

Crea un nuevo usuario en el sistema.

```
POST /api/auth/usuarios
```

**Acceso:** Requiere token + rol `administrador`.

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**

```json
{
  "name": "Juan Pérez",
  "email": "juan@daluzed.com",
  "password": "MiPassword123!",
  "password_confirmation": "MiPassword123!",
  "role_id": 2
}
```

| Campo                  | Tipo    | Requerido | Validación                             |
|------------------------|---------|-----------|----------------------------------------|
| `name`                 | string  | ✅        | máximo 255 caracteres                  |
| `email`                | string  | ✅        | formato válido, único en el sistema    |
| `password`             | string  | ✅        | mínimo 8 caracteres                    |
| `password_confirmation`| string  | ✅        | debe coincidir con `password`          |
| `role_id`              | integer | ✅        | debe existir en la tabla `roles`       |

**IDs de roles disponibles:**

| `role_id` | Nombre del rol          |
|-----------|-------------------------|
| 1         | `administrador`         |
| 2         | `gerencia`              |
| 3         | `jefe_produccion`       |
| 4         | `encargado_inventarios` |

**Respuesta exitosa — HTTP 201:**

```json
{
  "success": true,
  "message": "Usuario creado exitosamente.",
  "data": {
    "id": 5,
    "nombre": "Juan Pérez",
    "email": "juan@daluzed.com",
    "activo": true,
    "rol": "gerencia",
    "creado_en": "2026-05-09 10:00:00"
  }
}
```

**Errores posibles:**

| HTTP | `message`                                           | Causa                                  |
|------|-----------------------------------------------------|----------------------------------------|
| 401  | —                                                   | Token inválido o expirado              |
| 403  | `No tienes permisos para realizar esta acción.`     | El usuario autenticado no es admin     |
| 422  | Mensajes de validación por campo                    | Datos inválidos en el body             |

---

## Roles del sistema

El campo `rol` identifica los permisos del usuario en toda la app.

| Rol                     | Descripción                                          | Puede crear usuarios |
|-------------------------|------------------------------------------------------|----------------------|
| `administrador`         | Acceso total al sistema                              | ✅                   |
| `gerencia`              | Visualización de reportes y configuración            | ❌                   |
| `jefe_produccion`       | Operación productiva y gestión de despachos          | ❌                   |
| `encargado_inventarios` | Operación diaria completa del inventario             | ❌                   |

---

## Flujo de autenticación recomendado

```
1. POST /api/auth/login
      └── Guardar token y rol en el cliente (localStorage, cookie, etc.)

2. Todas las demás peticiones
      └── Incluir: Authorization: Bearer {token}

3. Si el backend responde HTTP 401
      └── El token expiró → redirigir al login

4. POST /api/auth/logout
      └── Eliminar token guardado en el cliente
```

---

## Manejo de errores recomendado

```
Antes de cada petición protegida:
  → Verificar que el token exista localmente
  → Si no existe → redirigir a login sin hacer la petición

Al recibir la respuesta:
  → HTTP 200 / 201 → procesar data
  → HTTP 401       → limpiar token local → redirigir a login
  → HTTP 403       → mostrar mensaje "Sin permisos"
  → HTTP 422       → mostrar errores de validación por campo
  → HTTP 500       → mostrar mensaje genérico de error
```

---

## Usuarios de prueba (entorno de desarrollo)

| Email                       | Contraseña    | Rol                   |
|-----------------------------|---------------|-----------------------|
| admin@inventario.test       | Admin1234!    | administrador         |
| gerencia@inventario.test    | Gerencia1234! | gerencia              |
| produccion@inventario.test  | Prod1234!     | jefe_produccion       |
| inventarios@inventario.test | Inv1234!      | encargado_inventarios |

---

## Ejemplo de implementación en JavaScript

```javascript
const API_URL = 'http://127.0.0.1:8000/api';

// ── Login ────────────────────────────────────────────────────────────────────
async function login(email, password) {
  const res = await fetch(`${API_URL}/auth/login`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ email, password }),
  });

  const data = await res.json();

  if (!res.ok) {
    throw new Error(data.message);
  }

  // Guardar token
  localStorage.setItem('token', data.data.token);
  localStorage.setItem('rol',   data.data.rol);

  return data.data;
}

// ── Petición autenticada genérica ────────────────────────────────────────────
async function fetchApi(endpoint, options = {}) {
  const token = localStorage.getItem('token');

  const res = await fetch(`${API_URL}${endpoint}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${token}`,
      ...options.headers,
    },
  });

  if (res.status === 401) {
    localStorage.removeItem('token');
    window.location.href = '/login'; // redirigir al login
    return;
  }

  return await res.json();
}

// ── Logout ───────────────────────────────────────────────────────────────────
async function logout() {
  await fetchApi('/auth/logout', { method: 'POST' });
  localStorage.removeItem('token');
  localStorage.removeItem('rol');
  window.location.href = '/login';
}

// ── Obtener usuario actual ───────────────────────────────────────────────────
async function getUsuarioActual() {
  return await fetchApi('/auth/me');
}

// ── Crear usuario (solo administrador) ──────────────────────────────────────
async function crearUsuario(datos) {
  return await fetchApi('/auth/usuarios', {
    method: 'POST',
    body: JSON.stringify(datos),
  });
}
```

---

## CORS

El backend acepta peticiones desde:

```
http://localhost:3000
```

Si el frontend corre en otro origen, solicitar al equipo de backend que agregue
el dominio correspondiente en `config/cors.php`.
