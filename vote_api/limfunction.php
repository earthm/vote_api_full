<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include("config.php");


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
function connect_db(){
    $servername = db_HOSTNAME;
    $username = db_USERNAME;
    $password = db_PASSWORD;
    $dbname = db_DBNAME;

    $conn = mysqli_connect($servername, $username, $password,$dbname);
    //mysqli_select_db($conn, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function badgeCheck($uid, $action_ref){
  $db = connect_db();
  if($action_ref == 'all'){
    $actions_type_id = -1;
  }else{
    $sql = "SELECT actions_type_id FROM actions_type WHERE actions_ref_name = '".$action_ref."' AND status = 1";

    $query = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($query);
    $actions_type_id = $row['actions_type_id'];
  }

  $badgePointlist = conditionsCheck($uid, $actions_type_id);
  $updateUserBadge = updateUserBadge($uid, $badgePointlist[0]);
  $updateUserPoint = updateUserPoint($uid, $badgePointlist[1]);
  echo json_encode($badgePointlist);
}
function conditionsCheck($uid, $actions_type){ ////get all action
  $db = connect_db();
  $sql = "SELECT * FROM actions LEFT JOIN conditions
          ON actions_conditions = conditions_id
          LEFT JOIN badge
          ON actions_badge = badge_id
          LEFT JOIN point
          ON actions_point = point_id";

  if($actions_type > 0){
    $sql .= " WHERE actions_type = ".$actions_type."";
  }
  //$sql .= " GROUP BY actions_id";
  //echo $sql;
  $query = mysqli_query($db, $sql);
  $return = array();
  $return2 = array();
  while($row = mysqli_fetch_array($query)){

    ////// check if this badge already got
    $badgegot = 0;
    $pointgot = 0;
    if($row['badge_stackable'] == 0 && $row['actions_badge'] != '-1'){
      $sql_count = "SELECT count(*) as count FROM users_badge
            WHERE users_badge_uid = '".$uid."'
            AND users_badge_badge = '".$row['badge_id']."'";
      $query_count = mysqli_query($db, $sql_count);
      $row_count = mysqli_fetch_array($query_count);

      //$badgegot = $row_count['count'];
      if($row_count['count'] == 0){
        $badgegot = -1;
      }
    }
    ////// check if this point already got
    if($row['actions_point'] != '-1'){
      $sql_count2 = "SELECT count(*) as count FROM users_point
            WHERE uid = '".$uid."'
            AND point_id = '".$row['actions_point']."'";
      $query_count2 = mysqli_query($db, $sql_count2);
      $row_count2 = mysqli_fetch_array($query_count2);

      $pointgot = $row_count2['count'];
      if($row_count2['count'] == 0){
        $pointgot = -1;
      }
    }

    if($badgegot == -1 || $pointgot == -1){
      $url = $row['conditions_api'].'?uid='.$uid;
      for($i=1;$i<=10;$i++){
        $actions_var[$i] = $row['conditions_label_type'.$i] == 4? strtotime($row['actions_var'.$i]) : $row['actions_var'.$i];
        if($row['conditions_var'.$i] != ""){
          $url .= '&'.$row['conditions_var'.$i].'='.$actions_var[$i];
        }
      }
      $header = array(
        'X-LIMP-SESSION:'.session_id()
      );

      $result = curlget($url,$header);

      $json = json_decode($result['result']);
      $decode_result = json_decode($result['result'],TRUE);
      $result['result'] = $decode_result;
      if($result['status'] == 200){
        $xx = json_encode($result['result']);
        if($xx == 'true'){
          if($badgegot == -1){
            array_push($return,$row['badge_id']);
          }
          if($pointgot == -1){
            array_push($return2,$row['point_id']);
          }

        }
      }else{
        //echo json_encode($result['result']);
      }
    }

  }
  //echo $sql_count2." ".$pointgot;
  //echo $url;
  //echo json_encode($return);
  //echo json_encode($return2);
  $join[0] = $return;
  $join[1] = $return2;
  //echo json_encode($join);
  return $join;
}


