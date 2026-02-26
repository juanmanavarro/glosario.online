# Features

## Gestión de usuarios en panel admin
Descripción: Permite al administrador crear, consultar, editar y eliminar usuarios del sistema desde el panel de administración, así como asignarles roles para controlar qué secciones y acciones pueden usar dentro de la aplicación.

Roles disponibles y alcance funcional:
- `super_admin`: acceso total al panel de administración y a todos los permisos del sistema.
- `Admin`: acceso a la gestión de usuarios y a todas las secciones/acciones del diccionario (términos, categorías, versiones y logs editoriales).
- `Editor`: acceso operativo al diccionario (términos, categorías y versiones) y lectura de logs editoriales, sin gestión de usuarios.

## Personalización del listado de términos en admin
Descripción: El listado de términos del panel de administración (`/admin/terms`) muestra las columnas `Título`, `Estado`, `Publicado el` y `Nº de acepciones`, eliminando el sufijo `ES` en el título para simplificar la visualización editorial.

## Creación y edición de términos
Descripción: Permite gestionar la creación y edición de términos desde el panel de administración, incluyendo el contenido editorial asociado (como título, estado de publicación, fecha de publicación y acepciones).
