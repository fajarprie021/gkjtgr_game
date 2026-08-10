<?php
require_once '../../config/auth.php';

staffLogout();
header('Location: login.php');
exit;