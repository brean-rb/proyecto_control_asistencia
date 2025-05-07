import pymysql
import bcrypt

# Configuración de la base de datos (según config.php)
DB_HOST = 'localhost'
DB_USER = 'root'
DB_PASS = ''
DB_NAME = 'gestion_guardias_asistencias'

# Conexión a la base de datos
conexion = pymysql.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME,
    charset='utf8mb4'
)

try:
    with conexion.cursor() as cursor:
        # Obtener todos los DNIs de profesores que no están en usuarios
        cursor.execute("""
            SELECT p.dni
            FROM profesores p
            WHERE p.dni NOT IN (SELECT u.documento FROM usuarios u)
        """)
        nuevos_docentes = cursor.fetchall()

        if not nuevos_docentes:
            print("No hay nuevos docentes para insertar.")
        else:
            password_hash = bcrypt.hashpw('secret'.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
            insertados = 0
            for (dni,) in nuevos_docentes:
                try:
                    cursor.execute(
                        "INSERT INTO usuarios (documento, password, rol) VALUES (%s, %s, %s)",
                        (dni, password_hash, 'profesor')
                    )
                    print(f"Docente {dni} insertado correctamente.")
                    insertados += 1
                except Exception as e:
                    print(f"Error al insertar docente {dni}: {e}")

            conexion.commit()
            print(f"\nTotal de docentes insertados: {insertados}")

finally:
    conexion.close()