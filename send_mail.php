<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars($_POST['nombre']);
    $comentario = htmlspecialchars($_POST['comentario']);
    $to = "ecototebagstore@gmail.com";
    $subject = "Nuevo comentario de " . $nombre;

    $message = "
    <html>
    <head>
    <title>Nuevo comentario</title>
    </head>
    <body>
    <p><strong>Nombre:</strong> $nombre</p>
    <p><strong>Comentario:</strong> $comentario</p>
    </body>
    </html>
    ";

    // Headers for email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: <$to>" . "\r\n";

    // Handle the file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_type = $_FILES['file']['type'];

        $handle = fopen($file_tmp, "r");
        $content = fread($handle, $file_size);
        fclose($handle);

        $encoded_content = chunk_split(base64_encode($content));

        $boundary = md5("random"); // define boundary with a md5 hashed value

        // Headers for attachment
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n";

        // Multipart boundary
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($message));

        // Preparing attachment
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $file_type; name=" . $file_name . "\r\n";
        $body .= "Content-Disposition: attachment; filename=" . $file_name . "\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "X-Attachment-Id: " . rand(1000, 99999) . "\r\n\r\n";
        $body .= $encoded_content; // Attaching the encoded file

        $body .= "--$boundary--";
    } else {
        $body = $message;
    }

    // Send email
    if (mail($to, $subject, $body, $headers)) {
        echo "Correo enviado con éxito.";
    } else {
        echo "Error al enviar el correo.";
    }
} else {
    echo "Método de solicitud no válido.";
}
?>
