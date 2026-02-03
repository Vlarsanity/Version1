<?php
session_start();

// If theme is submitted via GET, save it
if (isset($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'];
}

// Determine current theme
$themeClass = (isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark') ? 'dark' : '';
?>