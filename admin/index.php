<?php
include('menu.php');
include('../ketnoi.php');
// điều hướng nội dung
$page = $_GET['page'] ?? 'home';

switch($page){
    case 'rooms':
        include('rooms/list.php');
        break;

    // case 'rooms_add':
    //     include('rooms/add.php');
    //     break;

    // case 'rooms_edit':
    //     include('rooms/edit.php');
    //     break;

    case 'users':
        include('users/list.php');
        break;

    case 'users_add':
        include('users/add.php');
        break;

    case 'users_edit':
        include('users/edit.php');
        break;

    case 'report':
        include('thongke.php');
        break;


        break;
}

?>