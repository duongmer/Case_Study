  <div class="text-center" style="font-size:13px; padding: 20px 0;">
  </div>
</main>

<!-- JS -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="https://unpkg.com/boxicons@latest/dist/boxicons.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="js/plugins/pace.min.js"></script>
<?php if (!empty($extraScript)) echo $extraScript; ?>

<script>
function time() {
  var today = new Date();
  var weekday = ["Chủ Nhật","Thứ Hai","Thứ Ba","Thứ Tư","Thứ Năm","Thứ Sáu","Thứ Bảy"];
  var day = weekday[today.getDay()];
  var dd = String(today.getDate()).padStart(2,'0');
  var mm = String(today.getMonth()+1).padStart(2,'0');
  var yyyy = today.getFullYear();
  var h = String(today.getHours()).padStart(2,'0');
  var m = String(today.getMinutes()).padStart(2,'0');
  var s = String(today.getSeconds()).padStart(2,'0');
  var clock = document.getElementById("clock");
  if (clock) {
    clock.innerHTML = '<span class="date">' + day + ', ' + dd + '/' + mm + '/' + yyyy + ' - ' + h + ' giờ ' + m + ' phút ' + s + ' giây</span>';
  }
  setTimeout(time, 1000);
}
time();
</script>
</body>
</html>
