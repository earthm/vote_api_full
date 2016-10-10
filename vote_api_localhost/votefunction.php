<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

function curlget($url,$header=''){
  try{
    $curl = curl_init();

      $curl_options = array(
        CURLOPT_URL => $url,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => $header
      );

    curl_setopt_array($curl,$curl_options);

    if($result = curl_exec($curl)) {
      $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      $a['status'] = $status;
      $a['result'] = $result;
    }else{
      $a['status'] = FALSE;
      $a['result'] = curl_error($curl);
    }

    return $a;

  }catch(Exception $e){

  }
}

function curlpost($url,$data='',$header=''){
  try{
    $curl = curl_init();
    $curl_options = array(
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => $header
    );

    curl_setopt_array($curl,$curl_options);

    if($result = curl_exec($curl)) {
      $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      $a['status'] = $status;
      $a['result'] = $result;
    }else{
      $a['status'] = FALSE;
      $a['result'] = curl_error($curl);
    }

    return $a;

  }catch(Exception $e){

  }
}
function curlput($url,$data='',$header=''){
  try{
    $session = new Session();
    $session->start();
    $curl = curl_init();
    $curl_options = array(
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => $header
    );

    //http_build_query($data)

    curl_setopt_array($curl,$curl_options);

    if($result = curl_exec($curl)) {
      $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      $a['status'] = $status;
      $a['result'] = $result;
    }else{
      $a['status'] = FALSE;
      $a['result'] = curl_error($curl);
    }

    return $a;

  }catch(Exception $e){

  }
}
function getvote($id,$limit,$sn,$sort,$sortby){

  	$url = 'http://localhost:8888/vote_api/vote/index.php';
    $param = array();
    if($id != ''){
      $param[0] = 'id='.$id;
    }
    if($limit != -1){
      $param[1] = 'limit='.$limit;
    }
    if($sort != ''){
      $param[2] = 'sort='.$sort;
    }
    if($sn != ''){
      $param[3] = 'sn='.$sn;
    }
    if($sortby != ''){
      $param[4] = 'sortby='.$sortby;
    }
    if(count($param) > 0){
      $parameter = implode('&',$param);
      $url = $url.'?'.$parameter;
    }
    $header = array(
      'X-LIMP-SESSION:'.session_id()
    );
    $result_api = curlget($url,$header);
    $return = array();

  	if($result_api['status'] == 200){
  		$result_ = json_decode($result_api['result'],TRUE);
      //echo $result_api['result'];
      $status = 200;
      $all_result = array();

      $return['status'] = $status;
      $return['result'] = $result_;
  	}else{
      $return['status'] = $result_api['status'];
      $return['result'] = $result_api['result'];
    }
    //print_r($return);
    return json_encode($return);//JSON_FORCE_OBJECT

}
function insertvote($pb){

  	$url = 'http://localhost:8888/vote_api/vote/index.php';
    $post = json_encode($pb);
    $header = array(
      'X-LIMP-SESSION:'.session_id()
    );
  	$result_api = curlpost($url,$post,$header);
    $return = array();
  	if($result_api['status'] == 200){
  		$result_ = json_decode($result_api['result'],TRUE);
      //echo $result_api['result'];
      $status = 200;
      $all_result = array();

      $return['status'] = $status;
      $return['result'] = $result_;
  	}else{
      $return['status'] = $result_api['status'];
      $return['result'] = "Not found.";
    }
    return json_encode($return);//JSON_FORCE_OBJECT
}

function submitvote($pb){

  	$url = 'http://localhost:8888/vote_api/vote/submit/index.php';
    $post = json_encode($pb);
    $header = array(
      'X-LIMP-SESSION:'.session_id()
    );
  	$result_api = curlpost($url,$post,$header);
    $return = array();
  	if($result_api['status'] == 200){
  		$result_ = json_decode($result_api['result'],TRUE);
      //echo $result_api['result'];
      $status = 200;
      $all_result = array();

      $return['status'] = $status;
      $return['result'] = $result_;
  	}else{
      $return['status'] = $result_api['status'];
      $return['result'] = "Not found.";
    }
    return json_encode($return);//JSON_FORCE_OBJECT
}

function alreadyvote($vote_id,$vote_uid){

  	$url = 'http://localhost:8888/vote_api/vote/submit/index.php?vid='.$vote_id.'&uid='.$vote_uid;
    $header = array(
      'X-LIMP-SESSION:'.session_id()
    );
  	$result_api = curlget($url,$header);
    $return = array();
  	if($result_api['status'] == 200){
  		$result_ = json_decode($result_api['result'],TRUE);
      //echo $result_api['result'];
      $status = 200;
      $all_result = array();

      $return['status'] = $status;
      $return['result'] = $result_;
  	}else{
      $return['status'] = $result_api['status'];
      $return['result'] = "Not found.";
    }
    return json_encode($return);//JSON_FORCE_OBJECT
}
function hashtagInsert($pb){
  $url = 'http://localhost:8888/vote_api/vote/hashtag/index.php';
  $post = json_encode($pb);
  $header = array(
    'X-LIMP-SESSION:'.session_id()
  );
  $result_api = curlpost($url,$post,$header);
  $return = array();
  if($result_api['status'] == 200){
    $result_ = json_decode($result_api['result'],TRUE);
    //echo $result_api['result'];
    $status = 200;
    $all_result = array();

    $return['status'] = $status;
    $return['result'] = $result_;
  }else{
    $return['status'] = $result_api['status'];
    $return['result'] = "Not found.";
  }
  return json_encode($return);//JSON_FORCE_OBJECT
}
function getVoteTags($vote_id){

  	$url = 'http://localhost:8888/vote_api/vote/hashtag/index.php?id='.$vote_id;
    $header = array(
      'X-LIMP-SESSION:'.session_id()
    );
  	$result_api = curlget($url,$header);
    $return = array();
  	if($result_api['status'] == 200){
  		$result_ = json_decode($result_api['result'],TRUE);
      //echo $result_api['result'];
      $status = 200;
      $all_result = array();

      $return['status'] = $status;
      $return['result'] = $result_;
  	}else{
      $return['status'] = $result_api['status'];
      $return['result'] = "Not found.";
    }
    return json_encode($return);//JSON_FORCE_OBJECT
}


?>
