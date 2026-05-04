<?php
$result = $conn->query("
    SELECT motel.*, user.Name as username 
    FROM motel 
    LEFT JOIN user ON motel.user_id = user.ID
");
?>

<h3>Danh sách phòng</h3>

<a href="index.php?page=rooms_add">+ Thêm</a>

<table border="1">
<tr>
<th>ID</th>
<th>Tiêu đề</th>
<th>Giá</th>
<th>Địa chỉ</th>
<th>Người đăng</th>
<th>Trạng thái</th>
<th>Hành động</th>
</tr>

<?php while($r = $result->fetch_assoc()): ?>
<tr>
<td><?= $r['ID'] ?></td>
<td><?= $r['title'] ?></td>
<td><?= number_format($r['price']) ?></td>
<td><?= $r['address'] ?></td>
<td><?= $r['username'] ?></td>

<td><?= $r['approve'] ? 'Đã duyệt' : 'Chưa duyệt' ?></td>

<td>
<a href="index.php?page=rooms_edit&id=<?= $r['ID'] ?>">Sửa</a> |
<a href="rooms/delete.php?id=<?= $r['ID'] ?>">Xóa</a> |

<?php if($r['approve']==0): ?>
<a href="rooms/approve.php?id=<?= $r['ID'] ?>">Duyệt</a>
<?php else: ?>
<a href="rooms/hide.php?id=<?= $r['ID'] ?>">Ẩn</a>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</table>