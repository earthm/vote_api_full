<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
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

  $result = json_decode($result, TRUE);
  //print_r($result);
  if($result['status'] == '200'){
    http_response_code(200);
    //print_r($result['result']);
    $return = $result['result'];
		echo json_encode($return);
	}else{
		http_response_code(404);
    //echo json_encode($result);
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
      if($data['status'] == '200'){
         $return_data['key'] = $data['result']['key'];
         http_response_code(200);

       }else{
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
