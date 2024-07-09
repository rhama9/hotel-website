<?php

if (!isset($_SESSION)) {
    // Démarre la session
    session_start();
}

unset($_SESSION);
session_destroy();
header('location: index.php');
