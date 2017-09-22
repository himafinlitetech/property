<?php
$attId = $_REQUEST['id'];
//Start Connection
$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => "https://login.salesforce.com/services/oauth2/token",
	CURLOPT_SSL_VERIFYPEER =>0,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => "client_id=3MVG9g9rbsTkKnAXEHsxzO08QzDD2_0GnIedlmVrxZWJcZtP_A8dGjmuGcDlZjeqXCEUgjUxIp_Sau3voeK1v&client_secret=1687696237180270615&grant_type=password&username=warren@yh.com&password=demo1234",
	CURLOPT_HTTPHEADER => array(
	"cache-control: no-cache",
	"content-type: application/x-www-form-urlencoded",
	"postman-token: d3723d71-0840-5a08-8635-da468eca0978"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);
$res = (array)json_decode($response);

$access_token = $res['access_token'];

$instance_url = "https://ruchin-dev-ed.my.salesforce.com";

//End Connection 



$query = "SELECT id,Price__c from Property_Detail__c where id='".$attId."'";
			
$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => "https://ruchin-dev-ed.my.salesforce.com/services/data/v39.0/query?q=".urlencode($query),
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER =>0,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 500,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
	"authorization: Bearer ".$res['access_token'],
	"cache-control: no-cache"
	),
));

$response1 = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);
$res1 = (array)json_decode($response1);

			

if(!empty($_POST['price'])){		
	
	$upadatePrice = $_POST['price'];
	
	$content = json_encode(array('Price__c' => $upadatePrice));
	$url = "$instance_url/services/data/v39.0/sobjects/Property_Detail__c/".$attId;

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_HTTPHEADER,array("Authorization: OAuth $access_token","Content-type: application/json"));
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

	curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	if ($status != 204) {
	//die("Error: call to URL $url failed with status $status, curl_error " . curl_error($curl) .", curl_errno " . curl_errno($curl));
	}

	curl_close($curl);
	
	header("Location: site.php");
			
}
?>


<html>
<head>
</head>
<body>
<form method="POST" action="edit.php?id=<?php echo $attId; ?>">

<label>Property Price</label>

	<input type="text" value="<?php echo $res1['records'][0]->Price__c; ?>" name="price" />
	<button type="submit" >Save</button>
</form>
</body>

</html>
