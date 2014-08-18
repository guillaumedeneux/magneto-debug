<?php

class Magneto_Debug_Block_Analytics extends Magneto_Debug_Block_Abstract
{

    public function getAnalytics(){
        $timers = Varien_Profiler::getTimers();
        $result = array();
        $timersTotal = 0;
        foreach ($timers as $name=>$timer) {
            $sum = Varien_Profiler::fetch($name,'sum');
            $count = Varien_Profiler::fetch($name,'count');
            $realmem = Varien_Profiler::fetch($name,'realmem');
            $emalloc = Varien_Profiler::fetch($name,'emalloc');

            if ($sum<.0010 && $count<10 && $emalloc<10000) {
                continue;
            }
            $infos = $this->_getInfos($name);
            $result[$infos['module']][$infos['type']][$infos['subClass']] = $timer['sum'];
            $timersTotal += $timer['sum'];

        }
        return $result;
    }
    public function _getInfos($name){
        $result = array();

        if(preg_match('/^CORE::create_object_of::/is',$name)){
            $part = explode('::',$name);
            $class = $part[2];
            list($namespace,$module,$type,$subClass) = explode('_',$class);
            $result['module'] = $namespace.'_'.$module;
            $result['type'] = 'Model';
            $result['subClass'] = (is_array($subClass))? implode('_',$subClass):$subClass;
        }elseif(preg_match('/^BLOCK:/is',$name)){
            $result['module'] = '';
            $result['type'] =  'Block';
            $result['subClass'] =  '';
        }elseif(preg_match('/^DISPATCH EVENT:/is',$name)){
            $result['module'] = '';
            $result['type'] =  'Model';
            $result['subClass'] =  '';
        }elseif(preg_match('/^OBSERVER:/is',$name)){
            $result['module'] = '';
            $result['type'] =  'Model';
            $result['subClass'] =  '';
        }elseif(preg_match('/^\.phtml$/is',$name)){
            preg_match('/template\/(.)+$/is',$name,$matches);
            $part = explode('/',$matches[1]);
            $result['module'] = '';
            $result['type'] =  'Template';
            $result['subClass'] =  '';
        }else{
            $result['module'] = '';
            $result['type'] =  '';
            $result['subClass'] =  '';

        }
        return $result;
    }

}
