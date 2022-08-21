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
if(isset($_POST['controller'])) {
  $controller = $_POST['controller'];
} else {
  $controller = "";
}
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

// start - controller
if($controller == 'get_menu_sub'){
  if (isset($_POST['id_menu'])) {
    $id = $_POST['id_menu'];
    $getMenuSubByIdMenu = $db->getMenuSubByIdMenu($id);
      echo '<option value="">-- Pilih --</option>';
    foreach($getMenuSubByIdMenu[1] as $option){
      echo '<option value="'.$option['id_menu_sub'].'">'.$option['nama_menu_sub'].'</option>';
    } 
  }
} else if($controller == 'delete' && $delete == "y"){
  $id = $_POST['id'];
 
  $run = $db->delete($id);
  $retval['status'] = $run[0];
  $retval['title'] = $run[1];
  $retval['message'] = $run[2];
  
  echo json_encode($retval);
} else if($controller == 'create' && $create == "y"){
  $nama_menus = htmlspecialchars($_POST['nama_menus']);
  $urut_menus = htmlspecialchars($_POST['urut_menus']);
  $icon_menus = htmlspecialchars($_POST['icon_menus']);
  $folder = htmlspecialchars($_POST['folder']);
  $keterangan = htmlspecialchars($_POST['keterangan']);
  $status = htmlspecialchars($_POST['status']);
  $id_menu = htmlspecialchars($_POST['id_menu']);
  $id_menu_sub = htmlspecialchars($_POST['id_menu_sub']);

  $run = $db->create($nama_menus,$urut_menus,$icon_menus,$folder,$keterangan,$status,$id_menu,$id_menu_sub);
  $retval['status'] = $run[0];
  $retval['title'] = $run[1];
  $retval['message'] = $run[2];
  
  echo json_encode($retval);
} else if($controller == 'update' && $update == "y"){
  $id = htmlspecialchars($_POST['id_menus']);
  $nama_menus = htmlspecialchars($_POST['nama_menus']);
  $urut_menus = htmlspecialchars($_POST['urut_menus']);
  $icon_menus = htmlspecialchars($_POST['icon_menus']);
  $folder = htmlspecialchars($_POST['folder']);
  $keterangan = htmlspecialchars($_POST['keterangan']);
  $status = htmlspecialchars($_POST['status']);
  $id_menu = htmlspecialchars($_POST['id_menu']);
  $id_menu_sub = htmlspecialchars($_POST['id_menu_sub']);

  $run = $db->update($id,$nama_menus,$urut_menus,$icon_menus,$folder,$keterangan,$status,$id_menu,$id_menu_sub);
  $retval['status'] = $run[0];
  $retval['title'] = $run[1];
  $retval['message'] = $run[2];
  
  echo json_encode($retval);
} else {
  $retval['status'] = false;
  $retval['message'] = "Tidak memiliki hak akses.";
  $retval['title'] = "Error!";
  echo json_encode($retval); 
}
// end - controller

}}} else {
  header("HTTP/1.1 401 Unauthorized");
  exit;
} ?>