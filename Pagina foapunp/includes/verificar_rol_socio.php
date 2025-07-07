<?php
if ($_SESSION['usuario']['rol'] != 'socio') {
    header("Location: ../index.php");
    exit();
}
?>