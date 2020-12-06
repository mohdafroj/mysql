<?php
$basePath = ($_SERVER["HTTPS"] != "on") ? "http://" : "https://";
$basePath .= $_SERVER["SERVER_NAME"];
if (strpos($_SERVER["REQUEST_URI"], 'new/') != FALSE ){
  $basePath .= '/new';
}	
header('Location: '.$basePath);

?>