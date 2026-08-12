<?php
require_once '../../config/auth.php';
staffLogout();
session_destroy();
header('Location: login.php');
exit;