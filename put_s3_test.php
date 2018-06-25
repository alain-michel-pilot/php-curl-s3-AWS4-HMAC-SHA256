<?php

// set our constants from env vars
define('S3_KEY', getenv('S3_KEY'));
define('S3_SECRET', getenv('S3_SECRET'));
define('S3_REGION', getenv('S3_REGION'));
define('S3_BUCKET', getenv('S3_BUCKET'));



// set the curl endpoint we will hit at AWS
//$endpoint_url = "http://s3-" . S3_REGION . ".amazonaws.com/" . S3_BUCKET . "/index.html";
$endpoint_url = "https://s3-" . S3_REGION . ".amazonaws.com/" . S3_BUCKET . "/index.html";

// instantiate a curl resource
$curl = curl_init(); 


// set our cURL options
curl_setopt_array($curl, array(
	CURLOPT_CONNECTTIMEOUT => 30,
	CURLOPT_LOW_SPEED_LIMIT => 1,
	CURLOPT_LOW_SPEED_TIME => 30,
	CURLOPT_USERAGENT => 'leonstafford/wordpress-static-html-plugin',
	CURLOPT_URL => $endpoint_url,
	CURLOPT_HEADER => true,// get header back in response
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => false,
//	CURLOPT_CUSTOMREQUEST => "PUT",
	CURLOPT_VERBOSE => true,
	CURLOPT_STDERR =>  fopen('php://stderr', 'w'),
	CURLOPT_HTTPHEADER => array(
		'PUT',
		'Content-Type: text/html',
		'Content-Length: ' . filesize('index.html'),
		'Content-MD5: ' . base64_encode(md5_file('index.html')),
		'x-amz-acl: public-read',
		'x-amz-date: 20180625',
		'Host:' . S3_BUCKET . '.s3.amazonaws.com',
		'Date: 20180625',
		'Authorization: AWS4-HMAC-SHA256 Credential=' . S3_KEY . '/20180625/' . S3_REGION . '/s3/aws4_request,SignedHeaders=host;x-amz-date,Signature=fe5f80f77d5fa3beca038a248ff027d0445342fe2855ddc963176630326f1024'
	)
));



// show the curl info before making the request

$output = curl_exec($curl); 

echo print_r($output);

curl_close($curl);

