<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include("../../../config.php");  /// global config
include("../../config.php");
include("../../votefunction.php");
if(!isset($HTTP_RAW_POST_DATA)) {
    $HTTP_RAW_POST_DATA = file_get_contents("php://input");
}
/*function http_response_code($newcode = NULL)
{
        static $code = 200;
        if($newcode !== NULL)
        {
            header('status: ' . $newcode, true, $newcode);
            if(!headers_sent())
                $code = $newcode;
        }
        return $code;
}*/

$base_uri = "/";
$return_data = array();

//extract the GET parameter
$q = str_replace($base_uri, '', $_SERVER['REQUEST_URI']);
//
$r = $_SERVER['REQUEST_METHOD'];
// check HTTP method
if($r == 'GET') {
  $vote_id = isset($_GET['id'])? $_GET['id'] : '';

	$result = getVoteTags($vote_id);


    $result = json_decode($result);
    //print_r($result);
    //echo "count()".count($result);
    if(count($result) > 0){
      http_response_code(200);
			echo json_encode($result);
		}else{
		  http_response_code(404);
      echo json_encode($result);
		}
  //}
}
elseif($r == 'POST') {
   // echo $r;
    if(is_null($HTTP_RAW_POST_DATA) || $HTTP_RAW_POST_DATA == '') {
        http_response_code(400);
        echo "Missing request data";
        return;
    }else {
      $pb = json_decode($HTTP_RAW_POST_DATA, true);
	    $result = hashtagInsert($pb);


      $data = json_decode($result, true);

	    //$return_data['result'] = $data;
      if($data['result'] == 'true'){
         $return_data['key'] = $data['key'];
         http_response_code(200);

       }else{
       $return_data['key'] = $data['key'];
         http_response_code(404);
      }

    	echo json_encode($return_data);
    }
}
elseif($r == 'PUT') {

}
elseif($r == 'DELETE') {

}
else {
    http_response_code(405);
    return;
}
?>
