<?php
include("../connect.php");
  $sql = "SELECT * FROM user";
  $result = $conn->query($sql);
?>

<body onload="time()" class="app sidebar-mini rtl">
  <!-- Navbar-->
  <header class="app-header">
    <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar"
      aria-label="Hide Sidebar"></a>
    <!-- Navbar Right Menu-->
    <ul class="app-nav">
      <!-- User Menu-->
      <li><a class="app-nav__item" href="/index.html"><i class='bx bx-log-out bx-rotate-180'></i> </a>
      </li>
    </ul>
  </header>
  <!-- Sidebar menu-->
    <?php
        include('../menu.php');
    ?>
  <main class="app-content">
    <div class="app-title">
      <ul class="app-breadcrumb breadcrumb side">
        <li class="breadcrumb-item active"><a href="#"><b>Danh sách người dùng</b></a></li>
      </ul>
      <div id="clock"></div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="tile">
          <div class="tile-body">

            <div class="row element-button">
              <div class="col-sm-2">
                <a class="btn btn-add btn-sm" href="add.php" title="Thêm"><i class="fas fa-plus"></i>
                  Tạo mới người dùng</a>
              </div>
            </div>
            <?php if($result->num_rows > 0){ ?>
            <table class="table table-hover table-bordered js-copytextarea" cellpadding="0" cellspacing="0" border="0"
              id="sampleTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th width="150">Họ và tên</th>
                  <th width="20">Username</th>
                  <th width="300">Email</th>
                  <th>SĐT</th>
                  <th>Password</th>
                  <th>Ảnh</th>
                  <th>Phân loại</th>
                  <th width="100">Tính năng</th>
                </tr>
              </thead>
              <tbody>
                <?php  foreach($result as $row) {?>
                <tr>
                  <td><?php echo $row['ID']; ?></td>
                  <td><?php echo $row['Name']; ?></td>
                  <td><?php echo $row['Username']; ?></td>
                  <td><?php echo $row['Email']; ?></td>
                  <td><?php echo $row['Phone']; ?></td>
                  <td><?php echo $row['Password']; ?></td>
                  <td><img src="../../images/<?php echo $row['Avatar']; ?>" width="70"></td>
                  <td><?php echo $row['Role']; ?></td>
                  <td class="table-td-center"><button class="btn btn-primary btn-sm trash" type="button" title="Xóa"
                      onclick="myFunction(this)"><i class="fas fa-trash-alt"></i>
                    </button>
                    <button class="btn btn-primary btn-sm edit" type="button" title="Sửa" id="show-emp"
                      data-toggle="modal" data-target="#ModalUP"><i class="fas fa-edit"></i>
                    </button>
                  </td>
                </tr>
                <?php }?>
              </tbody>
            </table>
            <?php }?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!--
  MODAL
-->
  <div class="modal fade" id="ModalUP" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
    data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-body">
          <div class="row">
            <div class="form-group  col-md-12">
              <span class="thong-tin-thanh-toan">
                <h5>Chỉnh sửa thông tin nhân viên cơ bản</h5>
              </span>
            </div>
          </div>
          <table class="table table-hover table-bordered js-copytextarea" cellpadding="0" cellspacing="0" border="0"
    id="sampleTable">

            <thead>
                <tr>
                    <th width="10"><input type="checkbox" id="all"></th>
                    <th>ID</th>
                    <th>Họ và tên</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Avatar</th>
                    <th width="100">Tính năng</th>
                </tr>
            </thead>

            <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><input type="checkbox"></td>

            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>

            <td>
                <img src="../../images/<?php echo $row['avatar']; ?>" width="70">
            </td>

            <td><?php echo $row['address']; ?></td>
            <td><?php echo $row['birthday']; ?></td>
            <td><?php echo $row['gender']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['role']; ?></td>

            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>">
                    <button>Sửa</button>
                </a>

                <a href="delete.php?id=<?php echo $row['id']; ?>">
                    <button>Xóa</button>
                </a>
            </td>
        </tr>
        <?php } ?>
        </tbody>

