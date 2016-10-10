<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// include("../../config.php");  /// global config
 include("../config.php");
 include("../votefunction.php");
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
  $id = isset($_GET['id'])? $_GET['id'] : '';
  $limit = isset($_GET['limit'])? $_GET['limit'] : -1;
  $sort = isset($_GET['sort'])? $_GET['sort'] : '';
  $sn = isset($_GET['sn'])? $_GET['sn'] : '';
  $sortby = isset($_GET['sortby'])? $_GET['sortby'] : '';

  if((!is_numeric($id) || $id < -1) && $id != ''){
    http_response_code(400);
    echo "Bad Request ";
    return;
  }
  $result = getvote($id,$limit,$sn,$sort,$sortby);



    $result = json_decode($result);
    //print_r($result);
    //echo "count()".count($result);
    if(count($result) > 0){
      http_response_code(200);

      //$return['status'] = 200;
      //$return['result'] = $result;

			echo json_encode($result);
		}else{
		  http_response_code(404);

      //$return['status'] = 200;
      //$return['result'] = $result;

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
	    $result = insertvote($pb);

      $data = json_decode($result, true);

	    //$return_data['result'] = $data;
      if($data['result'] == 'true'){
         $return_data['key'] = $data['key'];
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
