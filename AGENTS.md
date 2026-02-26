# Repository Guidelines

## Estructura del Proyecto
Este repositorio es una app Laravel 12 con entorno local en DDEV y panel de administración con Filament.

- `app/`: lógica de la aplicación (modelos, providers, recursos/páginas/widgets de Filament)
- `app/Providers/Filament/`: configuración del panel `admin`
- `routes/`: rutas web/console
- `resources/`: vistas Blade, CSS y JS fuente
- `public/`: docroot y assets públicos (incluye assets publicados de Filament)
- `database/`: migraciones, seeders y factories
- `config/`: configuración de Laravel, Filament Shield y permisos
- `.ddev/`: configuración del entorno local

## Librerías Usadas (clave)
- `filament/filament` (`^5.0`): panel de administración (ruta base `/admin`)
- `bezhansalleh/filament-shield` (`^4.1`): roles y permisos para Filament (sobre `spatie/laravel-permission`)

## Comandos de Desarrollo
Usar DDEV por defecto (no depender del PHP local).

- `ddev start`: arranca contenedores
- `ddev artisan migrate`: ejecuta migraciones
- `ddev exec composer dev`: servidor Laravel + cola + logs + Vite
- `ddev npm run dev`: Vite en modo desarrollo
- `ddev npm run build`: build de frontend
- `ddev artisan make:filament-user`: crea usuario de acceso al panel
- `ddev artisan shield:generate --all --panel=admin`: regenera permisos de Shield
- `ddev artisan shield:super-admin --panel=admin`: asigna rol super admin

## Estilo y Convenciones
- Seguir `.editorconfig`: UTF-8, LF, indentación de 4 espacios (YAML a 2).
- PHP con convenciones Laravel; formatear con `laravel/pint`.
- Comando recomendado: `ddev exec ./vendor/bin/pint`
- Clases en `StudlyCase` (`AdminPanelProvider`, `TermResource`)
- Métodos y propiedades en `camelCase`
- Configs, columnas y migraciones en `snake_case`

## Validación Manual
En este proyecto la validación se hace manualmente (no se usa flujo de tests automatizados).

- Revisar panel en `https://glossary.ddev.site/admin`
- Verificar login, acceso a dashboard y permisos/roles en Shield
- Si hay cambios de permisos, regenerar con Shield y probar con un usuario no admin

## Seguridad y Configuración
- No subir `.env` ni credenciales.
- Revisar `config/filament-shield.php` y `config/permission.php` antes de producción.
- Confirmar que `App\Models\User` mantiene `HasRoles` y acceso a Filament (`FilamentUser`).
