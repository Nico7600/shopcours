
<?php
require_once('bootstrap.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['field1']) || empty($_POST['field2']) || empty($_POST['field3'])) {
        $_SESSION['message'] = 'Le formulaire est incomplet';
        $_SESSION['message_type'] = 'error';
        header('Location: form_page.php');
        exit();
    }



    $_SESSION['message'] = 'Formulaire soumis avec succès';
    $_SESSION['message_type'] = 'success';
    header('Location: form_page.php');
    exit();
}
?>