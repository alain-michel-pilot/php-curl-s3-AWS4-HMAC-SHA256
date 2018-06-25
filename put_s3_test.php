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

// dependencies for signature
$dateString = $dateString = date('Ymd');
$credential = implode("/", array(S3_KEY, $dateString, S3_REGION, 's3/aws4_request'));
$xAmzDate = $dateString . 'T000000Z';


$policy = base64_encode(json_encode(array(
  // ISO 8601 - date('c'); generates uncompatible date, so better do it manually.
  'expiration' => date('Y-m-d\TH:i:s.000\Z', strtotime('+5 minutes')), // 5 minutes into the future.
  'conditions' => array(
	array('PUT' => ''),
	array('Content-Type' => 'text/html'),
	array('Content-Length' => filesize('index.html')),
	array('Content-MD5' => base64_encode(md5_file('index.html'))),
	array('x-amz-acl' => 'public-read'),
	array('x-amz-date' => $xAmzDate),
	array('x-amz-content-sha256' => 'UNSIGNED-PAYLOAD'),
	array('Host:' => S3_BUCKET . '.s3.amazonaws.com'),
	array('x-amz-algorithm' => 'AWS4-HMAC-SHA256'),
	array('x-amz-credential' => $credential),
  )
)));

// Generate signature
$dateKey = hash_hmac('sha256', $dateString, 'AWS4' . S3_SECRET, true);
$dateRegionKey = hash_hmac('sha256', S3_REGION, $dateKey, true);
$dateRegionServiceKey = hash_hmac('sha256', 's3', $dateRegionKey, true);
$signingKey = hash_hmac('sha256', 'aws4_request', $dateRegionServiceKey, true);
$signature = hash_hmac('sha256', $policy, $signingKey, false);


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
		'x-amz-date: ' . gmdate('D, d M Y H:i:s T'),
		'x-amz-content-sha256: UNSIGNED-PAYLOAD',
		'Host:' . S3_BUCKET . '.s3.amazonaws.com',
		'x-amz-algorithm:' . 'AWS4-HMAC-SHA256',
		'Authorization: AWS4-HMAC-SHA256 Credential=' . S3_KEY . '/20180625/' . S3_REGION . '/s3/aws4_request,SignedHeaders=host;x-amz-date;x-amz-acl;content-md5;x-amz-algorithm,Signature=' . $signature
	)
));



// show the curl info before making the request

$output = curl_exec($curl); 

echo print_r($output);

curl_close($curl);

