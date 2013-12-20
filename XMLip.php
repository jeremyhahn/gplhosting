<?php
$Time = Time();

echo "<DynamicDNS>\r\n";

   echo "\r\n\t<WANaddress value=\"" . $_SERVER['REMOTE_ADDR'] . "\"></WANaddress>";
   echo "\r\n\t<TimeStamp value=\"$Time\"></TimeStamp>\r\n";

echo "\r\n</DynamicDNS>";

?>