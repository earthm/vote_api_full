<?php
use PHPUnit\Framework\TestCase;

class VoteAPIIntegration extends TestCase
{

    public function testGetVote_sql_id_pos()
    {
        $result = getVote(1,"","","","");
        $result = substr($result, 1, strlen($result)-2);
        $this->assertJsonStringEqualsJsonFile('output/getvote.json',$result);
    }
    public function testGetVote_sql_id_neg()
    {
        $result = getVote(-1,"","","","");
        $this->assertEquals(false, $result);
    }
    public function testGetVote_sql_id_notexist()
    {
        $result = getVote(1000000,"","","","");
        $this->assertEquals(false, $result);
    }
}
?>
