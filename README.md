# 📚 Aplicación de Control de Asistencia y Gestión de Guardias

Este proyecto ha sido desarrollado como parte del módulo de **Proyecto del Ciclo Superior de Desarrollo de Aplicaciones Web (DAW)**. La aplicación permite llevar un control del inicio y fin de jornada laboral del profesorado, así como gestionar las guardias en caso de ausencias.

---

## ⚙️ Requisitos

- **PHP** 7.4 o superior  
- **MySQL / MariaDB**  
- **XAMPP** (recomendado para pruebas locales)  
- Navegador moderno (Chrome, Firefox, Edge...)

---

## 🚀 Instalación en local con XAMPP

### 1. Clona el repositorio

```bash
git clone https://github.com/brean-rb/proyecto_control_asistencia.git
```

### 2. Copia el proyecto a la carpeta de XAMPP

```bash
mv proyecto_control_asistencia/ C:/xampp/htdocs/
```

O hazlo manualmente moviendo la carpeta al directorio `htdocs`.

### 3. Inicia Apache y MySQL desde el panel de XAMPP

### 4. Crea la base de datos

1. Abre [http://localhost/phpmyadmin](http://localhost/phpmyadmin).  
2. Crea una base de datos llamada:

   ```
   gestion_guardias_asistencias
   ```

3. Importa el archivo SQL que encontrarás en el proyecto (`database/guardias.sql`) para generar todas las tablas necesarias.

---

## 👤 Usuarios de prueba

Puedes usar los siguientes usuarios para iniciar sesión:

| Documento   | Contraseña | Rol       |
|-------------|------------|-----------|
| 11111111A   | secret     | admin     |
| 22222222B   | secret     | profesor  |

También puedes iniciar sesión con cualquier DNI de profesor que exista en la tabla `profesores`, usando siempre la contraseña `secret`.

> **Nota**: Todos los profesores importados tendrán el rol de 'profesor' por defecto.

---

## 🧩 Añadir automáticamente docentes como usuarios

> ⚠️ **IMPORTANTE**: Este paso solo es necesario si utilizas la base de datos `guardias.sql`. Si estás usando `gestion_guardias_asistencias.sql`, los usuarios ya están creados automáticamente.

Usa esta consulta SQL para insertar todos los docentes en la tabla `usuarios` como profesores:

```sql
INSERT INTO usuarios (documento, password, rol)
SELECT p.dni, 'secret', 'profesor'
FROM profesores p
WHERE p.dni NOT IN (SELECT u.documento FROM usuarios u);
```

---

## 🔧 Configuración de conexión

Edita el archivo:

```
/server/config/config.php
```

Y asegúrate de que los datos son correctos para XAMPP:

```php
define('SERVER', 'localhost');
define('USER', 'root');
define('PASSWORD', '');
define('DATABASE', 'gestion_guardias_asistencias');
```

---

## 🧪 Cómo acceder

Abre tu navegador y accede a:

```
http://localhost/proyecto_control_asistencia/client/src/login.php
```

Inicia sesión con cualquiera de los usuarios de prueba.

---

## 🧩 Funcionalidades principales

### 👥 Login y logout

- Inicio de sesión con DNI y contraseña.  
- Registro de entradas y salidas en archivo `.txt`.

### ⏱ Registro de jornada

- Iniciar jornada laboral (una vez al día).  
- Finalizar jornada.  
- Datos guardados en `registro_jornada`.

### 📅 Horario del docente

- Tabla visible tras iniciar sesión.  
- Filtrada por el `documento` del docente.

### 🔐 Panel de administración

Visible solo para usuarios con rol **admin**:

- Consultar asistencias.  
- Registrar ausencias.  
- Generar informes de guardias.

---

## 📂 Estructura del proyecto

```
control_asistencia_y_gestion_guardias/
├── client/
│   └── src/
│       ├── css/
│       │   └── styles.css
│       ├── js/
│       │   ├── login.js
│       │   ├── consulta_guardias.js
│       │   ├── registro_ausencia.js
│       │   ├── consulta_asistencia.js
│       │   └── informe_ausencias.js
│       ├── login.php
│       ├── index.php
│       ├── consulta_guardias.php
│       ├── consulta_asistencia.php
│       ├── registro_ausencia.php
│       └── informe_ausencias.php
├── server/
│   ├── config/
│   │   └── config.php
│   ├── consultar_asistencia.php
│   ├── consultar_guardias.php
│   ├── generar_informe.php
│   ├── horarios.php
│   ├── obtener_horario_ausente.php
│   ├── obtener_horario_profesor.php
│   ├── procesar_ausencia.php
│   ├── registrar_guardia.php
│   ├── registrar_jornada.php
│   └── registro_sesion.txt
├── database/
│   ├── gestion_guardias_asistencias.sql
│   └── guardias.sql
└── README.md
```

---

## 🛠 Tecnologías utilizadas

- **HTML5 / CSS3**  
- **Bootstrap 5** (local)  
- **JavaScript**  
- **PHP** (sin frameworks)  
- **MySQL**  
- **XAMPP**

---

## 📝 Licencia

Este proyecto es de uso **educativo**, desarrollado dentro del módulo de **Proyecto DAW**. Su uso está permitido solo con fines formativos.

---

Desarrollado por: **Rubén Ferrer**  
Centro: **IES Joan Coromines**  
Curso: **DAW 2024–2025**
