<?php
require_once 'config.php';
checkAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id       = (int)$_POST['id'];
    $newpw    = trim($_POST['newpw']);
    $confirmpw= trim($_POST['confirmpw']);

    if ($newpw === $confirmpw && strlen($newpw) >= 4) {
        $pw = $conn->real_escape_string($newpw);
        $conn->query("UPDATE user SET Password='$pw' WHERE ID=$id");
    }
}
header('Location: user.php?ok=saved');
exit;
?>
