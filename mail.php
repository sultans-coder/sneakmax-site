<?php
require_once 'PHPMailer/PHPMailerAutoload.php';

$admin_email = array();
foreach ($_POST["admin_email"] as $key => $value) {
    array_push($admin_email, $value);
}

$form_subject = trim($_POST["form_subject"]);

$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';

$jsonText = $_POST['Товары'];
$myArray = json_decode($jsonText, true);

$prod = '';

foreach ($myArray as $key => $value) {
    $cat = $value["category"];
    $title = $value["title"];
    $price = $value["price"];
    $prod .= "
        <tr>
            <td style='padding: 10px; border: #e9e9e9 1px solid;'>$title</td>
            <td style='padding: 10px; border: #e9e9e9 1px solid;'>$price</td>
        </tr>
    ";
}

$c = true;
$message = '';
foreach ($_POST as $key => $value) {
    if ($value != "" && $key != "admin_email" && $key != "form_subject" && $key != "Товары") {
        if (is_array($value)) {
            $val_text = '';
            foreach ($value as $val) {
                if ($val && $val != '') {
                    $val_text .= ($val_text == '' ? '' : ', ') . $val;
                }
            }
            $value = $val_text;
        }
        $message .= "
            " . (($c = !$c) ? '<tr>' : '<tr>') . "
            <td style='padding: 10px; width: auto;'><b>$key:</b></td>
            <td style='padding: 10px;width: 100%;'>$value</td>
            </tr>
        ";
    }
}
$message = "<table style='width: 50%;'>$message . $prod</table>";

$mail->isSMTP(); // Раскомментируйте эту строку для использования SMTP
$mail->Host = 'smtp.gmail.com'; // SMTP-сервер Gmail
$mail->Port = 587; // Порт SMTP Gmail (587 для TLS)
$mail->SMTPAuth = true; // Аутентификация SMTP
$mail->Username = 'sultanshaiahmet05@gmail.com'; // Ваш адрес Gmail
$mail->Password = '15A04p2005'; // Пароль от вашего аккаунта Gmail
$mail->SMTPSecure = 'tls'; // Используем TLS шифрование

$mail->setFrom('adm@' . $_SERVER['HTTP_HOST'], 'Your best site');

foreach ($admin_email as $key => $value) {
    $mail->addAddress($value);
}
$mail->Subject = $form_subject;
$body = $message;
$mail->msgHTML($body);

if ($_FILES) {
    foreach ($_FILES['file']['tmp_name'] as $key => $value) {
        $mail->addAttachment($value, $_FILES['file']['name'][$key]);
    }
}
$mail->send();

// Уведомление владельцу
$owner_email = 'sultanshaiahmet05@gmail.com'; // Замените на реальный адрес владельца
$owner_message = "У вас новая покупка на сайте.";
$mail->clearAddresses();
$mail->addAddress($owner_email);
$mail->Subject = "Уведомление о новой покупке";
$mail->msgHTML($owner_message);
$mail->send();
?>
