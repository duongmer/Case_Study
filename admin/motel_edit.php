<?php
require_once 'config.php';
checkAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id          = (int)$_POST['id'];
    $title       = $conn->real_escape_string(trim($_POST['title']));
    $price       = (int)$_POST['price'];
    $area        = (int)$_POST['area'];
    $address     = $conn->real_escape_string(trim($_POST['address']));
    $phone       = $conn->real_escape_string(trim($_POST['phone']));
    $district_id = (int)$_POST['district_id'];
    $approve     = (int)$_POST['approve'];
    $description = $conn->real_escape_string(trim($_POST['description']));

    $conn->query("UPDATE motel SET 
        title='$title', price=$price, area=$area, address='$address',
        phone='$phone', district_id=$district_id, approve=$approve,
        description='$description'
        WHERE ID=$id");
}
header('Location: motel.php?ok=saved');
exit;
?>
