<?php

class Magneto_Debug_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Cleans Magento's cache
     *
     * @return void
     */
    public function cleanCache()
    {
        Mage::app()->cleanCache();
    }

    /**
     * Check if client's ip is whitelisted
     *
     * @return bool
     */
    function isRequestAllowed() {
        $isDebugEnable = (int)Mage::getStoreConfig('debug/options/enable');
        $clientIp = $this->_getRequest()->getClientIp();
        $allow = false;

        if( $isDebugEnable ){
            $allow = true;

            // Code copy-pasted from core/helper, isDevAllowed method 
            // I cannot use that method because the client ip is not always correct (e.g varnish)
            $allowedIps = Mage::getStoreConfig('dev/restrict/allow_ips');
            if ( $isDebugEnable && !empty($allowedIps) && !empty($clientIp)) {
                $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
                if (array_search($clientIp, $allowedIps) === false
                    && array_search(Mage::helper('core/http')->getHttpHost(), $allowedIps) === false) {
                    $allow = false;
                }
            }
        }

        return $allow;
    }

    /**
     * Return readable file size
     *
     * @param int $size size in bytes
     * 
     * @return string
     */
	function formatSize($size) {
		$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		if ($size == 0) {
			 return('n/a'); 
		} else {
			return ( round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); 
		}
	}
	
	public function getMemoryUsage(){
		return $this->formatSize( memory_get_peak_usage(TRUE) );
	}

	public function getScriptDuration(){
		if( function_exists('xdebug_time_index') ){
			return sprintf("%0.2f", xdebug_time_index() );
		} else {
			return 'n/a';
		}
	}
	
	public static function sortModelCmp($a, $b) {
		if($a['occurrences']==$b['occurrences'])
			return 0;
		return ($a['occurrences'] < $b['occurrences']) ? 1 : -1;
	}
	
	public function sortModelsByOccurrences(&$models) {
		usort($models, array('Magneto_Debug_Helper_Data', 'sortModelCmp'));
	}

    public function getBlockFilename($blockClass)
    {
        return mageFindClassFile($blockClass);
    }

    /**
     * Returns all xml files that contains layout updates.
     *
     * @param int|null $storeId store identifier
     *
     * @param $designArea
     * @return array
     */
    function getLayoutUpdatesFiles($storeId, $designArea) {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $updatesRoot = Mage::app()->getConfig()->getNode($designArea . '/layout/updates');

        // Find files with layout updates
        $updateFiles = array();
        foreach ($updatesRoot->children() as $updateNode) {
            if ($updateNode->file) {
                $module = $updateNode->getAttribute('module');
                if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, $storeId)) {
                    continue;
                }
                $updateFiles[] = (string)$updateNode->file;
            }
        }
        // custom local layout updates file - load always last
        $updateFiles[] = 'local.xml';

        return $updateFiles;
    }

    /**
     * Read last lines of file (able to read huge file)
     *
     * @param string        $file
     * @param int           $lines
     * @param null|string   $header
     *
     * @return array|int
     */
    public function getLastRows($file, $lines, $header = null)
    {
        // Number of lines read per time
        $bufferlength = 1024;
        $aliq = "";
        $line_arr = array();
        $tmp = array();
        $tmp2 = array();

        if (!($handle = fopen($file , "r"))) {
            return "Could not fopen $file";
        }

        if (!$handle) {
            return "Bad file handle";
        }

        // Get size of file
        fseek($handle, 0, SEEK_END);
        $filesize = ftell($handle);

        $position= - min($bufferlength,$filesize);

        while ($lines > 0) {
            if (fseek($handle, $position, SEEK_END)) {
                return "Could not fseek";
            }

            unset($buffer);
            $buffer = "";
            // Read some data starting fromt he end of the file
            if (!($buffer = fread($handle, $bufferlength))) {
                return "File is empty";
            }

            // Split by line
            $cnt = (count($tmp) - 1);
            for ($i = 0; $i < count($tmp); $i++ ) {
                unset($tmp[0]);
            }
            unset($tmp);
            $tmp = explode("\n", $buffer);

            // Handle case of partial previous line read
            if ($aliq != "") {
                $tmp[count($tmp) - 1] .= $aliq;
            }

            unset($aliq);
            // Take out the first line which may be partial
            $aliq = array_shift($tmp);
            $read = count($tmp);

            // Read too much (exceeded indicated lines to read)
            if ($read >= $lines) {
                // Slice off the lines we need and merge with current results
                unset($tmp2);
                $tmp2 = array_slice($tmp, $read - $lines);
                $line_arr = array_merge($tmp2, $line_arr);

                // Discard the header line if it is there
                if ($header &&
                    (count($line_arr) <= $lines)) {
                    array_shift($line_arr);
                }

                // Break the loop
                $lines = 0;
            }
            // Reached start of file
            elseif (-$position >= $filesize) {
                // Get back $aliq which contains the very first line of the file
                unset($tmp2);
                $tmp2[0] = $aliq;

                $line_arr = array_merge($tmp2, $tmp, $line_arr);

                // Discard the header line if it is there
                if ($header &&
                    (count($line_arr) <= $lines)) {
                    array_shift($line_arr);
                }

                // Break the loop
                $lines = 0;
            }
            // Continue reading
            else {
                // Add the freshly grabbed lines on top of the others
                $line_arr = array_merge($tmp, $line_arr);
                $lines -= $read;

                // No longer a full buffer's worth of data to read
                if ($position - $bufferlength < -$filesize) {
                    $bufferlength = $filesize + $position;
                    $position = -$filesize;
                }
                // Still >= $bufferlength worth of data to read
                else {
                    $position -= $bufferlength;
                }
            }
        }

        fclose($handle);

        return $line_arr;
    }


    /**
     * Read last lines of file (able to read huge file)
     *
     * @param string        $file
     * @param int           $lines
     * @param null|string   $header
     *
     * @return array|int
     */
    public function getLastRowsTime($file, $time)
    {
        // Number of lines read per time
        $header = null;
        $bufferlength = 1024;
        $aliq = "";
        $line_arr = array();
        $tmp = array();
        $tmp2 = array();
        $lines = 30;

        if (!($handle = fopen($file , "r"))) {
            return array("Could not fopen $file");
        }

        if (!$handle) {
            return array("Bad file handle");
        }

        // Get size of file
        fseek($handle, 0, SEEK_END);
        $filesize = ftell($handle);

        $position= - min($bufferlength,$filesize);

        while ($lines > 0) {
            if (fseek($handle, $position, SEEK_END)) {
                return array("Could not fseek");
            }

            unset($buffer);
            $buffer = "";
            // Read some data starting fromt he end of the file
            if (!($buffer = fread($handle, $bufferlength))) {
                return array("File is empty");
            }

            // Split by line
            $cnt = (count($tmp) - 1);
            for ($i = 0; $i < count($tmp); $i++ ) {
                unset($tmp[0]);
            }
            unset($tmp);
            $tmp = explode("\n", $buffer);

            // Handle case of partial previous line read
            if ($aliq != "") {
                $tmp[count($tmp) - 1] .= $aliq;
            }

            unset($aliq);
            // Take out the first line which may be partial
            $aliq = array_shift($tmp);
            $read = count($tmp);
            $tmp = $this->cleanPrefixLine($tmp);
            $tmp = $this->setRemoteCall($tmp);


            // Read too much (exceeded indicated lines to read)
            if ($read >= $lines) {
                // Slice off the lines we need and merge with current results
                unset($tmp2);
                $tmp2 = array_slice($tmp, $read - $lines);
                $line_arr = array_merge($tmp2, $line_arr);

                // Break the loop
                $lines = 0;
            }
            // Reached start of file
            elseif (-$position >= $filesize) {
                // Get back $aliq which contains the very first line of the file
                unset($tmp2);
                $tmp2[0] = $aliq;

                $line_arr = array_merge($tmp2, $tmp, $line_arr);

                // Break the loop
                $lines = 0;
            }
            // Continue reading
            else {
                // Add the freshly grabbed lines on top of the others
                $line_arr = array_merge($tmp, $line_arr);
                $lines -= $read;

                // No longer a full buffer's worth of data to read
                if ($position - $bufferlength < -$filesize) {
                    $bufferlength = $filesize + $position;
                    $position = -$filesize;
                }
                // Still >= $bufferlength worth of data to read
                else {
                    $position -= $bufferlength;
                }
            }
        }

        fclose($handle);

        return $line_arr;
    }

    public function cleanPrefixLine($string)
    {
        $string = preg_replace('/^(.+[a-z]+ \([0-9]{1}\):)/iUs','',$string);
        $string = str_replace('<','&lt;',$string);
        $string = str_replace('>','&gt;',$string);

        return $string;
    }
    public function setRemoteCall($string)
    {
        $string = preg_replace('/(Users\/guillaumedeneux\/Sites\/Magento\/)?(([a-z]+\/)+[a-z]+\.[a-z]+)[\(\[:]{0,1}([0-9]*)[\)\]]{0,1}/is','<a href="http://localhost:8091/?message=$1$2:$4"  onclick="var ajax = new XMLHttpRequest(); ajax.open(\'GET\', this.href); ajax.send(null); return false;">$2:$4</a>',$string);
        //set default value 0;
        $string = preg_replace('/(([a-z]+\/)+[a-z]+\.[a-z]+):(\s)/is','$1:0$3',$string);

        return $string;
    }

    public function isPanelVisible($panelTitle)
    {
        return Mage::getStoreConfig('debug/options/debug_panel_' . strtolower($panelTitle) . '_visibility');
    }
}