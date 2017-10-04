<?php

date_default_timezone_set('UTC');

function curl($url, $method, $data = null) 
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(	   
	    'Access-Control-Request-Headers: content-type',
	    'Connection: keep-alive',
	    'Content-Type: application/json',
	    ));
	if ($method === 'POST') {
		curl_setopt($ch, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
	}
	$result = json_decode(curl_exec($ch));
	if (curl_errno($ch)) {
	    // this would be your first hint that something went wrong
	    die('Couldn\'t send request: ' . curl_error($ch));
	} else {
	    // check the HTTP status code of the request
	    $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    if ($resultStatus >= 200 && $resultStatus < 300) {
	        return $result;
	    } else {
	        // the request did not complete as expected. common errors are 4xx
	        // (not found, bad request, etc.) and 5xx (usually concerning
	        // errors/exceptions in the remote script execution)
	        echo 'Request failed: HTTP status code: ' . $resultStatus;
	        return false;
	    }
	}
	curl_close($ch);
}

$data = json_decode(file_get_contents('php://input'));

if (isset($data->visit)) {
	$log = date('d:m:y - H:i:s').'--'.file_get_contents('php://input').'--'.$_SERVER['HTTP_USER_AGENT'];
	if (isset($_COOKIE['session_id'])) {
		file_put_contents ('log.txt', $log.'--'.$_COOKIE['session_id']."\r\n", FILE_APPEND);
	} else {
		file_put_contents ('log.txt', $log."\r\n", FILE_APPEND);
	}

	if (isset($data->xhr)) {
		$cookies = json_encode([
			"cookies" => [
				"sid" => $data->cookies->sid,
				"vid" => $data->cookies->vid
			]
		]);

		$result = curl('http:'.$data->url, "POST", $cookies);
		if ($result) {
			setcookie('session_id', $result->session_id, -1, '/');
			setcookie('sid', $result->cookies->sid, -1, '/');
			setcookie('vid', $result->cookies->vid, -1, '/');

			$result = curl("http://robocrm.nanocoding.com/icu/load_number?tid=".$data->tid."&sid=".$result->cookies->sid, "GET");
			if ($result) {
				echo $result->number;
			}
			exit(); 
		}
	}
} else {
	$interest = 2;
	$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
	$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
	$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
	$tid = isset($_POST['tid']) ? trim($_POST['tid']) : '';
	$fid = isset($_POST['fid']) ? trim($_POST['fid']) : '';
	$products = isset($_POST['products']) ? $_POST['products'] : '';
	if(isset($_POST['session_id'])){
	    $session_id = $_POST['session_id'];
	} else {
	    if(isset($_COOKIE["session_id"])) { $session_id = $_COOKIE["session_id"] ;} else {$session_id = null;};
	}

	$post_data = json_encode(array(
	    'session_id' => $session_id,
	    'phone' => $phone,
	    'first_name' => $first_name,
	    'comment' => $comment,
	    'interest' => $interest,
	    'products' => $products,     
	    ));
	$url = 'http://robocrm.nanocoding.com/icu/lead?tid='.$tid."&fid=".$fid ;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST, 1); // set POST method
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	   
	    'Access-Control-Request-Headers: content-type',
	    'Access-Control-Request-Method: POST',
	    'Connection: keep-alive',
	    'Content-Type: application/json',
	    ));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); // add POST fields
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    // this would be your first hint that something went wrong
	    die('Couldn\'t send request: ' . curl_error($ch));
	} else {
	    // check the HTTP status code of the request
	    $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    if ($resultStatus == 200) {
	        // everything went better than expected
	        //header("Location: http://example.com/");
	    } else {
	        // the request did not complete as expected. common errors are 4xx
	        // (not found, bad request, etc.) and 5xx (usually concerning
	        // errors/exceptions in the remote script execution)
	        die('Request failed: HTTP status code: ' . $resultStatus);
	    }
	}
	curl_close($ch);
}
