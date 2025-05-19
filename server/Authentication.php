<?php
class Authentication {
    private $decodedToken = null;
    private $jwtKey = "123.proyecto_control_asistencia.zzz"; // Clave secreta para firmar los tokens

    public function getDecodedToken() {
        return $this->decodedToken;
    }

    /**
     * Valida que en la cabecera HTTP "Authorization" haya un token válido
     * @return Mensaje de error (vacío si todo ha ido bien)
     */
    public function validaToken() {
        $error = '';
        if(!array_key_exists('Authorization', getallheaders())) {
            $error = 'No se ha iniciado sesión en la aplicación';
        } else {
            $authorization = getallheaders()['Authorization'];
            $trozos = explode(' ', $authorization);
            $auth = $trozos[1]; // Normalmente recibimos 'Bearer token'

            try {
                $decoded = $this->decodeToken($auth);
                $this->decodedToken = $decoded;
            } catch(Exception $e) {
                $error = 'Token inválido o expirado';
            }
        }
        return $error;
    }

    /**
     * Genera un token para la autenticación del usuario
     * @param $usuario Objeto con los datos del usuario autenticado
     * @param $rol Rol del usuario
     * @return Token generado
     */
    public function generaToken($usuario, $rol = 'profesor') {
        $tiempo = time();
        $payload = [
            'exp' => $tiempo + (60 * 60 * 24), // Caduca en 24 horas
            'id' => $usuario,
            'rol' => $rol,
        ];
        return $this->encodeToken($payload);
    }

    /**
     * Codifica un token JWT
     */
    private function encodeToken($payload) {
        $header = $this->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = $this->base64UrlEncode(json_encode($payload));
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $this->jwtKey, true)
        );
        return "$header.$payload.$signature";
    }

    /**
     * Decodifica un token JWT
     */
    private function decodeToken($token) {
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            throw new Exception('Token inválido');
        }

        $payload = json_decode($this->base64UrlDecode($parts[1]), true);
        if (!$payload) {
            throw new Exception('Token inválido');
        }

        // Verificar expiración
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token expirado');
        }

        // Verificar firma
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', "$parts[0].$parts[1]", $this->jwtKey, true)
        );
        if ($signature !== $parts[2]) {
            throw new Exception('Firma inválida');
        }

        return $payload;
    }

    /**
     * Codifica en base64url
     */
    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodifica desde base64url
     */
    private function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
?> 