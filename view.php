<?php
require_once("../../../config/database.php");
require_once("model.php");
if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) )
{
session_start(); 
if ( !isset($_SESSION['session_username']) or !isset($_SESSION['session_id']) or !isset($_SESSION['session_level']) or !isset($_SESSION['session_kode_akses']) or !isset($_SESSION['session_hak_akses']) )
{
  echo '<div class="callout callout-danger">
          <h4>Session Berakhir!!!</h4>
          <p>Silahkan logout dan login kembali. Terimakasih.</p>
        </div>';
} else {
$db = new db();
$view=$_POST['view'];
$username = $_SESSION['session_username'];
$id_menus = 6; 
$cekMenusUser = $db->cekMenusUser($username,$id_menus); 
    foreach($cekMenusUser[1] as $data){
      $create = $data['c'];
      $read = $data['r'];
      $update = $data['u'];
      $delete = $data['d'];
      $nama_menus = $data['nama_menus'];
      $keterangan = $data['keterangan'];
    }
if($cekMenusUser[2] == 1) {
?>

<?php 
if($view == 'table'){
    $kriteria = htmlspecialchars($_POST['kriteria']);
    $getTable = $db->getTable($kriteria); 
?>
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Data Menus</h3>
  </div>
  <!-- /.box-header -->
  <div class="box-body table-responsive">
    <table class="table table-bordered table-striped text-nowrap">
      <thead>
      <tr>
        <th style="text-align:center;">#</th>
        <th style="text-align:center;">Aksi</th>
        <th style="text-align:center;">ID</th>
        <th style="text-align:center;">Menu</th>
        <th style="text-align:center;">Menu Sub</th>
        <th style="text-align:center;">Menus</th>
        <th style="text-align:center;">Urutan</th>
        <th style="text-align:center;">Icon</th>
        <th style="text-align:center;">Folder</th>
        <th style="text-align:center;">Keterangan</th>
        <th style="text-align:center;">Status</th>
      </tr>
      </thead>
      <tbody>
      <?php
      $no = 1;
      foreach($getTable[1] as $row){
      ?>
      <tr>
        <td style="text-align:center;"><?php echo $no++; ?></td>
        <td style="text-align:center;">
          <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">
              <span class="fa fa-fw fa-cogs"></span>
              <span class="sr-only">Toggle Dropdown</span>
            </button>
              <ul class="dropdown-menu" role="menu">
              <?php if($create == "y") {?>
              <li><a href="javascript:void(0)" class="read" id="<?php echo $row['id_menus'];?>"><i class="fa fa-fw fa-eye"></i>Detail</a></li>
              <?php } ?>
              <?php if($update == "y") {?>
              <li><a href="javascript:void(0)" class="update" id="<?php echo $row['id_menus'];?>"><i class="fa fa-fw fa-edit"></i>Edit</a></li>
              <?php } ?>
              <?php if($delete == "y") {?>
              <li><a href="javascript:void(0)" class="delete text-red" id="<?php echo $row['id_menus'];?>"><i class="fa fa-fw fa-trash-o"></i>Hapus</a></li>
              <?php } ?>
              </ul>
          </div>
        </td>
        <td style="text-align:center;"><?php echo $row['id_menus'];?></td>
        <td style="text-align:center;">
          <?php 
            $id = $row['id_menu'];
            $getMenuById = $db->getMenuById($id); 
            foreach($getMenuById[1] as $ref){
              echo $ref['nama_menu'];
            }
          ?>
        </td>
        <td style="text-align:center;">
          <?php 
            $id = $row['id_menu_sub'];
            $getMenuSubById = $db->getMenuSubById($id); 
            foreach($getMenuSubById[1] as $ref){
              echo $ref['nama_menu_sub'];
            }
          ?>
        </td>
        <td><?php echo $row['nama_menus'];?></td>
        <td style="text-align:center;"><?php echo $row['urut_menus'];?></td>
        <td style="text-align:center;"><i class="<?php echo $row['icon_menus'];?>"></i></td>
        <td style="text-align:center;"><?php echo $row['folder'];?></td>
        <td style="text-align:left;"><?php echo $row['keterangan'];?></td>
        <td style="text-align:center;">
          <?php 
            $kode = "status_user";
            $item = $row['status'];
            $getReferensi = $db->getReferensi($kode,$item); 
            foreach($getReferensi[1] as $ref){
              echo $ref['html'];
            }
          ?>
        </td>
      </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->
<script>
  // Form edit
  function formUpdate(id) {
    let value = {
      view : 'form_update',
      id : id,
    }
    $.ajax({
      url:"menus/<?php echo $id_menus;?>/view.php",
      type: "POST",
      data: value,
      success: function(data, textStatus, jqXHR)
      { 
        Swal.close()
        $('#pages').html(data);
      },
      error: function (request, textStatus, errorThrown) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: textStatus,
          didOpen: () => {
            Swal.hideLoading()
          }
        });
      }
    }); 
  }

  $(document).off('click', '.update').on('click', '.update', function(){
    Swal.fire({
      title: 'Loading...',
      html: 'Mohon menunggu sebentar...',
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      }
    });
    formUpdate($(this).attr('id'));
  });

  $(document).off('click', '.delete').on('click', '.delete', function(){
    let id = $(this).attr('id');
    Swal.fire({
      title: 'Hapus Data?',
      text: "Data akan dihapus selamanya!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Loading...',
          html: 'Mohon menunggu sebentar...',
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });
        let value = {
          controller : 'delete',
          id : id,
        }
        $.ajax({
          url:"menus/<?php echo $id_menus;?>/controller.php",
          type: "POST",
          data: value,
          success: function(data, textStatus, jqXHR)
          { 
            loadTable();
            $resp = JSON.parse(data);
            if($resp['status'] == true){
              toastr.success($resp['message'], $resp['title'], {timeOut: 2000, progressBar: true});
            } else {
              toastr.error($resp['message'], $resp['title'], {closeButton: true});
            }
          },
          error: function (request, textStatus, errorThrown) {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: textStatus,
              didOpen: () => {
                Swal.hideLoading()
              }
            });
          }
        }); 
      }
    })
  });

  // Form read
  function formRead(id) {
    let value = {
      view : 'form_read',
      id : id,
    }
    $.ajax({
      url:"menus/<?php echo $id_menus;?>/view.php",
      type: "POST",
      data: value,
      success: function(data, textStatus, jqXHR)
      { 
        Swal.close()
        $('#pages').html(data);
      },
      error: function (request, textStatus, errorThrown) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: textStatus,
          didOpen: () => {
            Swal.hideLoading()
          }
        });
      }
    }); 
  }

  $(document).off('click', '.read').on('click', '.read', function(){
    Swal.fire({
      title: 'Loading...',
      html: 'Mohon menunggu sebentar...',
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      }
    });
    formRead($(this).attr('id'));
  });
