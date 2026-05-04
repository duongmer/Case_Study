<?php
$where = "WHERE 1";

if(!empty($_GET['min_price'])){
    $where .= " AND price >= ".$_GET['min_price'];
}

if(!empty($_GET['max_price'])){
    $where .= " AND price <= ".$_GET['max_price'];
}

if(!empty($_GET['from'])){
    $where .= " AND created_at >= '".$_GET['from']."'";
}

if(!empty($_GET['to'])){
    $where .= " AND created_at <= '".$_GET['to']."'";
}

$result = $conn->query("SELECT * FROM motel $where");
?>

<h3>Thống kê</h3>

<form>
Giá từ: <input name="min_price">
Đến: <input name="max_price">
Từ ngày: <input type="date" name="from">
Đến: <input type="date" name="to">
<button>Lọc</button>
</form>

<table border="1">
<tr>
<th>ID</th>
<th>Tiêu đề</th>
<th>Giá</th>
<th>Ngày</th>
</tr>

<?php while($r = $result->fetch_assoc()): ?>
<tr>
<td><?= $r['ID'] ?></td>
<td><?= $r['title'] ?></td>
<td><?= $r['price'] ?></td>
<td><?= $r['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<hr>

<?php
$stat = $conn->query("
    SELECT MONTH(created_at) thang, COUNT(*) total 
    FROM motel GROUP BY MONTH(created_at)
");
?>

<h4>Số tin theo tháng</h4>

<table border="1">
<tr><th>Tháng</th><th>Số tin</th></tr>

<?php while($s = $stat->fetch_assoc()): ?>
<tr>
<td><?= $s['thang'] ?></td>
<td><?= $s['total'] ?></td>
</tr>
<?php endwhile; ?>
</table>