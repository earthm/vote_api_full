<?php

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
        die("Connection failed: " . mysql_connect_error());
    }
    return $conn;
}
//////additional
function getVote_sql($id,$limit,$sn,$sort,$sortby){
  $sql_l = '';
  $error = true;
  if($id != ''){
    $sql_l = ' AND vote_id='.$id;
  }else{

      if($sortby != ''){
        if($sortby == 'd' || $sortby == 'p'){
          $sql_l .= ' ORDER BY ';
          if($sortby == 'd'){
            $sql_l .= 'vote_date_start';
          }else if($sortby == 'p'){
            $sql_l .= 'vote_id';
          }
        }else{
          $error = false;
        }

          if($sort == 'd'){
            $sql_l .= " DESC";
          }else{
            $sql_l  .= " ASC";
          }


      }

    if($limit > 0){
      $sql_l .= " LIMIT ";
      if($sn > 0){
        $sql_l .= $sn .", ";
      }else if($sn == ""){
        $sql_l .= "0, ";

      }else{
        $error = false;
      }
      if($limit > 0){
        $sql_l .= $limit;
      }else{
        $sql_l .= 0;
      }
    }else if($limit == ""){
      $sql_l .= "";
    }else{
      $error = false;
    }
  }
  if($error == false){
    return $error;
  }else{
    return $sql_l;
  }
}
function getVoteChoice($vote_id){
  if($vote_id != ''){
    $db = connect_db();
    $sql1 = "SELECT * FROM limpanzee_votepolls.vote_choice WHERE vote_id = ".$vote_id;
    $query1 = mysqli_query($db, $sql1);
    $ii = 1;
    if(mysqli_num_rows($query1)){
      while($row1 = mysqli_fetch_array($query1,MYSQLI_ASSOC)){
        $vote[$ii]['chioce'] = $row1['vote_choice'];
        $vote[$ii]['count'] = $row1['vote_count'];
        $ii++;
      }
      return $vote;
    }else{
      return false;
    }
  }else{
    return false;
  }
}
function getVoteCount($vote_id){
  if($vote_id < 0 || $vote_id == ""){
    return false;
  }
  $db = connect_db();
  $sql2 = "SELECT sum(vote_count) as vote_count FROM limpanzee_votepolls.vote_choice WHERE vote_id = ".$vote_id;
  $query2 = mysqli_query($db, $sql2);
  $row2 = mysqli_fetch_array($query2,MYSQLI_ASSOC);
  $vote = $row2['vote_count'];
  return $vote;
}
function getVoteTags1($vote_id){
  $db = connect_db();
  $vote = [];
  $sql_tag = "SELECT * FROM limpanzee_votepolls.vote_tag WHERE vote_id = '".$vote_id."'";
  //echo $sql_tag;
  $query_tag = mysqli_query($db, $sql_tag);
  while($row_tag = mysqli_fetch_array($query_tag,MYSQLI_ASSOC)){
    if($row_tag['vote_tag_type'] == 'i1'){
      $sql_tag_list = "SELECT * FROM limpanzee_systemtags.i_tagmain WHERE main_id = '".$row_tag['tag_id']."'";
      $query_tag_list = mysqli_query($db, $sql_tag_list);
      $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
      $vote['news_tag_i_main'] = $row_tag_list['main_tagname'];
    }else if($row_tag['vote_tag_type'] == 'i2'){
      $sql_tag_list = "SELECT * FROM limpanzee_systemtags.i_taglesson WHERE lesson_id = '".$row_tag['tag_id']."'";
      $query_tag_list = mysqli_query($db, $sql_tag_list);
      $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
      $vote['news_tag_i_lesson'] = $row_tag_list['lesson_tagname'];
    }else if($row_tag['vote_tag_type'] == 'i3'){
      $sql_tag_list = "SELECT * FROM limpanzee_systemtags.i_tagtopic WHERE topic_id = '".$row_tag['tag_id']."'";
      $query_tag_list = mysqli_query($db, $sql_tag_list);
      $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
      $vote['news_tag_i_topic'] = $row_tag_list['topic_tagname'];
    }else if($row_tag['vote_tag_type'] == 'i4'){
      $sql_tag_list = "SELECT * FROM limpanzee_systemtags.i_tagsubtopic WHERE subtopic_id = '".$row_tag['tag_id']."'";
      $query_tag_list = mysqli_query($db, $sql_tag_list);
      $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
      $vote['news_tag_i_subtopic'] = $row_tag_list['subtopic_tagname'];
    }else if($row_tag['vote_tag_type'] == 'o1'){
      $sql_tag_list = "SELECT * FROM limpanzee_systemtags.o_tagmain WHERE main_id = '".$row_tag['tag_id']."'";
      $query_tag_list = mysqli_query($db, $sql_tag_list);
      $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
      $vote['news_tag_o_main'] = $row_tag_list['main_tagname'];
    }else if($row_tag['vote_tag_type'] == 'o2'){
      $sql_tag_list = "SELECT * FROM limpanzee_systemtags.o_taglesson WHERE lesson_id = '".$row_tag['tag_id']."'";
      $query_tag_list = mysqli_query($db, $sql_tag_list);
      $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
      $vote['news_tag_o_lesson'] = $row_tag_list['lesson_tagname'];
    }else if($row_tag['vote_tag_type'] == 'o3'){
      $sql_tag_list = "SELECT * FROM limpanzee_systemtags.o_tagtopic WHERE topic_id = '".$row_tag['tag_id']."'";
      $query_tag_list = mysqli_query($db, $sql_tag_list);
      $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
      $vote['news_tag_o_topic'] = $row_tag_list['topic_tagname'];
    }else if($row_tag['vote_tag_type'] == 'o4'){
      $sql_tag_list = "SELECT * FROM limpanzee_systemtags.o_tagsubtopic WHERE subtopic_id = '".$row_tag['tag_id']."'";
      $query_tag_list = mysqli_query($db, $sql_tag_list);
      $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
      $vote['news_tag_o_subtopic'] = $row_tag_list['subtopic_tagname'];
    }
  }
  if(empty($vote)){
    return "";
  }else{
    return $vote;
  }

}
//////
function getvote($id,$limit,$sn,$sort,$sortby){
    $db = connect_db();
    $vote = [];
    $sql_l = getVote_sql($id,$limit,$sn,$sort,$sortby);

    $sql = "SELECT * FROM limpanzee_votepolls.vote,limpanzee.profile WHERE vote_author = uid ".$sql_l;

    //echo $sql;
    $query = mysqli_query($db, $sql);
    $i = 0;
    while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){

      $vote[$i]['vote_id'] = $row['vote_id'];
      $vote[$i]['vote_topic'] = $row['vote_topic'];
      $vote[$i]['vote_intro'] = $row['vote_intro'];
      $vote[$i]['vote_author'] = $row['vote_author'];
      $vote[$i]['vote_type'] = $row['vote_type'];
      $vote[$i]['vote_tag_type'] = $row['vote_tag_type'];
      $vote[$i]['vote_date_start'] = $row['vote_date_start'];
      $vote[$i]['vote_date_end'] = $row['vote_date_end'];
      $vote[$i]['vote_time_start'] = $row['vote_time_start'];
      $vote[$i]['vote_time_end'] = $row['vote_time_end'];
      $vote[$i]['vote_original_image'] = S3BUCKET."/VotesPolls/".$row['vote_original_image'];
      $vote[$i]['vote_cover1'] = S3BUCKET."/VotesPolls/".$row['vote_cover1'];
      $vote[$i]['vote_author_screenName'] = $row['screen_name'];
      $vote[$i]['vote_author_accountName'] = $row['nick'];
      $vote[$i]['vote_author_avatar'] = S3BUCKET.'/Profile/wabox/'.$row['avatar'];
      $vote[$i]['vote_created'] = $row['vote_created'];

      /////
      $vote[$i]['vote_choice'] = getVoteChoice($row['vote_id']);

      ///////
      $vote[$i]['vote_sum_count'] =  getVoteCount($row['vote_id']);

      $header = array(
    		'X-LIMP-SESSION:'.session_id()
    	);


      ///tag

     $vote[$i]['vote_tags'] = getVoteTags1($row['vote_id']);


      $i++;
    }
    if(empty($vote)){
      return false;
    }else{
      return json_encode($vote,JSON_UNESCAPED_SLASHES);
    }
}
/*function getvote($id,$limit,$sn,$sort,$sortby){
    $db = connect_db();
    $vote = [];
    $sql_l = '';
    if($id != ''){
      $sql_l = ' AND vote_id='.$id;
    }else{

      if($sortby != ''){
        $sql_l .= ' ORDER BY ';
        if($sortby == 'd'){
          $sql_l .= 'vote_date_start';
        }else if($sortby == 'p'){
        }
        if($sort == 'd'){
          $sql_l .= " DESC";
        }else{
          $sql_l  .= " ASC";
        }
      }
      if($limit != -1){
        $sql_l .= " LIMIT ";
        if($sn != ''){
          $sql_l .= $sn .", ";
        }else{
          $sql_l .= "0, ";
        }
        if($limit != -1){
          $sql_l .= $limit;
        }else{
          $sql_l .= 0;
        }
      }
    }
    $sql = "SELECT * FROM limpanzee_votepolls.vote,limpanzee.profile WHERE vote_author = uid ".$sql_l;

    //echo $sql;
    $query = mysqli_query($db, $sql);
    $i = 0;
    while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){

      $vote[$i]['vote_id'] = $row['vote_id'];
      $vote[$i]['vote_topic'] = $row['vote_topic'];
      $vote[$i]['vote_intro'] = $row['vote_intro'];
      $vote[$i]['vote_author'] = $row['vote_author'];
      $vote[$i]['vote_type'] = $row['vote_type'];
      $vote[$i]['vote_tag_type'] = $row['vote_tag_type'];
      $vote[$i]['vote_date_start'] = $row['vote_date_start'];
      $vote[$i]['vote_date_end'] = $row['vote_date_end'];
      $vote[$i]['vote_time_start'] = $row['vote_time_start'];
      $vote[$i]['vote_time_end'] = $row['vote_time_end'];
      $vote[$i]['vote_original_image'] = S3BUCKET."/VotesPolls/".$row['vote_original_image'];
      $vote[$i]['vote_cover1'] = S3BUCKET."/VotesPolls/".$row['vote_cover1'];
      $vote[$i]['vote_author_screenName'] = $row['screen_name'];
      $vote[$i]['vote_author_accountName'] = $row['nick'];
      $vote[$i]['vote_author_avatar'] = S3BUCKET.'/Profile/wabox/'.$row['avatar'];
      $vote[$i]['vote_created'] = $row['vote_created'];

      ////// vote chioce

      $sql1 = "SELECT * FROM limpanzee_votepolls.vote_choice WHERE vote_id = ".$row['vote_id'];
      $query1 = mysqli_query($db, $sql1);
      $ii = 1;
      while($row1 = mysqli_fetch_array($query1,MYSQLI_ASSOC)){
        $vote[$i]['vote_choice'][$ii]['chioce'] = $row1['vote_choice'];
        $vote[$i]['vote_choice'][$ii]['count'] = $row1['vote_count'];
        $ii++;
      }
      ///////
      $sql2 = "SELECT sum(vote_count) as vote_count FROM limpanzee_votepolls.vote_choice WHERE vote_id = ".$row['vote_id'];
      $query2 = mysqli_query($db, $sql2);
      $row2 = mysqli_fetch_array($query2,MYSQLI_ASSOC);
      $vote[$i]['vote_sum_count'] = $row2['vote_count'];
      $header = array(
    		'X-LIMP-SESSION:'.session_id()
    	);


      ///tag
      $sql_tag = "SELECT * FROM limpanzee_votepolls.vote_tag WHERE vote_id = '".$row['vote_id']."'";
      //echo $sql_tag;
      $query_tag = mysqli_query($db, $sql_tag);
      while($row_tag = mysqli_fetch_array($query_tag,MYSQLI_ASSOC)){
        if($row_tag['vote_tag_type'] == 'i1'){
          $sql_tag_list = "SELECT * FROM limpanzee_systemtags.i_tagmain WHERE main_id = '".$row_tag['tag_id']."'";
          $query_tag_list = mysqli_query($db, $sql_tag_list);
          $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
          $vote[$i]['news_tag_i_main'] = $row_tag_list['main_tagname'];
        }else if($row_tag['vote_tag_type'] == 'i2'){
          $sql_tag_list = "SELECT * FROM limpanzee_systemtags.i_taglesson WHERE lesson_id = '".$row_tag['tag_id']."'";
          $query_tag_list = mysqli_query($db, $sql_tag_list);
          $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
          $vote[$i]['news_tag_i_lesson'] = $row_tag_list['lesson_tagname'];
        }else if($row_tag['vote_tag_type'] == 'i3'){
          $sql_tag_list = "SELECT * FROM limpanzee_systemtags.i_tagtopic WHERE topic_id = '".$row_tag['tag_id']."'";
          $query_tag_list = mysqli_query($db, $sql_tag_list);
          $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
          $vote[$i]['news_tag_i_topic'] = $row_tag_list['topic_tagname'];
        }else if($row_tag['vote_tag_type'] == 'i4'){
          $sql_tag_list = "SELECT * FROM limpanzee_systemtags.i_tagsubtopic WHERE subtopic_id = '".$row_tag['tag_id']."'";
          $query_tag_list = mysqli_query($db, $sql_tag_list);
          $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
          $vote[$i]['news_tag_i_subtopic'] = $row_tag_list['subtopic_tagname'];
        }else if($row_tag['vote_tag_type'] == 'o1'){
          $sql_tag_list = "SELECT * FROM limpanzee_systemtags.o_tagmain WHERE main_id = '".$row_tag['tag_id']."'";
          $query_tag_list = mysqli_query($db, $sql_tag_list);
          $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
          $vote[$i]['news_tag_o_main'] = $row_tag_list['main_tagname'];
        }else if($row_tag['vote_tag_type'] == 'o2'){
          $sql_tag_list = "SELECT * FROM limpanzee_systemtags.o_taglesson WHERE lesson_id = '".$row_tag['tag_id']."'";
          $query_tag_list = mysqli_query($db, $sql_tag_list);
          $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
          $vote[$i]['news_tag_o_lesson'] = $row_tag_list['lesson_tagname'];
        }else if($row_tag['vote_tag_type'] == 'o3'){
          $sql_tag_list = "SELECT * FROM limpanzee_systemtags.o_tagtopic WHERE topic_id = '".$row_tag['tag_id']."'";
          $query_tag_list = mysqli_query($db, $sql_tag_list);
          $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
          $vote[$i]['news_tag_o_topic'] = $row_tag_list['topic_tagname'];
        }else if($row_tag['vote_tag_type'] == 'o4'){
          $sql_tag_list = "SELECT * FROM limpanzee_systemtags.o_tagsubtopic WHERE subtopic_id = '".$row_tag['tag_id']."'";
          $query_tag_list = mysqli_query($db, $sql_tag_list);
          $row_tag_list = mysqli_fetch_array($query_tag_list,MYSQLI_ASSOC);
          $vote[$i]['news_tag_o_subtopic'] = $row_tag_list['subtopic_tagname'];
        }
      }
      $i++;
    }
    return json_encode($vote);
}
*/
function insertvote($pb){
  $db = connect_db();
  $post = $pb;
    $year = date('Y')+543;
    $vote_topic = isset($post['vote_topic'])? $post['vote_topic'] : '';
    $vote_intro = isset($post['vote_intro'])? $post['vote_intro'] : '';
    $vote_author = isset($post['vote_author'])? $post['vote_author'] : '';
    $vote_original_image = isset($post['vote_original_image'])? $post['vote_original_image'] : '';
    $vote_cover1 = isset($post['vote_cover1'])? $post['vote_cover1'] : '';
    $vote_type = isset($post['vote_type'])? $post['vote_type'] : '';
    $vote_tag_type = isset($post['vote_tag_type'])? $post['vote_tag_type'] : '';
    $vote_date_start = isset($post['vote_date_start'])? $post['vote_date_start'] : '';
    $vote_date_end = isset($post['vote_date_end'])? $post['vote_date_end'] : '';
    $vote_time_start = isset($post['vote_time_start'])? $post['vote_time_start'] : '';
    $vote_time_end = isset($post['vote_time_end'])? $post['vote_time_end'] : '';
    $vote_show_resultaftervote = isset($post['vote_show_resultaftervote'])? $post['vote_show_resultaftervote'] : '';
    $vote_show_resultafterend = isset($post['vote_show_resultafterend'])? $post['vote_show_resultafterend'] : '';

    $vote_topic = str_replace("'",'&apos;',$vote_topic);
    $sql = "INSERT INTO limpanzee_votepolls.vote SET
              vote_topic = '".$vote_topic."',
              vote_intro = '".$vote_intro."',
              vote_author = '".$vote_author."',
              vote_original_image = '".$vote_original_image."',
              vote_cover1 = '".$vote_cover1."',
              vote_type = '".$vote_type."',
              vote_tag_type = '".$vote_tag_type."',
              vote_date_start = '".$vote_date_start."',
              vote_date_end = '".$vote_date_end."',
              vote_time_start = '".$vote_time_start."',
              vote_time_end = '".$vote_time_end."',
              vote_show_resultaftervote = '".$vote_show_resultaftervote."',
              vote_show_resultafterend = '".$vote_show_resultafterend."',
              vote_created = NOW(),
              vote_status = '1'
              ";

    $query = mysqli_query($db, $sql);

    $return['result'] = $query;

    $insert_id = mysqli_insert_id($db);

    for($i=1;$i<=8;$i++){
      if(isset($post['vote_choice'.$i]) && $post['vote_choice'.$i] != ""){
        $sql2 = "INSERT INTO limpanzee_votepolls.vote_choice SET
                                          vote_id = '".$insert_id."',
                                          vote_choice = '".$post['vote_choice'.$i]."'
                                          ";
        $query2 = mysqli_query($db, $sql2);
        $return['result2'] = $query2;
      }
    }


    $return['key'] = $insert_id;

    return json_encode($return);
}