</script>
<?php }?>


<?php 
if($view == 'form_create' && $create == "y"){
?>
<div class="box box-success box-solid">
  <div class="box-header">
    <h3 class="box-title">Tambah Menus</h3>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <form class="form-horizontal" id="input_form_create">
      <div class="box-body">
        <div class="form-group">
          <label for="nama_menus" class="col-sm-2 control-label">Menus</label>
          <div class="col-sm-10">
            <input type="hidden" class="form-control" name="controller" value="create">
            <input type="text" class="form-control" id="nama_menus" name="nama_menus" placeholder="Menus...">
          </div>
        </div>
        <div class="form-group">
          <label for="urut_menus" class="col-sm-2 control-label">Urutan</label>
          <div class="col-sm-10">
            <input type="number" class="form-control" id="urut_menus" name="urut_menus" placeholder="Urutan...">
          </div>
        </div>
        <div class="form-group">
          <label for="icon_menus" class="col-sm-2 control-label">Icon</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="icon_menus" name="icon_menus" placeholder="Icon...">
          </div>
        </div>
        <div class="form-group">
          <label for="folder" class="col-sm-2 control-label">Folder</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="folder" name="folder" placeholder="Folder...">
          </div>
        </div>
        <div class="form-group">
          <label for="keterangan" class="col-sm-2 control-label">Keterangan</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" style="resize:vertical;" placeholder="Keterangan..."></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="status" class="col-sm-2 control-label">Status</label>
          <div class="col-sm-10">
            <select class="form-control select22" id="status" name="status" style="width: 100%;">
              <option value="">-- Pilih --</option>
              <?php 
                $kode = "status_referensi";
                $getReferensiByKode = $db->getReferensiByKode($kode); 
                foreach($getReferensiByKode[1] as $ref){
              ?>
              <option value="<?php echo $ref['item'];?>"><?php echo $ref['keterangan'];?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <br>
        <div class="form-group">
          <label for="id_menu" class="col-sm-2 control-label">Menu</label>
          <div class="col-sm-10">
            <select class="form-control select22" id="id_menu" name="id_menu" style="width: 100%;">
              <option value="">-- Pilih --</option>
              <?php 
                $getMenu = $db->getMenu(); 
                foreach($getMenu[1] as $ref){
              ?>
              <option value="<?php echo $ref['id_menu'];?>"><?php echo $ref['nama_menu'];?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="id_menu_sub" class="col-sm-2 control-label">Menu Sub</label>
          <div class="col-sm-10">
            <select class="form-control select22" id="id_menu_sub" name="id_menu_sub" style="width: 100%;">
              <option value="">-- Pilih --</option>
            </select>
          </div>
        </div>
      </div>

      <div class="box-footer">
        <button type="button" class="btn btn-success pull-right" id="btn-save">Simpan</button>
      </div>

    </form>
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->
<script>
  function getMenuSub(id_menu) {
    let value = {
      controller : 'get_menu_sub',
      id_menu : id_menu,
    }
    $.ajax({
      url:"menus/<?php echo $id_menus;?>/controller.php",
      type: "POST",
      data: value,
      success: function(data, textStatus, jqXHR)
      { 
        $('#id_menu_sub').html(data);
      },
      error: function (request, textStatus, errorThrown) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: textStatus,
          didOpen: () => {
            Swal.hideLoading()
          }
        });
      }
    }); 
  }

  $("#id_menu").change(function(){
    getMenuSub($('#id_menu').val());
  });

  $('#btn-save').click(function() {
    if($('#nama_menus').val() == ''){
      $('#nama_menus').focus();
      Swal.fire("Validasi!", "Menus wajib diisi.");
      return;
    }
    if($('#urut_menus').val() == ''){
      $('#urut_menus').focus();
      Swal.fire("Validasi!", "Urutan wajib diisi.");
      return;
    }
    if($('#icon_menus').val() == ''){
      $('#icon_menus').focus();
      Swal.fire("Validasi!", "Icon wajib diisi.");
      return;
    }
    if($('#folder').val() == ''){
      $('#folder').focus();
      Swal.fire("Validasi!", "Folder wajib diisi.");
      return;
    }
    if($('#keterangan').val() == ''){
      $('#keterangan').focus();
      Swal.fire("Validasi!", "Keterangan wajib diisi.");
      return;
    }
    if($('#status').val() == ''){
      $('#status').focus();
      Swal.fire("Validasi!", "Status wajib diisi.");
      return;
    }
    Swal.fire({
      title: 'Tambah Data?',
      text: "Data akan ditambahkan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Tambah',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Loading...',
          html: 'Mohon menunggu sebentar...',
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
            var val = $('#input_form_create').serialize();
            $.ajax({
              url:"menus/<?php echo $id_menus;?>/controller.php",
              type: "POST",
              data: val,
              success: function(data, textStatus, jqXHR)
              { 
                loadTable();
                $resp = JSON.parse(data);
                if($resp['status'] == true){
                  toastr.success($resp['message'], $resp['title'], {timeOut: 2000, progressBar: true});
                } else {
                  toastr.error($resp['message'], $resp['title'], {closeButton: true});
                }
              },
              error: function (request, textStatus, errorThrown) {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: textStatus,
                  didOpen: () => {
                    Swal.hideLoading()
                  }
                });
              }
            });
          }
        });
      }
    })
  });
