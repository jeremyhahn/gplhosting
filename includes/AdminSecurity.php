<?php
  if (!(isset($_SESSION['LOGGED_IN'])) || $_SESSION['LOGGED_IN'] != true) {
   echo "<script language=\"JavaScript\">alert('You must be logged in to access this portion of the control panel.');location.href = 'http://www.gplhosting.org/manage.php';</script>";
  }
    if (!(isset($_SESSION['SiteRole'])) || $_SESSION['SiteRole'] != "Site Admin") {
   echo "<script language=\"JavaScript\">alert('You must be logged in with administrative privileges to access this portion of the control panel.');location.href = 'http://www.gplhosting.org/manage.php';</script>";
  }
?>