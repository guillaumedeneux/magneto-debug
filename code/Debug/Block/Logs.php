<?php

class Magneto_Debug_Block_Logs extends Magneto_Debug_Block_Abstract
{

    public function getLogs()
    {
        $logs = array();
        $files = array();
        $pathLogs = Mage::getBaseDir('var') . DS . 'log';

        if ($dir = opendir($pathLogs)) {
            while (false !== ($file = readdir($dir))) {
                if ($file != '.' && $file != '..' && substr($file, 0, 1) != '.') {
                    $files[filemtime($pathLogs . DS . $file)] = $file;
                }
            }
            closedir($dir);
        }
        // sort
        krsort($files);

        foreach ($files as $file) {
            $logs[$file] = Mage::helper('debug')->getLastRowsTime($pathLogs . DS . $file, 20);
        }


        return $logs;

    }
}
