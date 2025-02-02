<?php
session_start();
session_unset();
session_destroy();
$this->pages->render('Pagina/index');
exit;
?>

