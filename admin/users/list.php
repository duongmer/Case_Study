<?php
$result = $conn->query("SELECT * FROM user");
?>

<h3>Danh sách người dùng</h3>

<a href="index.php?page=users_add">+ Thêm</a>

<table border="1">
<tr>
<th>ID</th>
<th>Name</th>
<th>Username</th>
<th>Email</th>
<th>Phone</th>
<th>Hành động</th>
</tr>

<?php while($u = $result->fetch_assoc()): ?>
<tr>
<td><?= $u['ID'] ?></td>
<td><?= $u['Name'] ?></td>
<td><?= $u['Username'] ?></td>
<td><?= $u['Email'] ?></td>
<td><?= $u['Phone'] ?></td>

<td>
<a href="index.php?page=users_edit&id=<?= $u['ID'] ?>">Sửa</a> |
<a href="users/delete.php?id=<?= $u['ID'] ?>">Xóa</a>
</td>
</tr>
<?php endwhile; ?>
</table>