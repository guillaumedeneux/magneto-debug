<?php
/**
 * Created by Guillaume Deneux.
 * Date: 05/12/14
 */ 
class Magneto_Debug_Model_Metrics extends Mage_Core_Model_Abstract
{
    private $_timers = array();
    private $_average = false;
    private $_deviation = false;

    const PERFECT = 1;
    const VERYGOOD = 2;
    const GOOD = 3;
    const MIDDLE = 4;
    const BAD = 5;
    const VERYBAD = 6;

    protected $_metricsTitle = array(
        self::PERFECT=>'There\'s no quicker way.',
        self::VERYGOOD=>'Very Fast',
        self::GOOD=>'Fast',
        self::MIDDLE=>'Middle',
        self::BAD=>'Low',
        self::VERYBAD=>'Very low'
    );

    //Utilisation des écarts types
    public function getLevel($value){
        $result = array();

        if($this->_deviation===false || $this->_average===false){
            $this->_deviation = $this->getEcartType($this->_timers,5);
            $this->_average = $this->getAverage($this->_timers);
        }

        $minDeviation = $this->_average + -4*$this->_deviation;
        if($minDeviation<0){
            $minDeviation*=-1;
        }
        $minvalue = $minDeviation + $value;

        foreach(array(-2,-1,0,1,2) as $i){

            $level = $i + 3;
            $cal = $this->_average + $i*$this->_deviation + $minDeviation;

            if($minvalue<$cal){
                $result = array('title'=>$this->_metricsTitle[$level], 'level'=>$level);
                break;
            }
        }
        if(!$result){
            $result = array('title'=>$this->_metricsTitle[self::VERYBAD], 'level'=>self::VERYBAD);
        }

        return $result;
    }

    public function setTime($values){
        $this->_timers = $values;
    }

    public function resetTime(){
        $this->_timers = array();
    }

    public function addTime($value){
        $this->_timers[] = $value;
    }

    public function getEcartType($array, $nbdecimals=2) {

        if (is_array($array))
        {
            //moyenne des valeurs
            reset($array);
            $somme=0;
            $nbelement=count($array);
            foreach ($array as $value) {
                $somme += floatval($value);
            }
            $moyenne = $somme/$nbelement;
            //numerateur
            reset($array);
            $sigma=0;
            foreach ($array as $value) {
                $sigma += pow((floatval($value)-$moyenne),2);
            }
            //denominateur = $nbelement
            $ecarttype = sqrt($sigma/$nbelement);
            return number_format($ecarttype, $nbdecimals);
        }
        else
        {
            return false;
        }
    }

    public function getAverage($values){
        return array_sum($values)/count($values);
    }

}