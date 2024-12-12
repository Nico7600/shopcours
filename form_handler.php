
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifiez que tous les champs obligatoires sont remplis
    if (empty($_POST['field1']) || empty($_POST['field2']) || empty($_POST['field3'])) {
        $_SESSION['message'] = 'Le formulaire est incomplet';
        $_SESSION['message_type'] = 'error';
        header('Location: form_page.php');
        exit();
    }

    // Traitez le formulaire ici
    // ...

    $_SESSION['message'] = 'Formulaire soumis avec succès';
    $_SESSION['message_type'] = 'success';
    header('Location: form_page.php');
    exit();
}
?>