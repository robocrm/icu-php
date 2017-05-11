<?
$interest = 2;

$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$tid = isset($_POST['tid']) ? trim($_POST['tid']) : '';
$fid = isset($_POST['fid']) ? trim($_POST['fid']) : '';
$products = isset($_POST['products']) ? $_POST['products'] : '';

if(isset($_POST['visit_id'])){ 
    $visit_id = $_POST['visit_id'];
} else {
    if(isset($_COOKIE["visit_id"])) { $visit_id = $_COOKIE["visit_id"] ;} else {$visit_id = null;};    
}

$post_data = json_encode(array(
    'visit_id' => $visit_id,
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
