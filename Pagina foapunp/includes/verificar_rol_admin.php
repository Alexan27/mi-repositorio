<?php
if ($_SESSION['usuario']['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>