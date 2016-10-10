<?php
use PHPUnit\Framework\TestCase;

class VoteAPI extends TestCase
{
    public function testGetVote_sql_id_pos()
    {
        $result = getVote_sql(1,"","","","");
        $this->assertEquals(" AND vote_id=1", $result);

    }

    public function testGetVote_sql_id_none()///list all
    {
        $result = getVote_sql("","","","","");
        $this->assertEquals("", $result);

    }
    //////
    public function testGetVote_sql_id_limit_pos()
    {
        $result = getVote_sql("",1,"","","");
        //echo $result;
        $this->assertEquals(" LIMIT 0, 1", $result);

    }
    public function testGetVote_sql_id_limit_neg()
    {
        $result = getVote_sql("",-1,"","","");
        $this->assertEquals(false, $result);

    }
    public function testGetVote_sql_id_limit_string()
    {
        $result = getVote_sql("","string","","","");
        $this->assertEquals(false, $result);

    }
    /////////
    public function testGetVote_sql_id_limit_sn_pos()
    {
        $result = getVote_sql("",1,1,"","");
        $this->assertEquals(" LIMIT 1, 1", $result);

    }
    public function testGetVote_sql_id_limit_sn_neg()
    {
        $result = getVote_sql("",1,-1,"","");
        $this->assertEquals(false, $result);

    }
    public function testGetVote_sql_id_limit_sn_string()
    {
        $result = getVote_sql("",1,"string","","");
        $this->assertEquals(false, $result);

    }

    //////
    public function testGetVote_sql_id_sort_d()
    {
        $result = getVote_sql("","","","","d");
        $this->assertEquals(" ORDER BY vote_date_start ASC", $result);

    }
    public function testGetVote_sql_id_sort_p()
    {
        $result = getVote_sql("","","","","p");
        $this->assertEquals(" ORDER BY vote_id ASC", $result);
    }
    public function testGetVote_sql_id_sort_other()
    {
        $result = getVote_sql("","","","","xxx");
        $this->assertEquals(false, $result);
    }
    //////
    public function testGetVote_sql_id_sort_sortby_d()
    {
        $result = getVote_sql("","","","d","d");
        $this->assertEquals(" ORDER BY vote_date_start DESC", $result);
    }
    public function testGetVote_sql_id_sort_sortby_a()
    {
        $result = getVote_sql("","","","a","d");
        $this->assertEquals(" ORDER BY vote_date_start ASC", $result);
    }
    public function testGetVote_sql_id_sort_sortby_other()
    {
        $result = getVote_sql("","","","XXX","d");
        $this->assertEquals(" ORDER BY vote_date_start ASC", $result);
    }

    //////
    public function testGetVote_sql_id_limit_pos_sort_d()
    {
        $result = getVote_sql("",1,"","","d");
        $this->assertEquals(" ORDER BY vote_date_start ASC LIMIT 0, 1", $result);
    }
    public function testGetVote_sql_id_limit_neg_sort_d()
    {
        $result = getVote_sql("",-1,"","","d");
        $this->assertEquals(false, $result);
    }
    public function testGetVote_sql_id_limit_pos_sort_p()
    {
        $result = getVote_sql("",1,"","","p");
        $this->assertEquals(" ORDER BY vote_id ASC LIMIT 0, 1", $result);
    }
    public function testGetVote_sql_id_limit_neg_sort_p()
    {
        $result = getVote_sql("",-1,"","","p");
        $this->assertEquals(false, $result);
    }
    public function testGetVote_sql_id_limit_pos_sort_other()
    {
        $result = getVote_sql("",1,"","","xxx");
        $this->assertEquals(false, $result);
    }
    /////
    public function testGetVote_sql_id_limit_pos_sn_pos_sort_d()
    {
        $result = getVote_sql("",1,1,"","d");
        $this->assertEquals(" ORDER BY vote_date_start ASC LIMIT 1, 1", $result);
    }
    public function testGetVote_sql_id_limit_pos_sn_neg_sort_d()
    {
        $result = getVote_sql("",1,-1,"","d");
        $this->assertEquals(false, $result);
    }
    public function testGetVote_sql_id_limit_neg_sn_neg_sort_d()
    {
        $result = getVote_sql("",-1,-1,"","d");
        $this->assertEquals(false, $result);
    }
    //////
    public function testGetVote_sql_id_limit_pos_sn_pos_sortby_d_sort_a()
    {
        $result = getVote_sql("",1,1,"a","d");
        $this->assertEquals(" ORDER BY vote_date_start ASC LIMIT 1, 1", $result);
    }
    public function testGetVote_sql_id_limit_pos_sn_pos_sortby_d_sort_d()
    {
        $result = getVote_sql("",1,1,"d","d");
        $this->assertEquals(" ORDER BY vote_date_start DESC LIMIT 1, 1", $result);
    }
    public function testGetVote_sql_id_limit_pos_sn_pos_sortby_d_sort_other()
    {
        $result = getVote_sql("",1,1,"xxx","d");
        $this->assertEquals(" ORDER BY vote_date_start ASC LIMIT 1, 1", $result);
    }

    //////////
    public function testGetVote_choice_pos()
    {
        $result = getVoteChoice(1);
        $expect = [
                    1 => ["chioce" => "Lower tuition fees and other costs","count" => "0"],
                    2 => ["chioce" => "Create lifelong learning opportunities for diverse populations","count" => "0"],
                    3 => ["chioce" => "Strengthen collaboration with the private sector", "count" => "0"]
                  ];
        //print_r($expect);
        //print_r($result);
        //$result = json_encode($result);
        $this->assertEquals($expect, $result);
    }
    public function testGetVote_choice_neg()
    {
        $result = getVoteChoice(-1);
        $this->assertEquals(false, $result);
    }
    public function testGetVote_choice_none()
    {
        $result = getVoteChoice("");
        $this->assertEquals(false, $result);
    }
    public function testGetVote_choice_notexist()
    {
        $result = getVoteChoice(1000000);
        $this->assertEquals(false, $result);
    }
    ///////
    public function testGetVote_count_pos(){
      $result = getVoteCount(1);
      $this->assertEquals(0, $result);
    }
    public function testGetVote_count_neg(){
      $result = getVoteCount(-1);
      $this->assertEquals(false, $result);
    }
    public function testGetVote_count_none(){
      $result = getVoteCount("");
      $this->assertEquals(false, $result);
    }
    public function testGetVote_count_not_exist(){
      $result = getVoteCount(10000000);
      $this->assertEquals(false, $result);
    }

}
?>
