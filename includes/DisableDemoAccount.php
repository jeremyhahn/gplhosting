<?php
if ($_SESSION['Username'] == "demo" || $_POST['Username'] == "demo") {
  echo "<script language=\"JavaScript\">alert('This feature has been disabled in the demo account.');history.go(-1);</script>";
  exit();
}

?>