function updateUserBadge($uid,$badgelist){
  $db = connect_db();
  $return = true;
  foreach($badgelist as $val){
    $sql = "INSERT INTO users_badge SET
              users_badge_uid = '".$uid."',
              users_badge_badge = '".$val."',
              users_badge_status = '1'";

    $query = mysqli_query($db, $sql);
    if(!$query){
      $return = false;
    }
  }
  return $return;
}

function updateUserPoint($uid,$pointlist){
  $db = connect_db();
  $return = true;
  foreach($pointlist as $val){
    $sql = "INSERT INTO users_point SET
              uid = '".$uid."',
              point_id = '".$val."',
              status = '1'";

    $query = mysqli_query($db, $sql);
    if(!$query){
      $return = false;
    }
  }
  return $return;
}

//////****** Badge Condition ******

////////// ---- check has friend over $friend
function friends($uid, $friend = 0){

    $url = base_URL."friends/index.php?uid=".$uid;  //// get friend data from limpanzee db
    $header = array(
      'X-LIMP-SESSION:'.session_id()
    );
    $result = curlget($url,$header);
    //$result = json_encode($result);
    //print_r($result);
    if($result['result'] >= $friend){
      return true;
    }else{
      return false;
    }

}
////////// ---- check user's been at the location $count times between date $de and $ds
function location($uid, $location_id, $count = 0, $ds = 0, $de = 0){

    $url = base_URL."location/index.php?uid=".$uid."&l=".$location_id;  //// get location data from limpanzee db
    $header = array(
      'X-LIMP-SESSION:'.session_id()
    );
    $result = curlget($url,$header);
    $result['result'] = json_decode($result['result'],true);
    $tempcount = 0;
    foreach($result['result'] as $val){
      if($de !=0){
        $datetime = strtotime($val['datetime']);
        if($datetime >= $ds && $datetime <= $de){
          $tempcount++;
        }
      }else if($de ==0){
        $datetime = strtotime($val['datetime']);
        if($datetime >= $ds){
          $tempcount++;
        }
      }else{
        $tempcount++;
      }
    }
    if($tempcount >= $count){
      return true;
    }else{
      return false;
    }

}

////////// ---- event
function event($uid, $event = 0, $win = 0){

    $url = base_URL."event/index.php?uid=".$uid."&e=".$event."&w=".$win;
    $header = array(
      'X-LIMP-SESSION:'.session_id()
    );
    $result = curlget($url,$header);
    $result['result'] = json_decode($result['result'],true);
    $tempcount = count($result['result']);
    if($tempcount > 0){
      return true;
    }else{
      return false;
    }

}

////////// ---- role
function role($uid, $role = 0){

    $url = base_URL."role/index.php?uid=".$uid."&r=".$role;
    $header = array(
      'X-LIMP-SESSION:'.session_id()
    );
    $result = curlget($url,$header);
    $result['result'] = json_decode($result['result'],true);
    $tempcount = count($result['result']);
    if($tempcount > 0 && $role != 0){
      return true;
    }else{
      return false;
    }

}


//////****** Badge List ******
function badgelist($uid){
  $db = connect_db();
  $sql = "SELECT * FROM users_badge
        WHERE users_badge_uid = ".$uid."
        AND users_badge_status = 1";

  $query = mysqli_query($db, $sql);
  $result = array();
  while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){
    array_push($result, $row);
  }

  return $result;

}

function pointlist($uid){
  $db = connect_db();
  $sql = "SELECT * FROM users_point as u, point as p
        WHERE uid = ".$uid."
        AND u.point_id = p.point_id
        AND status = 1";

  $query = mysqli_query($db, $sql);
  $result = array();
  while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){
    array_push($result, $row);
  }

  return $result;

}

function pointsum($uid){
  $db = connect_db();
  $sql = "SELECT sum(point_point) as sum FROM users_point as u, point as p
        WHERE u.uid = ".$uid."
        AND u.point_id = p.point_id
        AND status = 1";

  $query = mysqli_query($db, $sql);
  $result = array();
  $row = mysqli_fetch_array($query,MYSQLI_ASSOC);

  return $row['sum'];

}

?>
