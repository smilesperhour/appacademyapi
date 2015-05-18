<?php
	//Configuration for our PHP Server.
	set_time_limit(0);
	ini_set('default_socket_timeout', 300);
	session_start();
	//Make Constants using define.
define('clientID', '7febd8e1af4b41ca8e6b4e9ca46183ed');
define('clientSecret', '6bef1e045ff04c6e812f1dff17920a63');
define('redirectURI', 'http://localhost/appacademyapi/index.php');
define('ImageDirectory', 'pics/');
	//Function that is going to connect to Instagram.
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
	//function to get userID cause username doesn't allow us to get picture!
	function getUserID($userName){
		$url = 'https://api.instagram.com/v1/users/search?q='.$userName.'&client_id='.clientID;
		$instagramInfo = connectToInstagram($url);//connnecting to Instagram 
		$results = json_decode($instagramInfo, true);//creating a local variable to decode json infomation.
		return $results['data'][0]['id'];//echoing out userID.
	}
	//function to print out images onto screen
	function printImages($userID){
		$url = 'https://api.instagram.com/v1/users/'.$userID.'/media/recent?client_id='.clientID.'&count=5';
		$instagramInfo = connectToInstagram($url);
		$results = json_decode($instagramInfo, true);
		//Parse through the info. one by one.
		foreach ($results['data'] as $items){
			$image_url = $items['images']['low_resolution']['url'];//going to go through all of my results and give myself back the URL of those pictures because we want to save it in the PHP server.
			// echo '<img src=" '.$image_url.'"/><br/>';
			 echo '<img src=" '.$image_url.'"/><br/>';
			//calling a function to save that $image_url
			savePictures($image_url);	
		}
	}
//function to save image to server
	function savePictures($image_url){
		echo $image_url .'<br>';
		$filename = basename($image_url);//the filename is what we are storing. basename is the PHP built in method that we are using to store $image_url
		echo $filename . '<br>';
		$destination = ImageDirectory . $filename;//making sure that the image doesn't exist in the storage.
		file_put_contents($destination, file_get_contents($image_url));//goes and grabs an imagefile and stores is into our sserver/.
	}
	if (isset($_GET['code'])){
		$code = $_GET['code'];
		$url = 'https://api.instagram.com/oauth/access_token';
		$access_token_settings = array('client_id' => clientID, 
									   'client_secret' => clientSecret,
									   'grant_type' => 'authorization_code',
									   'redirect_uri' => redirectURI,
									   'code' => $code
										);
		//cURL is what we use in PHP, it's a library calls to other API's
		$curl = curl_init($url);//setting a cURL session and we put in $url because that's where we are getting the data from.
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings);//settings the POSTFIELDS to array setup that we created.
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//setting it equal to 1 because we are getting strings back.
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//but in live work-production we want to set this to true.
$result = curl_exec($curl);
curl_close($curl);
$results = json_decode($result, true);
$userName = $results['user']['username'];
$userID = getUserID($userName);
printImages($userID);
}
else{
?>
<!DOCTYPE html>
<html>
<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<head>
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="stylesheet" type="text/css" href="css/pic.css">
<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Cedarville+Cursive' rel='stylesheet' type='text/css'>
	<title></title>
</head>
<body>
	<div>
	<a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI; ?>&response_type=code">Login</a>
	</div>
</body>
</html>
<?php  
}
?>
