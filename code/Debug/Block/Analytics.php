<?php

class Magneto_Debug_Block_Analytics extends Magneto_Debug_Block_Abstract
{

    public function getAnalytics()
    {
        if(!$this->hasData('analytics')){
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
            $this->setData('analytics',$result);
        }
        return $this->getData('analytics');
    }

    public function getResume($type)
    {
        $result = array();
        $result['mage']=0;
        $result['custom']=0;

        foreach ($this->getAnalytics() as $module => $analytics) {
            $isMage = preg_match('/^Mage_/',$module);

            if(array_key_exists($type,$analytics)){
                foreach ($analytics as $typeClass => $classes) {
                    if($type==$typeClass){
                        foreach ($classes as $class => $timer) {
                            if($isMage){
                             //   $result['mage']['value']+= number_format($timer, 2, '.', '');
                                $result['mage']+= number_format($timer, 2, '.', '');
                            }else{
                             //   $result['custom']['value']+= number_format($timer, 2, '.', '');
                                $result['custom']+= number_format($timer, 2, '.', '');
                            }
                        }
                    }
                }
            }
        }
     //   $result['mage']['percent'] = ceil($result['mage']['value']*100 / ($result['mage']['value'] + $result['custom']['value']));
       // $result['custom']['percent'] = floor($result['custom']['value']*100 / ($result['mage']['value'] + $result['custom']['value']));

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
        }elseif(preg_match('/^BLOCK\-DEBUG\:([a-z_]+)$/is',$name, $matches)){
            list($namespace,$module,$type,$subClass) = explode('_',$matches[1]);
            $result['module'] = $namespace.'_'.$module;
            $result['type'] =  'Block';
            $result['subClass'] =  (is_array($subClass))? implode('_',$subClass):$subClass;
/*        }elseif(preg_match('/^DISPATCH EVENT:/is',$name)){
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
            $result['subClass'] =  '';*/
        }else{
            $result['module'] = '';
            $result['type'] =  '';
            $result['subClass'] =  '';
        }
        return $result;
    }

}
