<?php
use PHPUnit\Framework\TestCase;

class VipAPI extends TestCase
{
    public function testGetVote_id_pos()
    {
        $result = getvote(1,"","","","");
        $result = json_decode($result,true);
        $this->assertEquals(200, $result['status']);

    }
    public function testGetVote_id_neg()
    {
        $result = getvote(-22,"","","","");
        $result = json_decode($result,true);
        $this->assertEquals(400, $result['status']);

    }
    public function testGetVote_id_notexist()
    {
        $result = getvote(100,"","","","");
        $result = json_decode($result,true);
        $this->assertEquals(404, $result['status']);

    }
    public function testGetVote_id_string()
    {
        $result = getvote("string","","","","");
        $result = json_decode($result,true);
        $this->assertEquals(400, $result['status']);

    }
    public function testGetVote_id_none()
    {
        $result = getvote("","","","","");
        $result = json_decode($result,true);
        $this->assertEquals(400, $result['status']);

    }
    public function testGetVote_id_listall()
    {
        $result = getvote(-1,"","","","");
        $result = json_decode($result,true);
        $this->assertEquals(200, $result['status']);

    }
}
?>
