<?php
# Запуск host.php <имя файла>
$link=mysqli_connect("192.168.0.113", "root", "1234567", "nmap");
while (!$link) {
	printf("%s\n", mysqli_connect_error());
	echo "Wait for MySQL...\n";
	sleep (30);
	$link=mysqli_connect("192.168.0.113", "root", "1234567", "nmap");
}
mysqli_query($link, 'set names utf8');
$id=NULL;
$mac=NULL;
$macname=NULL;
$ip=NULL;
$hostname=NULL;
$device=NULL;
$os=NULL;
$scantime=NULL;
$ports=NULL;
$file_handle = fopen("$argv[1]", "r");
while (!feof($file_handle)) {
	$line = fgets($file_handle);
	if (strlen(strstr($line,"MAC Address: ")) > 0){
	$mac=trim(substr($line,13,17));
	if (strlen(strstr($line,"(")) > 0 && strlen(strstr($line,")")) > 0 ){
	$macname=preg_replace("/[^a-zA-Z0-9\s]/", "", trim(substr($line,strpos($line,"(")+1,strpos($line,")")-strpos($line,"(")-1)));
	}
	}
	if (strlen(strstr($line,"Nmap done: 1 IP address (1 host up) scanned")) > 0){
	$scantime=trim(substr($line,47,strpos($line," seconds")-47));
	}
	if (strlen(strstr($line,"Nmap done: 1 IP address (0 hosts up) scanned")) > 0){
	if (isset($argv[2])){
	echo $argv[2]." - Host down\n";
	}
	exit;
	}
}
fclose($file_handle);

if (is_null($mac)) {
echo "MAC Address failed\n";
$id=0;
}

$result=mysqli_query($link,"SELECT * FROM host WHERE mac LIKE '$mac'");
while ($data = mysqli_fetch_array($result))
{
echo "MAC Address in database\n";
$id=$data['id'];
}
mysqli_free_result($result);

if (is_null($id)){
echo "New MAC Address found: ".$mac."\n";
mysqli_query($link,"INSERT INTO host (mac, macname) VALUES ('$mac', '$macname')");

$result=mysqli_query($link,"SELECT * FROM host WHERE mac LIKE '$mac'");
while ($data = mysqli_fetch_array($result))
{
$id=$data['id'];
}
mysqli_free_result($result);

}



$file_handle = fopen("$argv[1]", "r");
while (!feof($file_handle)) {
	$line = fgets($file_handle);

	if (strlen(strstr($line,"Nmap scan report for ")) > 0){
	if(strlen(strstr($line,"(")) > 0 && strlen(strstr($line,")")) > 0 ) {
	$ip=trim(substr($line,strpos($line,"(")+1,strpos($line,")")-strpos($line,"(")-1));
	$hostname=trim(substr($line,20,strpos($line,"(")-20));
	} else {
	$ip=trim(substr($line,20));
	}
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
	if(strlen(strstr($line,"/tcp")) > 0 || strlen(strstr($line,"/udp")) > 0){
	#echo $line;
	$port=substr($line,0,strpos($line,"/"));
	#echo "PORT: ".$port."\n";
	$protocol=trim(substr($line,strpos($line,"/")+1,3));
	#echo "PROTOCOL: ".$protocol."\n";
	$ports=$ports." ".$port."/".$protocol.",";
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
	$date=date("Y-m-d");
	$check=NULL;
	$result=mysqli_query($link,"SELECT * FROM port WHERE host_id LIKE '$id' AND date LIKE '$date' AND ip LIKE '$ip' AND port LIKE '$port' AND protocol LIKE '$protocol'");
	while ($data = mysqli_fetch_array($result))
	{
	$check=1;
	}
	mysqli_free_result($result);
	if (is_null($check)){
	mysqli_query($link,"INSERT INTO port (host_id, date, hostname, ip, port, protocol, state, service, version, scantime) VALUES ('$id', SUBSTR(NOW(),1,10), '$hostname', '$ip', '$port', '$protocol', '$state', '$service', '$version', '$scantime')");
	}
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

echo "ID: ".$id."\n";
echo "IP: ".$ip."\n";
echo "Hostname: ".$hostname."\n";
echo "MAC: ".$mac."\n";
echo "Mac Name: ".$macname."\n";
echo "Device type: ".$device."\n";
echo "OS: ".$os."\n";
echo "Ports: ".$ports."\n";
echo "Scan time: ".$scantime."\n\n";

# Scan log
if ( isset($argv[2]) ){
mysqli_query($link,"INSERT INTO scan_log (host_id, date, ip) VALUES ('$id', SUBSTR(NOW(),1,10), '$argv[2]')");
}
mysqli_close($link);
?>
