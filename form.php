<?php
$mensaje_exito = "";
$mensaje_error = "";


// Captura la respuesta del captcha y la IP del usuario
$ip = $_SERVER['REMOTE_ADDR'];
$captcha = $_POST['g-recaptcha-response'];

// Clave secreta de reCAPTCHA
$secretkey = "6LfZtewrAAAAAGhQpB923qnw4LWqjsresZS_Vfs4";

// Validación de Captcha
$respuesta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretkey&response=$captcha&remoteip=$ip");
$atributos = json_decode($respuesta, true);

// Comprueba si la respuesta del captcha es válida
if (!$atributos['success']) {
    echo "Verificación de CAPTCHA fallida. Por favor, intenta de nuevo.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpiar y asignar datos del formulario
    $nombre   = htmlspecialchars(trim($_POST['name'] ?? ''));
    $empresa  = htmlspecialchars(trim($_POST['company'] ?? ''));
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telefono = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $mensaje  = htmlspecialchars(trim($_POST['message'] ?? ''));

    // Validación mínima
    if (!$nombre || !$email || !$mensaje) {
        $mensaje_error = "Faltan datos obligatorios.";
    } else {
        // Asunto del correo
        $asuntoCorreo = "Consulta de $nombre";

        // Mensaje del correo
        $mensajeCorreo = "Nuevo mensaje desde el formulario de contacto:\n\n";
        $mensajeCorreo .= "Nombre: $nombre\n";
        $mensajeCorreo .= "Empresa: $empresa\n";
        $mensajeCorreo .= "Correo Electrónico: $email\n";
        $mensajeCorreo .= "Teléfono: $telefono\n";
        $mensajeCorreo .= "Mensaje:\n$mensaje";

        // Destinatario final
        $destinatario = 'laboratorioscrisol@gmail.com';

        // Encabezados
        $headers = "From: Web Crisol <no-reply@tudominio.com>\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        

        // Enviar correo
        if (mail($destinatario, $asuntoCorreo, $mensajeCorreo, $headers)) {
            $mensaje_exito = "Tu mensaje ha sido enviado con éxito. ¡Gracias!";
            exit;
        } else {
            $mensaje_error = "Ocurrió un error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.";
        }
    }
}
?>
