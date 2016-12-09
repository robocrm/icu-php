<?
$interest = 2;

if(isset($_POST['phone'])){ $phone = trim($_POST['phone']);}
if(isset($_POST['first_name'])){$first_name = trim($_POST['first_name']) ;}
if(isset($_POST['comment'])){$comment = trim($_POST['comment']) ;}
if(isset($_POST['tid'])){ $tid = trim($_POST['tid']);}
if(isset($_POST['fid'])){ $fid = trim($_POST['fid']);}
if(isset($_POST['product'])){ $product = $_POST['product'];}

if(isset($_COOKIE["visit_id"])) { $visit_id = $_COOKIE["visit_id"] ;} else {$visit_id = null;};
$product = json_encode($product[0]);
$post_data = json_encode(array(
    'visit_id' => $visit_id,
    'phone' => $phone,
    'first_name' => $first_name,
    'comment' => $comment,
    'interest' => $interest,
    'products' => '['.$product.']',     
    ), JSON_FORCE_OBJECT);

// var_dump($post_data);
$url = 'http://robocrm.nanocoding.com/icu/lead?tid='.$tid."&fid=".$fid ;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
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
        header("Location: http://example.com/");
    } else {
        // the request did not complete as expected. common errors are 4xx
        // (not found, bad request, etc.) and 5xx (usually concerning
        // errors/exceptions in the remote script execution)
        die('Request failed: HTTP status code: ' . $resultStatus);
    }
}
curl_close($ch);
?>