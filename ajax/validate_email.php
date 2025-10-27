<?php
// ajax/validate_email.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// Solo aceptar peticiones AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'fetch') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$email = trim($_GET['email'] ?? '');

if (empty($email)) {
    echo json_encode(['success' => false, 'valid' => false, 'message' => 'Email vacío']);
    exit;
}

/**
 * Valida que el correo no tenga patrones sospechosos
 */
function validar_patron_email(string $email): ?string {
    $partes = explode('@', $email);
    if (count($partes) !== 2) {
        return 'Formato de email inválido';
    }
    
    $local = strtolower($partes[0]);
    $domain = strtolower($partes[1]);
    
    // 1. Longitud mínima del local (antes del @)
    if (strlen($local) < 3) {
        return 'El nombre de usuario es demasiado corto (mín. 3 caracteres)';
    }
    
    // 2. Detectar secuencias repetitivas sospechosas (aaaa, xxxx, 1111)
    if (preg_match('/(.)\1{3,}/', $local)) {
        return 'El correo contiene caracteres repetitivos sospechosos';
    }
    
    // 3. Detectar patrones aleatorios comunes
    // Patrones como: asdfgh, qwerty, abcdef, 123456, xxxxyyy, etc.
    $patrones_sospechosos = [
        '/^[a-z]{3}\d{3,}$/',           // abc123, xyz789
        '/^[a-z]{6,}$/',                 // asdfgh, qwerty (solo letras consecutivas)
        '/^\d{4,}$/',                    // 12345, 67890 (solo números)
        '/^(x+|y+|z+|a+)(x+|y+|z+|a+)/', // xxx, yyy, xxxyyy, aaabbb
        '/^test\d*$/',                   // test, test1, test123
        '/^user\d*$/',                   // user, user1, user123
        '/^admin\d*$/',                  // admin, admin1
        '/^demo\d*$/',                   // demo, demo1
        '/^(abc|xyz|qwe|asd|zxc)\d*$/', // abc123, xyz456
    ];
    
    foreach ($patrones_sospechosos as $patron) {
        if (preg_match($patron, $local)) {
            return 'El correo parece ser generado aleatoriamente o de prueba';
        }
    }
    
    // 4. Verificar que no sea completamente aleatorio
    // Un correo real suele tener vocales y consonantes intercaladas
    $vocales = preg_match_all('/[aeiou]/', $local);
    $consonantes = preg_match_all('/[bcdfghjklmnpqrstvwxyz]/', $local);
    $numeros = preg_match_all('/[0-9]/', $local);
    $total = strlen($local);
    
    // Si es muy largo y tiene muy pocas vocales, es sospechoso
    if ($total > 8 && $vocales === 0) {
        return 'El correo parece no ser válido (sin vocales)';
    }
    
    // Si tiene solo 2-3 caracteres diferentes repetidos (xxxyyy, aaabbb)
    $caracteres_unicos = count(array_unique(str_split($local)));
    if ($total > 6 && $caracteres_unicos <= 3) {
        return 'El correo tiene un patrón demasiado repetitivo';
    }
    
    // 5. Verificar secuencias consecutivas largas (abcdef, 123456)
    for ($i = 0; $i < strlen($local) - 3; $i++) {
        $seq = substr($local, $i, 4);
        // Verificar si son 4 caracteres consecutivos en el alfabeto
        if (preg_match('/^[a-z]+$/', $seq)) {
            $consecutivos = true;
            for ($j = 1; $j < strlen($seq); $j++) {
                if (ord($seq[$j]) !== ord($seq[$j-1]) + 1) {
                    $consecutivos = false;
                    break;
                }
            }
            if ($consecutivos) {
                return 'El correo contiene secuencias alfabéticas sospechosas';
            }
        }
        // Verificar si son 4 números consecutivos
        if (preg_match('/^\d+$/', $seq)) {
            $consecutivos = true;
            for ($j = 1; $j < strlen($seq); $j++) {
                if ((int)$seq[$j] !== (int)$seq[$j-1] + 1) {
                    $consecutivos = false;
                    break;
                }
            }
            if ($consecutivos) {
                return 'El correo contiene secuencias numéricas sospechosas';
            }
        }
    }
    
    // 6. Lista de nombres comunes falsos
    $nombres_falsos = [
        'asdasd', 'asdfgh', 'qwerty', 'qwertyui', 'zxcvbn',
        'testtest', 'test123', 'prueba', 'ejemplo',
        'xxxyyy', 'aaabbb', 'noname', 'random', 'fake',
        'temporal', 'temp', 'basura', 'spam'
    ];
    
    foreach ($nombres_falsos as $falso) {
        if (strpos($local, $falso) !== false) {
            return 'El correo parece ser temporal o de prueba';
        }
    }
    
    return null; // Patrón válido
}

/**
 * Valida que el correo sea real y accesible
 */
function validar_email_completo(string $email): array {
    // 1. Validación de formato básico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'valid' => false,
            'message' => 'El formato del correo no es válido'
        ];
    }
    
    // 2. Validar patrones sospechosos
    $error_patron = validar_patron_email($email);
    if ($error_patron !== null) {
        return [
            'success' => false,
            'valid' => false,
            'message' => $error_patron
        ];
    }
    
    // 3. Extraer dominio
    $parts = explode('@', $email);
    if (count($parts) !== 2) {
        return [
            'success' => false,
            'valid' => false,
            'message' => 'El correo no tiene un formato válido'
        ];
    }
    
    $domain = $parts[1];
    
    // 4. Verificar que el dominio tenga registros MX o A
    $has_mx = checkdnsrr($domain, 'MX');
    $has_a = checkdnsrr($domain, 'A');
    
    if (!$has_mx && !$has_a) {
        return [
            'success' => false,
            'valid' => false,
            'message' => 'El dominio del correo no existe o no puede recibir emails'
        ];
    }
    
    // 5. Lista de dominios desechables/temporales
    $disposable_domains = [
        'tempmail.com', 'guerrillamail.com', '10minutemail.com', 
        'throwaway.email', 'mailinator.com', 'trashmail.com', 
        'yopmail.com', 'maildrop.cc', 'temp-mail.org',
        'fakeinbox.com', 'sharklasers.com', 'guerrillamailblock.com',
        'pokemail.net', 'spam4.me', 'grr.la', 'dispostable.com',
        'tempinbox.com', 'minuteinbox.com', 'emailondeck.com',
        'mytemp.email', 'mohmal.com', 'moakt.com'
    ];
    
    if (in_array(strtolower($domain), $disposable_domains, true)) {
        return [
            'success' => false,
            'valid' => false,
            'message' => 'No se permiten correos temporales o desechables'
        ];
    }
    
    // 6. Verificar dominios populares conocidos
    $trusted_domains = [
        'gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com',
        'icloud.com', 'protonmail.com', 'live.com', 'msn.com',
        'aol.com', 'zoho.com', 'mail.com', 'gmx.com'
    ];
    
    $is_trusted = in_array(strtolower($domain), $trusted_domains, true);
    
    return [
        'success' => true,
        'valid' => true,
        'verified' => $has_mx,
        'trusted' => $is_trusted,
        'message' => $is_trusted 
            ? 'Correo verificado como válido' 
            : 'Correo válido (dominio verificado)'
    ];
}

// Ejecutar validación
$resultado = validar_email_completo($email);
echo json_encode($resultado, JSON_UNESCAPED_UNICODE);