# 📡 Laravel GraphQL API

> Una aplicación backend robusta construida con Laravel 11 y GraphQL, diseñada para manejar autenticación, permisos y procesamiento de imágenes de manera eficiente.

## 🚀 Características Principales

- **GraphQL API** con Lighthouse GraphQL
- **Autenticación segura** con Laravel Sanctum
- **Sistema de permisos** granular con Spatie Laravel Permission  
- **Procesamiento de imágenes** avanzado con Intervention Image
- **Arquitectura escalable** con helpers personalizados
- **Interfaz GraphiQL** integrada para desarrollo

## 🛠️ Stack Tecnológico

### Backend
- **Laravel 11** - Framework PHP moderno
- **PHP 8.2+** - Lenguaje de programación
- **GraphQL** - API query language
- **Lighthouse** - GraphQL server para Laravel

### Principales Dependencias
- `nuwave/lighthouse` - Servidor GraphQL para Laravel
- `laravel/sanctum` - Sistema de autenticación API
- `spatie/laravel-permission` - Gestión de roles y permisos
- `intervention/image` - Manipulación avanzada de imágenes
- `mll-lab/laravel-graphiql` - Interfaz web para GraphQL

### Herramientas de Desarrollo
- **Laravel Sail** - Entorno de desarrollo con Docker
- **Laravel Pint** - Code formatter para PHP
- **PHPUnit** - Testing framework
- **Vite** - Build tool para assets frontend

## ⚙️ Requisitos del Sistema

- **PHP**: ^8.2
- **Composer**: Última versión
- **Node.js**: 18+ (para asset compilation)
- **Base de datos**: PostgreSQL

## 📦 Instalación

### 1. Clonar el repositorio
```bash
git clone <tu-repositorio-url>
cd <nombre-del-proyecto>
```

### 2. Instalar dependencias PHP
```bash
composer install
```

### 3. Instalar dependencias Node.js
```bash
npm install
```

### 4. Configurar el entorno
```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar key de aplicación
php artisan key:generate

# Configurar tu base de datos en .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=tu_base_datos
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_password
```

### 5. Ejecutar migraciones
```bash
# Crear base de datos y ejecutar migraciones
php artisan migrate

# Opcional: Ejecutar seeders
php artisan db:seed
```

### 6. Configurar permisos y storage
```bash
# Crear link simbólico para storage
php artisan storage:link
```

## 🚀 Uso

### Desarrollo local
```bash
# Servidor de desarrollo
php artisan serve

# Compilar assets (en otra terminal)
npm run dev
```

### Con Laravel Sail (Docker)
```bash
# Levantar contenedores
./vendor/bin/sail up -d

# Ejecutar comandos dentro del contenedor
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

### Acceso a GraphiQL
Una vez que el servidor esté corriendo, puedes acceder a la interfaz GraphiQL en:
```
http://localhost:8000/graphiql
```

## 📋 Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Generar documentación GraphQL schema
php artisan lighthouse:print-schema

# Ejecutar tests
php artisan test

# Code formatting
./vendor/bin/pint
```

## 🔐 Autenticación

El proyecto utiliza **Laravel Sanctum** para autenticación API. Los endpoints GraphQL requieren tokens de acceso válidos.

### Ejemplo de autenticación:
```graphql
# Headers
Authorization: Bearer tu-token-aqui
```

## 🎯 Estructura del Proyecto

```
├── app/
│   ├── GraphQL/           # Resolvers y tipos GraphQL
│   ├── helpers/           # Helper functions personalizados
│   │   ├── ImageHelper.php
│   │   └── StatusHelper.php
│   └── Models/            # Modelos Eloquent
├── database/
│   ├── migrations/        # Migraciones de base de datos
│   └── seeders/           # Seeders
├── graphql/               # Schema GraphQL
└── resources/
    └── js/                # Assets frontend
```

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests con coverage
php artisan test --coverage

# Ejecutar tests específicos
php artisan test --filter NombreDelTest
```

## 🔧 Configuración Adicional

### Variables de Entorno Importantes
```env
APP_NAME="Tu App Name"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Configuración GraphQL
LIGHTHOUSE_CACHE_ENABLE=false

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000
```

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🆘 Soporte

Si encuentras algún problema o tienes preguntas:

1. Revisa la [documentación de Laravel](https://laravel.com/docs)
2. Consulta la [documentación de Lighthouse GraphQL](https://lighthouse-php.com/)
3. Abre un issue en este repositorio

---

**Desarrollado con ❤️ usando Laravel y GraphQL**
