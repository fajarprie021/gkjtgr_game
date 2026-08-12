<?php
require_once '../../config/auth.php';
if (isset($_SESSION['staff_id']) && ($_SESSION['staff_role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;