</script>
<?php }?>

<?php 
if($view == 'form_update' && $update == "y"){
  $id = $_POST['id'];
  $getDataById = $db->getDataById($id); 
  foreach($getDataById[1] as $data){
?>
<div class="box box-warning box-solid">
  <div class="box-header">
    <h3 class="box-title">Edit Menus</h3>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <form class="form-horizontal" id="input_form_update">
      <div class="box-body">
        <div class="form-group">
          <label for="nama_menus" class="col-sm-2 control-label">Menus</label>
          <div class="col-sm-10">
            <input type="hidden" class="form-control" name="controller" value="update">
            <input type="hidden" class="form-control" name="id_menus" value="<?php echo $id;?>">
            <input type="text" class="form-control" id="nama_menus" name="nama_menus" placeholder="Menus..." value="<?php echo $data['nama_menus'];?>">
          </div>
        </div>
        <div class="form-group">
          <label for="urut_menus" class="col-sm-2 control-label">Urutan</label>
          <div class="col-sm-10">
            <input type="number" class="form-control" id="urut_menus" name="urut_menus" placeholder="Urutan..." value="<?php echo $data['urut_menus'];?>">
          </div>
        </div>
        <div class="form-group">
          <label for="icon_menus" class="col-sm-2 control-label">Icon</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="icon_menus" name="icon_menus" placeholder="Icon..." value="<?php echo $data['icon_menus'];?>">
          </div>
        </div>
        <div class="form-group">
          <label for="folder" class="col-sm-2 control-label">Folder</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="folder" name="folder" placeholder="Folder..." value="<?php echo $data['folder'];?>">
          </div>
        </div>
        <div class="form-group">
          <label for="keterangan" class="col-sm-2 control-label">Keterangan</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" style="resize:vertical;" placeholder="Keterangan..."><?php echo $data['keterangan'];?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="status" class="col-sm-2 control-label">Status</label>
          <div class="col-sm-10">
            <select class="form-control select22" id="status" name="status" style="width: 100%;">
              <?php 
                $kode = "status_referensi";
                $item = $data['status'];
                $getReferensi = $db->getReferensi($kode,$item); 
                foreach($getReferensi[1] as $row){
              ?>
              <option value="<?php echo $row['item'];?>"><?php echo $row['keterangan'];?></option>
              <?php } ?>
              <option value="">-- Pilih --</option>
              <?php 
                $kode = "status_referensi";
                $getReferensiByKode = $db->getReferensiByKode($kode); 
                foreach($getReferensiByKode[1] as $ref){
              ?>
              <option value="<?php echo $ref['item'];?>"><?php echo $ref['keterangan'];?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <br>
        <div class="form-group">
          <label for="id_menu" class="col-sm-2 control-label">Menu</label>
          <div class="col-sm-10">
            <select class="form-control select22" id="id_menu" name="id_menu" style="width: 100%;">
              <?php 
                $id = $data['id_menu'];
                $getMenuById = $db->getMenuById($id); 
                foreach($getMenuById[1] as $row){
              ?>
              <option value="<?php echo $row['id_menu'];?>"><?php echo $row['nama_menu'];?></option>
              <?php } ?>
              <option value="">-- Pilih --</option>
              <?php 
                $getMenu = $db->getMenu(); 
                foreach($getMenu[1] as $ref){
              ?>
              <option value="<?php echo $ref['id_menu'];?>"><?php echo $ref['nama_menu'];?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="id_menu_sub" class="col-sm-2 control-label">Menu Sub</label>
          <div class="col-sm-10">
            <select class="form-control select22" id="id_menu_sub" name="id_menu_sub" style="width: 100%;">
              <?php 
                $id = $data['id_menu_sub'];
                $getMenuSubById = $db->getMenuSubById($id); 
                foreach($getMenuSubById[1] as $row){
              ?>
              <option value="<?php echo $row['id_menu_sub'];?>"><?php echo $row['nama_menu_sub'];?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>

      <div class="box-footer">
        <button type="button" class="btn btn-warning pull-right" id="btn-save">Simpan</button>
      </div>

    </form>
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->
<script>
  function getMenuSub(id_menu) {
    let value = {
      controller : 'get_menu_sub',
      id_menu : id_menu,
    }
    $.ajax({
      url:"menus/<?php echo $id_menus;?>/controller.php",
      type: "POST",
      data: value,
      success: function(data, textStatus, jqXHR)
      { 
        $('#id_menu_sub').html(data);
      },
      error: function (request, textStatus, errorThrown) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: textStatus,
          didOpen: () => {
            Swal.hideLoading()
          }
        });
      }
    }); 
  }

  $("#id_menu").change(function(){
    getMenuSub($('#id_menu').val());
  });

  $(document).ready(function(){
    if($('#id_menu_sub').val() == null){
      getMenuSub($('#id_menu').val());
    }
  });

  $('#btn-save').click(function() {
    if($('#nama_menus').val() == ''){
      $('#nama_menus').focus();
      Swal.fire("Validasi!", "Menus wajib diisi.");
      return;
    }
    if($('#urut_menus').val() == ''){
      $('#urut_menus').focus();
      Swal.fire("Validasi!", "Urutan wajib diisi.");
      return;
    }
    if($('#icon_menus').val() == ''){
      $('#icon_menus').focus();
      Swal.fire("Validasi!", "Icon wajib diisi.");
      return;
    }
    if($('#folder').val() == ''){
      $('#folder').focus();
      Swal.fire("Validasi!", "Folder wajib diisi.");
      return;
    }
    if($('#keterangan').val() == ''){
      $('#keterangan').focus();
      Swal.fire("Validasi!", "Keterangan wajib diisi.");
      return;
    }
    if($('#status').val() == ''){
      $('#status').focus();
      Swal.fire("Validasi!", "Status wajib diisi.");
      return;
    }
    Swal.fire({
      title: 'Edit Data?',
      text: "Data akan dirubah!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Edit',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Loading...',
          html: 'Mohon menunggu sebentar...',
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
            var val = $('#input_form_update').serialize();
            $.ajax({
              url:"menus/<?php echo $id_menus;?>/controller.php",
              type: "POST",
              data: val,
              success: function(data, textStatus, jqXHR)
              { 
                loadTable();
                $resp = JSON.parse(data);
                if($resp['status'] == true){
                  toastr.success($resp['message'], $resp['title'], {timeOut: 2000, progressBar: true});
                } else {
                  toastr.error($resp['message'], $resp['title'], {closeButton: true});
                }
              },
              error: function (request, textStatus, errorThrown) {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: textStatus,
                  didOpen: () => {
                    Swal.hideLoading()
                  }
                });
              }
            });
          }
        });
      }
    })
  });
