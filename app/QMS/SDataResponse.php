<?php namespace App\QMS;

class SDataResponse
{
    public $lAnalysis;
    public $lResults;
    public $oLot;
    public $oItem;

    public function setAnalysis($var = null)
    {
        $this->lAnalysis = $var;
    }

    public function setResults($var = null)
    {
        $this->lResults = $var;
    }

    public function getAnalysis()
    {
        return $this->lAnalysis;
    }

    public function getResults()
    {
        return $this->lResults;
    }
}
