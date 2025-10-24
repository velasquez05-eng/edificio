<?php
require_once '../../includes/phpmailer/PHPMailer.php';
require_once '../../includes/phpmailer/SMTP.php';
require_once '../../includes/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class CorreoModelo {
    private $mailer;
    private $config;

    public function __construct() {
        $this->config = [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_username' => 'sys.codex.dev@gmail.com',
            'smtp_password' => 'uvif khxh erfq ehsa',
            'from_email' => 'sys.codex.dev@gmail.com',
            'from_name' => 'Sistema de Gestión de Edificios'
        ];

        $this->inicializarMailer();
    }

    private function inicializarMailer() {
        $this->mailer = new PHPMailer(true);

        try {
            // Configuración del servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp_host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['smtp_username'];
            $this->mailer->Password = $this->config['smtp_password'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $this->config['smtp_port'];
            $this->mailer->CharSet = 'UTF-8';

            // Remitente
            $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);

        } catch (Exception $e) {
            error_log("Error inicializando PHPMailer: " . $e->getMessage());
        }
    }

    // Notificar credenciales al registrar
    public function notificarCredenciales($email, $nombre, $username, $password) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $nombre);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Credenciales de Acceso - Sistema de Gestión';

            // Calcular fechas
            date_default_timezone_set('America/La_Paz');
            $fecha_envio = date('d/m/Y');
            $fecha_limite = date('d/m/Y', strtotime('+3 days'));

            $mensaje = "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"language\" content=\"es\">
    <style type=\"text/css\">
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            color: #333333;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(13,61,71,0.15);
            overflow: hidden;
        }
        .letterhead {
            background: linear-gradient(135deg, #0d3d47 0%, #2a7595 100%);
            color: #ffffff;
            padding: 30px 35px;
            text-align: center;
        }
        .letterhead h1 {
            margin: 0 0 6px 0;
            font-size: 28px;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .subtitle {
            font-size: 16px;
            opacity: 0.85;
        }
        .content {
            padding: 30px 35px;
        }
        .salutation {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .credentials-section {
            background-color: #afefce;
            border-left: 5px solid #368979;
            padding: 20px 25px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .credentials-title {
            font-weight: 600;
            margin-bottom: 12px;
            color: #0d3d47;
            font-size: 16px;
        }
        .credential-row {
            display: block;
            margin: 12px 0;
        }
        .credential-label {
            font-weight: 600;
            display: inline-block;
            width: 120px;
            color: #2a7595;
        }
        .credential-value {
            display: inline-block;
            background-color: #f8f9fa;
            border: 2px solid #6c757d;
            border-radius: 6px;
            padding: 8px 12px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #495057;
            min-width: 200px;
        }
        .timeline {
            display: table;
            width: 100%;
            margin: 25px 0;
            border-collapse: separate;
            border-spacing: 10px;
        }
        .timeline-item {
            display: table-cell;
            text-align: center;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px 10px;
            background-color: #ffffff;
        }
        .timeline-date {
            font-weight: 700;
            color: #368979;
            font-size: 14px;
        }
        .timeline-label {
            font-size: 12px;
            color: #555555;
            margin-top: 4px;
        }
        .important-notice {
            background-color: #fff8e1;
            border-left: 5px solid #ffb300;
            padding: 18px 22px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .instructions {
            background-color: #e0f7fa;
            border-left: 5px solid #2a7595;
            padding: 18px 22px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .signature {
            margin-top: 35px;
            line-height: 1.5;
        }
        .contact-info {
            margin-top: 10px;
            font-size: 14px;
            color: #555555;
        }
        .footer {
            background-color: #f4f7fa;
            padding: 20px 35px;
            font-size: 12px;
            color: #666666;
            text-align: center;
            line-height: 1.4;
        }
        a {
            color: #2a7595;
            text-decoration: none;
            font-weight: 600;
        }
        strong {
            font-weight: 600;
        }
        h4 {
            margin: 0 0 10px 0;
        }
        p {
            margin: 0 0 15px 0;
        }
    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"letterhead\">
            <h1>SEInt</h1>
            <div class=\"subtitle\">Sistema De Edificio Inteligente</div>
        </div>

        <div class=\"content\">
            <div class=\"salutation\">
                Estimado(a) <strong>" . htmlspecialchars($nombre) . "</strong>,
            </div>

            <p>Por medio de la presente, nos complace informarle que se ha creado su cuenta de acceso a <strong>SEInt</strong>. A continuación, encontrará las credenciales provisionales para acceder a la plataforma.</p>

            <div class=\"credentials-section\">
                <div class=\"credentials-title\">🔐 CREDENCIALES DE ACCESO TEMPORAL</div>
                <div class=\"credential-row\">
                    <span class=\"credential-label\">Usuario:</span>
                    <span class=\"credential-value\">" . htmlspecialchars($username) . "</span>
                </div>
                <div class=\"credential-row\">
                    <span class=\"credential-label\">Contraseña:</span>
                    <span class=\"credential-value\">" . htmlspecialchars($password) . "</span>
                </div>
            </div>

            <div class=\"timeline\">
                <div class=\"timeline-item\">
                    <div class=\"timeline-date\">" . $fecha_envio . "</div>
                    <div class=\"timeline-label\">Fecha de Emisión</div>
                </div>
                <div class=\"timeline-item\">
                    <div class=\"timeline-date\">" . $fecha_limite . "</div>
                    <div class=\"timeline-label\">Fecha Límite de Activación</div>
                </div>
            </div>

            <div class=\"important-notice\">
                <h4 style=\"color: #856404;\">⚠️ INSTRUCCIONES IMPORTANTES</h4>
                <p>
                • <strong>Tiene 3 días calendario para activar su cuenta</strong>, contados a partir de la fecha de emisión.<br>
                • Al primer acceso, deberá cambiar su contraseña por motivos de seguridad.<br>
                • Las credenciales son de uso personal e intransferible.
                </p>
            </div>

            <div class=\"instructions\">
                <h4 style=\"color: #0c5460;\">📋 PROCEDIMIENTO DE ACCESO</h4>
                <ol style=\"margin: 0; padding-left: 20px;\">
                    <li>Diríjase al portal del sistema.</li>
                    <li>Ingrese las credenciales proporcionadas en este correo.</li>
                    <li>Establezca una nueva contraseña de acceso.</li>
                </ol>
            </div>

            <p>Para cualquier inconveniente o consulta relacionada con el acceso al sistema, puede contactar con el administrador del sistema.</p>

            <div class=\"signature\">
                <p>Atentamente,<br>
                <strong>Departamento de Administración</strong><br>
                SEInt</p>
                <div class=\"contact-info\">
                    📞 Teléfono: +(591) 76543210 | ✉️ Email: <a href=\"mailto:sys.codex.dev@gmail.com\">sys.codex.dev@gmail.com</a><br>
                    🏢 Oficina: Av. Principal #123, Ciudad La Paz, Bolivia.
                </div>
            </div>
        </div>

        <div class=\"footer\">
            <p>
                <em>Este es un mensaje automático generado por el sistema. Por favor no responda a este correo.</em><br>
                <em>Si recibió este mensaje por error, favor eliminarlo y notificar al departamento de sistemas.</em>
            </p>
        </div>
    </div>
</body>
</html>";

            $this->mailer->Body = $mensaje;
            $this->mailer->AltBody = "Hola $nombre,\n\nTu cuenta ha sido creada exitosamente.\nUsuario: $username\nContraseña: $password\n\nTienes 3 días para activar tu cuenta.\n\nAccede al sistema con las credenciales proporcionadas.";

            return $this->mailer->send();

        } catch (Exception $e) {
            error_log("Error enviando notificación de credenciales: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function notificarRestablecimientoPassword($email, $nombre) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $nombre);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Restablecimiento de Contraseña - Sistema de Gestión';

            // Calcular fechas
            date_default_timezone_set('America/La_Paz');
            $fecha_envio = date('d/m/Y');
            $fecha_limite = date('d/m/Y', strtotime('+1 day'));

            // Usar HEREDOC para mejor legibilidad y evitar problemas de comillas
            $mensaje = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="language" content="es">
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            color: #333333;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(13,61,71,0.15);
            overflow: hidden;
        }
        .letterhead {
            background: linear-gradient(135deg, #0d3d47 0%, #2a7595 100%);
            color: #ffffff;
            padding: 30px 35px;
            text-align: center;
        }
        .letterhead h1 {
            margin: 0 0 6px 0;
            font-size: 28px;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .subtitle {
            font-size: 16px;
            opacity: 0.85;
        }
        .content {
            padding: 30px 35px;
        }
        .salutation {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .credentials-section {
            background-color: #afefce;
            border-left: 5px solid #368979;
            padding: 20px 25px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .credentials-title {
            font-weight: 600;
            margin-bottom: 12px;
            color: #0d3d47;
            font-size: 16px;
        }
        .credential-row {
            display: block;
            margin: 12px 0;
        }
        .credential-label {
            font-weight: 600;
            display: inline-block;
            width: 120px;
            color: #2a7595;
        }
        .credential-value {
            display: inline-block;
            background-color: #f8f9fa;
            border: 2px solid #6c757d;
            border-radius: 6px;
            padding: 8px 12px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #495057;
            min-width: 200px;
        }
        .timeline {
            display: table;
            width: 100%;
            margin: 25px 0;
            border-collapse: separate;
            border-spacing: 10px;
        }
        .timeline-item {
            display: table-cell;
            text-align: center;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px 10px;
            background-color: #ffffff;
        }
        .timeline-date {
            font-weight: 700;
            color: #368979;
            font-size: 14px;
        }
        .timeline-label {
            font-size: 12px;
            color: #555555;
            margin-top: 4px;
        }
        .important-notice {
            background-color: #fff8e1;
            border-left: 5px solid #ffb300;
            padding: 18px 22px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .instructions {
            background-color: #e0f7fa;
            border-left: 5px solid #2a7595;
            padding: 18px 22px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .signature {
            margin-top: 35px;
            line-height: 1.5;
        }
        .contact-info {
            margin-top: 10px;
            font-size: 14px;
            color: #555555;
        }
        .footer {
            background-color: #f4f7fa;
            padding: 20px 35px;
            font-size: 12px;
            color: #666666;
            text-align: center;
            line-height: 1.4;
        }
        a {
            color: #2a7595;
            text-decoration: none;
            font-weight: 600;
        }
        strong {
            font-weight: 600;
        }
        h4 {
            margin: 0 0 10px 0;
        }
        p {
            margin: 0 0 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="letterhead">
            <h1>SEInt</h1>
            <div class="subtitle">Sistema De Edificio Inteligente</div>
        </div>

        <div class="content">
            <div class="salutation">
                Estimado(a) <strong>{$nombre}</strong>,
            </div>

            <p>Por medio de la presente, le informamos que se ha restablecido su contraseña de acceso a <strong>SEInt</strong>. A continuación, encontrará las instrucciones para acceder a la plataforma.</p>

            <div class="credentials-section">
                <div class="credentials-title">🔐 INFORMACIÓN DE ACCESO TEMPORAL</div>
                <div class="credential-row">
                    <span class="credential-label">Contraseña Temporal:</span>
                    <span class="credential-value">Su número de carnet de identidad</span>
                </div>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-date">{$fecha_envio}</div>
                    <div class="timeline-label">Fecha de Emisión</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">{$fecha_limite}</div>
                    <div class="timeline-label">Fecha Límite de Activación</div>
                </div>
            </div>

            <div class="important-notice">
                <h4 style="color: #856404;">⚠️ INSTRUCCIONES IMPORTANTES</h4>
                <p>
                • <strong>Tiene 1 día calendario para acceder a su cuenta</strong>, contados a partir de la fecha de emisión.<br>
                • El sistema le pedirá cambiar su contraseña por motivos de seguridad.<br>
                • Las credenciales son de uso personal e intransferible.
                </p>
            </div>

            <div class="instructions">
                <h4 style="color: #0c5460;">📋 PROCEDIMIENTO DE ACCESO</h4>
                <ol style="margin: 0; padding-left: 20px;">
                    <li>Diríjase al portal del sistema.</li>
                    <li>Ingrese su usuario y la contraseña temporal (su carnet de identidad).</li>
                    <li>Establezca una nueva contraseña de acceso.</li>
                </ol>
            </div>

            <p>Para cualquier inconveniente o consulta relacionada con el acceso al sistema, puede contactar con el administrador del sistema.</p>

            <div class="signature">
                <p>Atentamente,<br>
                <strong>Departamento de Administración</strong><br>
                SEInt</p>
                <div class="contact-info">
                    📞 Teléfono: +(591) 76543210 | ✉️ Email: <a href="mailto:sys.codex.dev@gmail.com">sys.codex.dev@gmail.com</a><br>
                    🏢 Oficina: Av. Principal #123, Ciudad La Paz, Bolivia.
                </div>
            </div>
        </div>

        <div class="footer">
            <p>
                <em>Este es un mensaje automático generado por el sistema. Por favor no responda a este correo.</em><br>
                <em>Si recibió este mensaje por error, favor eliminarlo y notificar al departamento de sistemas.</em>
            </p>
        </div>
    </div>
</body>
</html>
HTML;

            $this->mailer->Body = $mensaje;
            $this->mailer->AltBody = "Hola {$nombre},\n\nSe ha restablecido tu contraseña en el sistema.\nContraseña temporal: Tu número de carnet de identidad\n\nTienes 1 día para acceder a tu cuenta y cambiar la contraseña.\n\nAccede al sistema con las credenciales proporcionadas.";

            return $this->mailer->send();

        } catch (Exception $e) {
            error_log("Error enviando notificación de restablecimiento: " . $this->mailer->ErrorInfo);
            return false;
        }
    }


    public function notificarCodigoRecuperacion($email, $nombre_completo, $codigo) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $nombre_completo);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Código de Recuperación - Sistema de Gestión';

            // Calcular fechas y tiempos
            date_default_timezone_set('America/La_Paz');
            $fecha_envio = date('d/m/Y');
            $hora_envio = date('H:i');
            $fecha_expiracion = date('d/m/Y H:i', strtotime('+30 minutes'));

            $mensaje = "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"language\" content=\"es\">
    <style type=\"text/css\">
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            color: #333333;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(13,61,71,0.15);
            overflow: hidden;
        }
        .letterhead {
            background: linear-gradient(135deg, #0d3d47 0%, #2a7595 100%);
            color: #ffffff;
            padding: 30px 35px;
            text-align: center;
        }
        .letterhead h1 {
            margin: 0 0 6px 0;
            font-size: 28px;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .subtitle {
            font-size: 16px;
            opacity: 0.85;
        }
        .content {
            padding: 30px 35px;
        }
        .salutation {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .code-section {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border-radius: 12px;
            padding: 30px 25px;
            margin: 25px 0;
            text-align: center;
            color: #ffffff;
        }
        .code-title {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 18px;
            letter-spacing: 1px;
        }
        .code-value {
            font-size: 42px;
            font-weight: bold;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            background-color: rgba(255,255,255,0.2);
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            display: inline-block;
            min-width: 300px;
        }
        .timeline {
            display: table;
            width: 100%;
            margin: 25px 0;
            border-collapse: separate;
            border-spacing: 10px;
        }
        .timeline-item {
            display: table-cell;
            text-align: center;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px 10px;
            background-color: #ffffff;
        }
        .timeline-date {
            font-weight: 700;
            color: #368979;
            font-size: 14px;
        }
        .timeline-label {
            font-size: 12px;
            color: #555555;
            margin-top: 4px;
        }
        .important-notice {
            background-color: #fff8e1;
            border-left: 5px solid #ffb300;
            padding: 18px 22px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .instructions {
            background-color: #e0f7fa;
            border-left: 5px solid #2a7595;
            padding: 18px 22px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .steps {
            background-color: #f0f8ff;
            border-left: 5px solid #4a90e2;
            padding: 18px 22px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .signature {
            margin-top: 35px;
            line-height: 1.5;
        }
        .contact-info {
            margin-top: 10px;
            font-size: 14px;
            color: #555555;
        }
        .footer {
            background-color: #f4f7fa;
            padding: 20px 35px;
            font-size: 12px;
            color: #666666;
            text-align: center;
            line-height: 1.4;
        }
        a {
            color: #2a7595;
            text-decoration: none;
            font-weight: 600;
        }
        strong {
            font-weight: 600;
        }
        h4 {
            margin: 0 0 10px 0;
        }
        p {
            margin: 0 0 15px 0;
        }
        .step-number {
            background-color: #4a90e2;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"letterhead\">
            <h1>SEInt</h1>
            <div class=\"subtitle\">Sistema De Edificio Inteligente</div>
        </div>

        <div class=\"content\">
            <div class=\"salutation\">
                Estimado(a) <strong>" . htmlspecialchars($nombre_completo) . "</strong>,
            </div>

            <p>Hemos recibido una solicitud para restablecer la contraseña de su cuenta en <strong>SEInt</strong>. A continuación, encontrará el código de verificación necesario para completar el proceso.</p>

            <div class=\"code-section\">
                <div class=\"code-title\">🔒 CÓDIGO DE VERIFICACIÓN</div>
                <div class=\"code-value\">" . htmlspecialchars($codigo) . "</div>
                <div style=\"font-size: 14px; opacity: 0.9;\">Utilice este código en el sistema para verificar su identidad</div>
            </div>

            <div class=\"timeline\">
                <div class=\"timeline-item\">
                    <div class=\"timeline-date\">" . $fecha_envio . "</div>
                    <div class=\"timeline-label\">Fecha de Emisión</div>
                </div>
                <div class=\"timeline-item\">
                    <div class=\"timeline-date\">" . $hora_envio . "</div>
                    <div class=\"timeline-label\">Hora de Emisión</div>
                </div>
                <div class=\"timeline-item\">
                    <div class=\"timeline-date\">" . $fecha_expiracion . "</div>
                    <div class=\"timeline-label\">Fecha y Hora de Expiración</div>
                </div>
            </div>

            <div class=\"important-notice\">
                <h4 style=\"color: #856404;\">⚠️ INFORMACIÓN IMPORTANTE</h4>
                <p>
                • <strong>Este código es válido por 30 minutos</strong> a partir del momento de su emisión.<br>
                • No comparta este código con nadie por motivos de seguridad.<br>
                • Si no solicitó este código, ignore este mensaje y su cuenta permanecerá segura.
                </p>
            </div>

            <div class=\"steps\">
                <h4 style=\"color: #0c5460;\">📋 PROCESO DE RECUPERACIÓN</h4>
                <p style=\"margin-bottom: 12px;\"><span class=\"step-number\">1</span> <strong>Ingresa tu email</strong> - Introduce tu dirección de correo electrónico registrada.</p>
                <p style=\"margin-bottom: 12px;\"><span class=\"step-number\">2</span> <strong>Verifica el código</strong> - Ingresa el código de verificación proporcionado en este correo.</p>
                <p style=\"margin-bottom: 0;\"><span class=\"step-number\">3</span> <strong>Crea nueva contraseña</strong> - Establece una nueva contraseña para tu cuenta.</p>
            </div>

            <div class=\"instructions\">
                <h4 style=\"color: #0c5460;\">🔍 INSTRUCCIONES DE USO</h4>
                <ol style=\"margin: 0; padding-left: 20px;\">
                    <li>Regrese a la página de recuperación de contraseña</li>
                    <li>Ingrese el código de verificación mostrado arriba</li>
                    <li>Complete el proceso estableciendo su nueva contraseña</li>
                </ol>
            </div>

            <p>Si usted no solicitó este código o tiene alguna pregunta sobre la seguridad de su cuenta, por favor contacte inmediatamente con el administrador del sistema.</p>

            <div class=\"signature\">
                <p>Atentamente,<br>
                <strong>Departamento de Seguridad</strong><br>
                SEInt</p>
                <div class=\"contact-info\">
                    📞 Teléfono: +(591) 76543210 | ✉️ Email: <a href=\"mailto:sys.codex.dev@gmail.com\">sys.codex.dev@gmail.com</a><br>
                    🏢 Oficina: Av. Principal #123, Ciudad La Paz, Bolivia.
                </div>
            </div>
        </div>

        <div class=\"footer\">
            <p>
                <em>Este es un mensaje automático generado por el sistema. Por favor no responda a este correo.</em><br>
                <em>Si recibió este mensaje por error, favor eliminarlo y notificar al departamento de sistemas.</em>
            </p>
        </div>
    </div>
</body>
</html>";

            $this->mailer->Body = $mensaje;
            $this->mailer->AltBody = "Hola $nombre_completo,\n\nHemos recibido una solicitud para restablecer tu contraseña.\n\nCódigo de verificación: $codigo\n\nEste código es válido por 30 minutos.\n\nProceso de recuperación:\n1. Ingresa tu email\n2. Verifica el código\n3. Crea nueva contraseña\n\nSi no solicitaste este código, ignora este mensaje.\n\nAtentamente,\nDepartamento de Seguridad - SEInt";

            return $this->mailer->send();

        } catch (Exception $e) {
            error_log("Error enviando código de recuperación: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    // Metodo para obtener la URL base del sistema
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . "://" . $host;
    }

    // Metodo para verificar la conexión SMTP
    public function verificarConexion() {
        try {
            $this->mailer->smtpConnect();
            $this->mailer->smtpClose();
            return true;
        } catch (Exception $e) {
            error_log("Error verificando conexión SMTP: " . $e->getMessage());
            return false;
        }
    }
}
?>