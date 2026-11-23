<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

logout_utente();
header('Location: index.php');
exit;
