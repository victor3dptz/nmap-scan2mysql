<?php
# Запуск host.php <имя файла>
$link=mysqli_connect("192.168.0.113", "root", "1234567", "nmap");
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}
mysqli_query($link, 'set names utf8');
$mac="";
$device="";
$os="";
$scantime="";

$file_handle = fopen("$argv[1]", "r");
while (!feof($file_handle)) {
	$line = fgets($file_handle);

	if (strlen(strstr($line,"Nmap scan report for ")) > 0){
	$ip=trim(substr($line,20));
	mysqli_query($link,"INSERT INTO host (ip, date, time) VALUES ('$ip', SUBSTR(NOW(),1,10), SUBSTR(NOW(),12,8))");
	$result=mysqli_query($link,"SELECT * FROM host WHERE ip LIKE '$ip' ORDER BY id DESC LIMIT 1");
	while($data = mysqli_fetch_array($result))
	{
	$id=$data['id'];
	}
	mysqli_free_result($result);
	}
	if (strlen(strstr($line,"MAC Address: ")) > 0){
	$mac=trim(substr($line,13,17));
	mysqli_query($link,"UPDATE host SET mac='$mac' WHERE id LIKE '$id'");
	}
	if (strlen(strstr($line,"Device type: ")) > 0){
	$device=trim(substr($line,13));
	mysqli_query($link,"UPDATE host SET device='$device' WHERE id LIKE '$id'");
	}
	if (strlen(strstr($line,"Running: ")) > 0){
	$os=trim(substr($line,9));
	mysqli_query($link,"UPDATE host SET os='$os' WHERE id LIKE '$id'");
	}
	if (strlen(strstr($line,"Running (JUST GUESSING): ")) > 0) {
	$os=trim(substr($line,25));
	mysqli_query($link,"UPDATE host SET os='$os' WHERE id LIKE '$id'");
	}
	if (strlen(strstr($line,"Aggressive OS guesses: ")) > 0){
	if (strlen($os) == 0) {
	$os=trim(substr($line,23));
	mysqli_query($link,"UPDATE host SET os='$os' WHERE id LIKE '$id'");
	}
	}
	if (strlen(strstr($line,"Nmap done: 1 IP address (1 host up) scanned")) > 0){
	$scantime=trim(substr($line,47,strpos($line," seconds")-47));
	mysqli_query($link,"UPDATE host SET scantime='$scantime' WHERE id LIKE '$id'");
	}
	if(strlen(strstr($line,"/tcp")) > 0 || strlen(strstr($line,"/udp")) > 0){
	#echo $line;
	$port=substr($line,0,strpos($line,"/"));
	#echo "PORT: ".$port."\n";
	$protocol=trim(substr($line,strpos($line,"/")+1,3));
	#echo "PROTOCOL: ".$protocol."\n";
	$state=trim(substr($line,$statepos,$servicepos-$statepos));
	#echo "STATE: ".$state."\n";
	if ($versionpos > 0){
	$service=trim(substr($line,$servicepos,$versionpos-$servicepos));
	#echo "SERVICE: ".$service."\n";
	$version=trim(substr($line,$versionpos));
	#echo "VERSION: ".$version."\n";
	} else {
	$service=trim(substr($line,$servicepos));
	#echo "SERVICE: ".$service."\n";
	$version="";
	}
	mysqli_query($link,"INSERT INTO port (id,port,protocol,state,service,version) VALUES ('$id','$port','$protocol','$state','$service','$version')");
	}
	if(strlen(strstr($line,"PORT")) > 0 && strlen(strstr($line,"STATE")) > 0 && strlen(strstr($line,"SERVICE")) > 0 && strlen(strstr($line,"VERSION")) > 0){
	$statepos=strpos($line,"STATE");
	$servicepos=strpos($line,"SERVICE");
	$versionpos=strpos($line,"VERSION");
	}
	if(strlen(strstr($line,"PORT")) >0 && strlen(strstr($line,"STATE")) > 0 && strlen(strstr($line,"SERVICE")) > 0 && strlen(strstr($line,"VERSION")) == 0){
	$statepos=strpos($line,"STATE");
	$servicepos=strpos($line,"SERVICE");
	$versionpos=0;
	}

}
fclose($file_handle);

if (isset($ip)){
echo "ID: ".$id."\n";
echo "IP: ".$ip."\n";
echo "MAC: ".$mac."\n";
echo "Device type: ".$device."\n";
echo "OS: ".$os."\n";
echo "Scan time: ".$scantime."\n\n";
}

mysqli_close($link);
?>
