<?php
  if (!(isset($_SESSION['Username'])) || $_SESSION['Username'] == "") {
   echo "<script language=\"JavaScript\">alert('You must be logged in to access this portion of the control panel.');location.href = 'manage.php';</script>";
  }
  if (!(isset($_SESSION['LOGGED_IN'])) || $_SESSION['LOGGED_IN'] != true) {
   echo "<script language=\"JavaScript\">alert('You must be logged in to access this portion of the control panel.');location.href = 'manage.php';</script>";
  }
?>