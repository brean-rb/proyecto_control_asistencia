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
        cursor.execute("SELECT id, documento, password FROM usuarios")
        usuarios = cursor.fetchall()

        actualizados = 0
        errores = 0

        for usuario in usuarios:
            user_id, documento, password = usuario
            # Generar hash seguro con bcrypt
            hashed = bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
            try:
                cursor.execute(
                    "UPDATE usuarios SET password = %s WHERE id = %s",
                    (hashed, user_id)
                )
                print(f"Usuario {documento} actualizado correctamente.")
                actualizados += 1
            except Exception as e:
                print(f"Error al actualizar usuario {documento}: {e}")
                errores += 1

        conexion.commit()
        print("\nResumen:")
        print(f"Usuarios actualizados: {actualizados}")
        print(f"Errores: {errores}")

finally:
    conexion.close()