function submitVote($pb){
  $db = connect_db();
  $post = $pb;
    $vote_id = isset($post['vote_id'])? $post['vote_id'] : '';
    $vote_uid = isset($post['vote_uid'])? $post['vote_uid'] : '';
    $alreadyVote = alreadyVote($vote_id,$vote_uid);
    $alreadyVote = json_decode($alreadyVote,true);
    if($alreadyVote['result'] == 0){
      $sql = "INSERT INTO limpanzee_votepolls.vote_member_list SET
                vote_id = '".$vote_id."',
                vote_uid = '".$vote_uid."',
                vote_timestamp = NOW()
                ";

      $query = mysqli_query($db, $sql);

      $return['result'] = $query;

      $insert_id = mysqli_insert_id($db);
      $return['key'] = $insert_id;
    }else{
      $return['result'] = false;
      $return['key'] = "already vote";
    }

    return json_encode($return);
}

function alreadyVote($vote_id,$vote_uid){
  $db = connect_db();
    $sql = "SELECT count(*) as count FROM limpanzee_votepolls.vote_member_list WHERE
              vote_id = '".$vote_id."' AND
              vote_uid = '".$vote_uid."'
              ";

    $query = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($query,MYSQLI_ASSOC);
    $return['result'] = $row['count'];
    return json_encode($return);
}

