<?php
session_start();
session_unset(); // Vide toutes les variables de session (ex: $_SESSION['admin'])
session_destroy();
header("Location: ../index_.php");
exit();