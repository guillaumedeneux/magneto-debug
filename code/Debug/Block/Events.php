<?php
class Magneto_Debug_Block_Events extends Magneto_Debug_Block_Abstract
{
    public function getEvents() {
        $events = Varien_Profiler::getTimers();
        $result = array();
        $i=0;
        foreach ($events as $name => $event) {
            if(preg_match('/^dispatch/iUs',$name)){
                $i++;
                $result[$i]['event'] = $name;
            }
            if(preg_match('/^observer/iUs',$name)){
                $result[$i]['observers'][] = $name;
            }

        }

        return $result;
    }
}

