<?php

// 1. CONFIGURACIÓN DEL CORREO
// ====================================================================

// **¡IMPORTANTE!** Reemplaza esta dirección con tu correo electrónico
$destinatario = "jorge.vizuete.mendez@gmail.com"; 

$asunto_base = "Nuevo Mensaje de Contacto desde tu Web"; 
$cabeceras = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";


// 2. VERIFICACIÓN Y RECOLECCIÓN DE DATOS
// ====================================================================

// Verifica si se ha enviado el formulario por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Limpia y recoge los datos del formulario. 
    // Usamos 'htmlspecialchars' y 'trim' para seguridad básica.
    $nombre = htmlspecialchars(trim($_POST['nombreCompleto']));
    $email = htmlspecialchars(trim($_POST['emailContacto']));
    $asunto_input = htmlspecialchars(trim($_POST['asunto']));
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));

    // Validación básica: Comprueba que los campos requeridos no estén vacíos
    if (empty($nombre) || empty($email) || empty($mensaje) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Manejo de error si faltan datos
        http_response_code(400); // Bad Request
        echo "Por favor, completa todos los campos requeridos y asegúrate de que el correo electrónico sea válido.";
        exit;
    }

    // 3. CONSTRUCCIÓN DEL MENSAJE
    // ====================================================================

    // Establece el asunto final
    $asunto_final = $asunto_input ? $asunto_base . ": " . $asunto_input : $asunto_base;
    
    // Configura la cabecera 'Reply-To' para poder responder directamente al remitente
    $cabeceras .= "Reply-To: " . $nombre . " <" . $email . ">" . "\r\n";
    $cabeceras .= "From: " . $nombre . " <" . $email . ">" . "\r\n"; // Es mejor usar un correo del mismo dominio en 'From' para evitar spam

    // Prepara el cuerpo del mensaje en formato HTML
    $cuerpo_mensaje = '
        <html>
        <head>
            <title>' . $asunto_final . '</title>
        </head>
        <body>
            <h2>Mensaje Recibido</h2>
            <p><strong>De:</strong> ' . $nombre . '</p>
            <p><strong>Correo Electrónico:</strong> ' . $email . '</p>
            <p><strong>Asunto:</strong> ' . ($asunto_input ? $asunto_input : "Sin asunto proporcionado") . '</p>
            <hr>
            <h3>Mensaje:</h3>
            <p style="white-space: pre-line;">' . $mensaje . '</p>
        </body>
        </html>
    ';

    // 4. ENVÍO DEL CORREO
    // ====================================================================

    // La función mail() de PHP intenta enviar el correo
    if (mail($destinatario, $asunto_final, $cuerpo_mensaje, $cabeceras)) {
        // Redirección exitosa: Vuelve a la página principal o a una de "Gracias"
        header("Location: contacto.html"); 
        exit;
    } else {
        // Manejo de error si el servidor no pudo enviar el correo
        http_response_code(500); // Internal Server Error
        echo "Lo sentimos, el servidor no pudo enviar tu mensaje. Intenta de nuevo más tarde.";
        exit;
    }

} else {
    // Si alguien intenta acceder al script directamente sin POST
    http_response_code(403); // Forbidden
    echo "Acceso Denegado.";
    exit;
}

?>