</table>

          <BR>
          <a href="#" style="    float: right;
        font-weight: 600;
        color: #ea0000;">Chỉnh sửa nâng cao</a>
          <BR>
          <BR>
          <button class="btn btn-save" type="button">Lưu lại</button>
          <a class="btn btn-cancel" data-dismiss="modal" href="#">Hủy bỏ</a>
          <BR>
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>
  <!--
  MODAL
-->

  <!-- Essential javascripts for application to work-->
  <script src="js/jquery-3.2.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="src/jquery.table2excel.js"></script>
  <script src="js/main.js"></script>
  <!-- The javascript plugin to display page loading on top-->
  <script src="js/plugins/pace.min.js"></script>
  <!-- Page specific javascripts-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
  <!-- Data table plugin-->
  <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
  <script type="text/javascript">$('#sampleTable').DataTable();</script>
  <script>
    function deleteRow(r) {
      var i = r.parentNode.parentNode.rowIndex;
      document.getElementById("myTable").deleteRow(i);
    }
    jQuery(function () {
      jQuery(".trash").click(function () {
        swal({
          title: "Cảnh báo",
         
          text: "Bạn có chắc chắn là muốn xóa nhân viên này?",
          buttons: ["Hủy bỏ", "Đồng ý"],
        })
          .then((willDelete) => {
            if (willDelete) {
              swal("Đã xóa thành công.!", {
                
              });
            }
          });
      });
    });
    oTable = $('#sampleTable').dataTable();
    $('#all').click(function (e) {
      $('#sampleTable tbody :checkbox').prop('checked', $(this).is(':checked'));
      e.stopImmediatePropagation();
    });
    //Thời Gian
    function time() {
      var today = new Date();
      var weekday = new Array(7);
      weekday[0] = "Chủ Nhật";
      weekday[1] = "Thứ Hai";
      weekday[2] = "Thứ Ba";
      weekday[3] = "Thứ Tư";
      weekday[4] = "Thứ Năm";
      weekday[5] = "Thứ Sáu";
      weekday[6] = "Thứ Bảy";
      var day = weekday[today.getDay()];
      var dd = today.getDate();
      var mm = today.getMonth() + 1;
      var yyyy = today.getFullYear();
      var h = today.getHours();
      var m = today.getMinutes();
      var s = today.getSeconds();
      m = checkTime(m);
      s = checkTime(s);
      nowTime = h + " giờ " + m + " phút " + s + " giây";
      if (dd < 10) {
        dd = '0' + dd
      }
      if (mm < 10) {
        mm = '0' + mm
      }
      today = day + ', ' + dd + '/' + mm + '/' + yyyy;
      tmp = '<span class="date"> ' + today + ' - ' + nowTime +
        '</span>';
      document.getElementById("clock").innerHTML = tmp;
      clocktime = setTimeout("time()", "1000", "Javascript");

      function checkTime(i) {
        if (i < 10) {
          i = "0" + i;
        }
        return i;
      }
    }
    //In dữ liệu
    var myApp = new function () {
      this.printTable = function () {
        var tab = document.getElementById('sampleTable');
        var win = window.open('', '', 'height=700,width=700');
        win.document.write(tab.outerHTML);
        win.document.close();
        win.print();
      }
    }
    //     //Sao chép dữ liệu
    //     var copyTextareaBtn = document.querySelector('.js-textareacopybtn');

    // copyTextareaBtn.addEventListener('click', function(event) {
    //   var copyTextarea = document.querySelector('.js-copytextarea');
    //   copyTextarea.focus();
    //   copyTextarea.select();

    //   try {
    //     var successful = document.execCommand('copy');
    //     var msg = successful ? 'successful' : 'unsuccessful';
    //     console.log('Copying text command was ' + msg);
    //   } catch (err) {
    //     console.log('Oops, unable to copy');
    //   }
    // });


    //Modal
    $("#show-emp").on("click", function () {
      $("#ModalUP").modal({ backdrop: false, keyboard: false })
    });
  </script>
</body>

</html>