function hashtagInsert($pb){
  $db = connect_db();
  $post = $pb;
    $vote_id = isset($post['vote_id'])? $post['vote_id'] : '';
    $hashtag = isset($post['hashtag'])? $post['hashtag'] : '';

    $sql1 = "SELECT count(*) as count, ht_count, ht_id  FROM limpanzee_systemtags.ht_tag WHERE
              ht_name = '".$hashtag."' AND
              tagType = 'voteht'
              ";

    $query1 = mysqli_query($db, $sql1);
    $row = mysqli_fetch_array($query1,MYSQLI_ASSOC);


    if($row['count'] == 0){
      $sql = "INSERT INTO limpanzee_systemtags.ht_tag SET
              ht_name = '".$hashtag."',
              ht_count = '1',
              tagType = 'voteht',
              created_date = NOW()
              ";
      $query = mysqli_query($db, $sql);

      $return['result'] = $query;
      $insert_id = mysqli_insert_id($db);

    }else{
      $sql = "UPDATE limpanzee_systemtags.ht_tag SET
              ht_count = ht_count + 1
              WHERE ht_name = '".$hashtag."' AND tagType = 'voteht'
              ";
      $query = mysqli_query($db, $sql);

      $return['result'] = $query;
      $insert_id = $row['ht_id'];
    }


    $return['key'] = $insert_id;

    $sql2 = "INSERT INTO limpanzee_votepolls.vote_tag SET
            vote_id = '".$vote_id."',
            tag_id = '".$insert_id."',
            vote_tag_type = 'ht'
            ";
    $query2 = mysqli_query($db, $sql2);

    return json_encode($return);
}

