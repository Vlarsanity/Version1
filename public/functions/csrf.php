<?php
session_start();

// Generate token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to get the token
function getCsrfToken() {
    return $_SESSION['csrf_token'];
}