</script>
<?php }}?>

<?php 
if($view == 'form_read' && $read == "y"){
  $id = $_POST['id'];
  $getDataById = $db->getDataById($id); 
  foreach($getDataById[1] as $data){
?>
<div class="box box-info box-solid">
  <div class="box-header">
    <h3 class="box-title">Detail Menus</h3>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <form class="form-horizontal">
      <div class="box-body">
        <div class="form-group">
          <label for="nama_menus" class="col-sm-2 control-label">Menus</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="nama_menus" name="nama_menus" placeholder="Menus..." value="<?php echo $data['nama_menus'];?>" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="urut_menus" class="col-sm-2 control-label">Urutan</label>
          <div class="col-sm-10">
            <input type="number" class="form-control" id="urut_menus" name="urut_menus" placeholder="Urutan..." value="<?php echo $data['urut_menus'];?>" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="icon_menus" class="col-sm-2 control-label">Icon</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="icon_menus" name="icon_menus" placeholder="Icon..." value="<?php echo $data['icon_menus'];?>" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="folder" class="col-sm-2 control-label">Folder</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="folder" name="folder" placeholder="Folder..." value="<?php echo $data['folder'];?>" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="keterangan" class="col-sm-2 control-label">Keterangan</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" style="resize:vertical;" placeholder="Keterangan..." disabled><?php echo $data['keterangan'];?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="status" class="col-sm-2 control-label">Status</label>
          <div class="col-sm-10">
            <select class="form-control select22" id="status" name="status" style="width: 100%;">
              <?php 
                $kode = "status_referensi";
                $item = $data['status'];
                $getReferensi = $db->getReferensi($kode,$item); 
                foreach($getReferensi[1] as $row){
              ?>
              <option value="<?php echo $row['item'];?>"><?php echo $row['keterangan'];?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <br>
        <div class="form-group">
          <label for="id_menu" class="col-sm-2 control-label">Menu</label>
          <div class="col-sm-10">
            <select class="form-control select22" id="id_menu" name="id_menu" style="width: 100%;">
              <?php 
                $id = $data['id_menu'];
                $getMenuById = $db->getMenuById($id); 
                foreach($getMenuById[1] as $row){
              ?>
              <option value="<?php echo $row['id_menu'];?>"><?php echo $row['nama_menu'];?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="id_menu_sub" class="col-sm-2 control-label">Menu Sub</label>
          <div class="col-sm-10">
            <select class="form-control select22" id="id_menu_sub" name="id_menu_sub" style="width: 100%;">
              <?php 
                $id = $data['id_menu_sub'];
                $getMenuSubById = $db->getMenuSubById($id); 
                foreach($getMenuSubById[1] as $row){
              ?>
              <option value="<?php echo $row['id_menu_sub'];?>"><?php echo $row['nama_menu_sub'];?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
    </form>
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->
<script>
  $(document).ready(function(){
    if($('#id_menu_sub').val() == null){
      $('#id_menu_sub').html('<option>-- Pilih --</option>');
    }
  });
</script>
<?php }}?>

<script>
  $('.select22').select2()

  $(function () {
    $('.table').DataTable({
      'language': {
        "emptyTable": "Data tidak ditemukan.",
        "info": "Menampilkan _START_ - _END_ dari _TOTAL_",
        "infoEmpty": "Menampilkan 0 - 0 dari 0",
        "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
        "lengthMenu": "Tampilkan _MENU_ baris",
        "search": "Cari:",
        "zeroRecords": "Tidak ditemukan data yang sesuai.",
        "thousands": "'",
        "paginate": {
          "first": "<<",
          "last": ">>",
          "next": ">",
          "previous": "<"
        }
      },  
      'destroy'     : true,
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true
    })
  })
</script>
<?php 
}}} else {
  header("HTTP/1.1 401 Unauthorized");
  exit;
} ?>