function getVoteTags($vote_id){
  $db = connect_db();
    $sql = "SELECT * FROM limpanzee_votepolls.vote_tag, limpanzee_systemtags.ht_tag
            WHERE vote_tag.tag_id = ht_tag.ht_id
            AND vote_id = '".$vote_id."'
              ";

    $query = mysqli_query($db, $sql);
    $i = 0;

    while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){
      $vote_tag_type = $row['vote_tag_type'];
      if($vote_tag_type == 'ht'){
        $return['result']['tag_type'] = 'ht';
      }else if($vote_tag_type == 'i1' || $vote_tag_type == 'i2' || $vote_tag_type == 'i3' || $vote_tag_type == 'i4'){
        $return['result']['tag_type'] = 'i';
      }else if($vote_tag_type == 'o1' || $vote_tag_type == 'o2' || $vote_tag_type == 'o3' || $vote_tag_type == 'o4'){
        $return['result']['tag_type'] = 'o';
      }

      $return['result']['tags_list'][$i]['vote_tag_type'] = $row['vote_tag_type'];
      $return['result']['tags_list'][$i]['vote_id'] = $row['vote_id'];
      $return['result']['tags_list'][$i]['tag_id'] = $row['tag_id'];
      $return['result']['tags_list'][$i]['ht_name'] = $row['ht_name'];
      $i++;
    }
    return json_encode($return);
}


?>
