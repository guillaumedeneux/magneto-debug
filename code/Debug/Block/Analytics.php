<?php

class Magneto_Debug_Block_Analytics extends Magneto_Debug_Block_Abstract
{
    public function getList()
    {
        return array('block' => $this->__('Block'),
       //     'observer' => $this->__('Observer'),
            'queries' => $this->__('Queries')
        );
    }

    public function getResume($type)
    {
        $result = array();

        foreach ($this->getAnalytics() as $module => $analytics) {
            $isMage = preg_match('/^Mage_/', $module);

            if (array_key_exists($type, $analytics)) {
                foreach ($analytics as $typeClass => $classes) {
                    if ($type == $typeClass) {
                        foreach ($classes as $class => $timer) {

                            $valueMs = sprintf('%.3f', $timer) * 1000;

                            if ($valueMs === 0)
                                continue;

                            if ($isMage) {
                                if (!isset($result['Mage'])) {
                                    $result['Mage'] = $valueMs;
                                } else {
                                    $result['Mage'] += $valueMs;
                                }
                            } else {
                                if (!isset($result[$module])) {
                                    $result[$module] = $valueMs;
                                } else {
                                    $result[$module] += $valueMs;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;

    }

    public function getAnalytics()
    {
        if (!$this->hasData('analytics')) {
            $timers = Varien_Profiler::getTimers();
            $result = array();
            foreach ($timers as $name => $timer) {
                $sum = Varien_Profiler::fetch($name, 'sum');
                $count = Varien_Profiler::fetch($name, 'count');
                $realmem = Varien_Profiler::fetch($name, 'realmem');
                $emalloc = Varien_Profiler::fetch($name, 'emalloc');

                if ($sum < .001 && $count < 10 && $emalloc < 10000) {
                    continue;
                }
                $infos = $this->_getInfos($name);
                if($infos){
                    $result[$infos['module']][$infos['type']][$infos['subClass']] = $timer['sum'];
                }
            }
            if(Mage::getSingleton('core/resource')->getConnection('core_write')->getProfiler()){
                $result = array_merge($this->_getQueries(),$result);
            }

            $this->setData('analytics', $result);
        }
        return $this->getData('analytics');
    }

    protected function _getQueries()
    {
        $result = array();
        foreach (Mage::getSingleton('debug/observer')->getQueriesTime() as $query) {
            list($namespace, $module, $type, $subClass) = explode('_', $query['class']);
            $sSubClass = (is_array($subClass))? implode('_',$subClass) : $subClass;
            $result[$namespace.'_'.$module]['queries'][$sSubClass] = $query['time'];
        }

        return $result;
    }

    protected  function _getInfos($name)
    {
        $result = array();

        if (preg_match('/^BLOCK\-DEBUG\:([a-z_]+)$/is', $name, $matches)) {
            list($namespace, $module, $type, $subClass) = explode('_', $matches[1]);
            $result['module'] = $namespace . '_' . $module;
            $result['type'] = 'block';
            $result['subClass'] = (is_array($subClass)) ? implode('_', $subClass) : $subClass;
            /*        }elseif(preg_match('/^DISPATCH EVENT:/is',$name)){
                        $result['module'] = '';
                        $result['type'] =  'Model';
                        $result['subClass'] =  '';*/
      /*  } elseif (preg_match('/^OBSERVER:/is', $name)) {
            $result['module'] = '';
            $result['type'] = 'observer';
            $result['subClass'] = '';

         } elseif (preg_match('/^CORE::create_object_of::/is', $name)) {
            $part = explode('::', $name);
            $class = $part[2];
            list($namespace, $module, $type, $subClass) = explode('_', $class);
            $result['module'] = $namespace . '_' . $module;
            $result['type'] = 'model';
            $result['subClass'] = (is_array($subClass)) ? implode('_', $subClass) : $subClass; */
        }

        return $result;
    }

}
