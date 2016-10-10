<?php
class VoteApi
{
    public function getVote()
    {
        return $this->amount;
    }

    public function negate()
    {
        return new Money(-1 * $this->amount);
    }
}
?>
