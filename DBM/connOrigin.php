<?php
$webhost        = '192.168.1.233';
$webusername    = 'root';
$webpassword    = 'msroot';
$webdbname      = 'erp';
$webcon         = mysqli_connect($webhost, $webusername, $webpassword, $webdbname);
$webcon->set_charset("utf8");
?>