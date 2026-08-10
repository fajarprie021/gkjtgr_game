<?php
require_once '../../config/auth.php';

playerLogout();
header('Location: login.php');
exit;