<?php
if ($_SESSION['usuario']['rol'] != 'empleado') {
    header("Location: ../index.php");
    exit();
}
?>