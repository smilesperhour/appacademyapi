<?php

//Configuration for our PHP Server
set_time_limit(0);
ini_set('default_socket_timeout', 300);

session_start();

//Make Constant using define.
define('clientID', '7febd8e1af4b41ca8e6b4e9ca46183ed');
define('clientSecret', '6bef1e045ff04c6e812f1dff17920a63');
define('redirectURI', 'http://localhost/appacademyapi/index.php');
define('ImageDirectory', 'pics/');

//function that will connect to instagram
function connectToInstagram($url){
	$ch = curl_init();

	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => 2,

	));
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
//function to get userid for pics
function getUserID($username){
	$url = 'http://api.instagram.com/v1/users/search?q=' .$userName . '&client_id='.clientID;
	$instagramInfo = connectToInstagram($url);
	$results = json_decode($instagramInfo, true);

	echo $results['data']['0']['id'];
}

if (isset($_GET['code'])){
	$code = (($_GET['code']));
	$url = 'https://api.instagram.com/oauth/access_token';
	$access_token_settings = array('client_id' => clientID,
								   'client_secret' => clientSecret,
								   'grant_type' => authorization_code,
								   'redirect_uri' => redirectURI,
								   'code' => $code
								   );
	//curl is what we use in php, it is a librbary to make calls to other api's
	$curl = curl_init($url);// setting a curl session and url is were we are getting the data from.
	curl_setopt($curl, CURLOPT_POST, true);curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings);//setting post fields to the array setup
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//1 is to get a string
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($curl);
curl_close($curl);

$results = json_decode($result, true);
getUserID($results['suser']['username']);
}
else{
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	
	<a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI; ?>&response_type=code">Login</a>
	
</body>
</html>
<?php  
}
?>
