# 📚 Aplicación de Control de Asistencia y Gestión de Guardias

Este proyecto ha sido desarrollado como parte del módulo de **Proyecto del Ciclo Superior de Desarrollo de Aplicaciones Web (DAW)**. La aplicación permite llevar un control del inicio y fin de jornada laboral del profesorado, así como gestionar las guardias en caso de ausencias.

---

## ⚙️ Requisitos

- **PHP** 7.4 o superior  
- **MySQL / MariaDB**  
- **XAMPP** (recomendado para pruebas locales)  
- **Python 3.8+** (para scripts de gestión de usuarios)  
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

3. Importa el archivo SQL que encontrarás en el proyecto (`database/gestion_guardias_asistencias.sql` o `database/guardias.sql`) para generar todas las tablas necesarias.

---

## 👤 Usuarios de prueba

Puedes usar los siguientes usuarios para iniciar sesión:

| Documento   | Contraseña | Rol       |
|-------------|------------|-----------|
| 11111111A   | secret     | admin     |
| 22222222B   | secret     | profesor  |

También puedes iniciar sesión con cualquier DNI de profesor que exista en la tabla `profesores` o `docent`, usando siempre la contraseña `secret`.

> **Nota**: Todos los profesores importados tendrán el rol de 'profesor' por defecto.

---

## 🧩 Gestión de usuarios y contraseñas seguras

### Contraseñas hasheadas

Las contraseñas de los usuarios se almacenan de forma segura usando hash bcrypt.  
Para actualizar todas las contraseñas existentes a formato seguro, utiliza el script Python:

```bash
cd server
python actualizar_passwords.py
```

Este script solo hasheará contraseñas que no estén ya encriptadas.

### Añadir automáticamente docentes como usuarios

> ⚠️ **IMPORTANTE**: Este paso solo es necesario si utilizas la base de datos `guardias.sql`. Si estás usando `gestion_guardias_asistencias.sql`, los usuarios ya están creados automáticamente.

Puedes usar el script Python para insertar todos los docentes en la tabla `usuarios` como profesores (con contraseña segura):

```bash
cd server
python insertar_docentes_como_usuarios.py
```

Esto añadirá todos los docentes que no estén ya en la tabla `usuarios`, con contraseña `secret` (hasheada) y rol `profesor`.

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

- Inicio de sesión con DNI y contraseña (con opción de mostrar/ocultar contraseña).
- Contraseñas seguras (bcrypt).
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
proyecto_control_asistencia/
├── client/
│   ├── src/
│   │   ├── css/
│   │   ├── js/
│   │   ├── login.php
│   │   ├── index.php
│   │   ├── consulta_guardias.php
│   │   ├── consulta_asistencia.php
│   │   ├── registro_ausencia.php
│   │   └── informe_ausencias.php
│   └── vendor/
├── server/
│   ├── config/
│   │   └── config.php
│   ├── script/
│   │   ├── actualizar_passwords.py
│   │   └── insertar_docentes_como_usuarios.py
│   ├── consultar_asistencia.php
│   ├── consultar_guardias.php
│   ├── generar_informe.php
│   ├── horarios.php
│   ├── obtener_horario_ausente.php
│   ├── obtener_horario_profesor.php
│   ├── procesar_ausencia.php
│   ├── registrar_guardia.php
│   ├── registrar_jornada.php
│   ├── registro_sesion.txt
│   ├── listar_docentes.php
│   ├── logout.php
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
- **Python 3**  
- **XAMPP**

---

## 📝 Licencia

Este proyecto es de uso **educativo**, desarrollado dentro del módulo de **Proyecto DAW**. Su uso está permitido solo con fines formativos.

---

Desarrollado por: **Rubén Ferrer**  
Centro: **IES Joan Coromines**  
Curso: **DAW 2024–2025**
