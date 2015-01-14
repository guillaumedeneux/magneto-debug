<?php
class Magneto_Debug_Block_Debug extends Magneto_Debug_Block_Abstract
{


	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function renderView()
    {
        // Render Debug toolbar only if allowed 
        if( Mage::helper('debug')->isRequestAllowed() ){
            return parent::renderView();
        } 
    }

    public function getVersion()
    {
        return (string)Mage::getConfig()->getNode('modules/Magneto_Debug/version');
    }
    
    private function createDummyPanel($title){
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => 'Subtitle for ' . $title,
            'content' => 'Some content for ' . $title,
            'template' => 'debug_versions_panel',
        );
        return $panel;
    }

    protected function createEventsPanel() {
        $title = 'Events';
        $content = '';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => 'Magento events observer',
            'template' => 'debug_events_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_events')
        );
        return $panel;
    }


    protected function createVersionsPanel() {
        $title = 'Versions';
        $content = '';
         $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => 'Magento modules',
            'template' => 'debug_versions_panel',           // child block defined in layout xml
             'order' => Mage::getStoreConfig('debug/order_options/debug_panel_versions')
         );
        return $panel;
    }    
	
	protected function createPerformancePanel() {
        $title = 'Performance';
		$helper = Mage::helper('debug');
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "TIME: {$helper->getScriptDuration()}s MEM: {$helper->getMemoryUsage()}",
            'template' => 'debug_performance_panel',
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_performance')
        );
        return $panel;
    } 

    protected function createConfigPanel() {
        $title = 'Configuration';
        $content = '';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Search configurations",
            'template' => 'debug_config_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_configuration')
        );
        return $panel;
    }

	 
    protected function createBlocksPanel() {
        $title = 'Blocks';
		$nBlocks = count(Mage::getSingleton('debug/observer')->getBlocks());
		
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "{$nBlocks} used blocks",
            'template' => 'debug_blocks_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_blocks')
        );
        return $panel;
    }
	
	
	protected function createLayoutPanel() {
        $title = 'Layout';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Layout handlers",
            'template' => 'debug_layout_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_layout')
        );
        return $panel;
    }
	
	protected function createControllerPanel() {
        $title = 'Controller';
        $content = '';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => 'Controller and request',
            'template' => 'debug_controller_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_controller')
        );
        return $panel;
    }   


    protected function createModelsPanel() {
        $title = 'Models';
		$nModels = count(Mage::getSingleton('debug/observer')->getModels());
		$nQueries = count(Mage::getSingleton('debug/observer')->getQueries());
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "{$nModels} models, {$nQueries} queries",
            'template' => 'debug_models_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_models')
        );
        return $panel;
    } 


    protected function createUtilsPanel() {
        $title = 'Utilities';
		
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Quick actions",
            'template' => 'debug_utils_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_utilities')
        );
        return $panel;
    }

    protected function createAnalyticsPanel() {
        $title = 'Analytics';

        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Magento vs custom",
            'template' => 'debug_analytics_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_analytics')
        );
        return $panel;
    }

    protected function createModulesPanel() {
        $title = 'Modules';

        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "View by modules",
            'template' => 'debug_modules_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_modules')
        );
        return $panel;
    }

    protected function createLogsPanel()
    {
        $title = 'Logs';

        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "View logs",
            'template' => 'debug_logs_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_logs')
        );
        return $panel;
    }

    protected function createPreferencesPanel()
    {
        $title = 'Preferences';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Customize Magneto Debug",
            'template' => 'debug_preferences_panel',           // child block defined in layout xml
            'order' => Mage::getStoreConfig('debug/order_options/debug_panel_preferences')
        );
        return $panel;
    }
    
    public function getPanels() {
        $panels = array();
        $panels[] = $this->createLogsPanel();
        $panels[] = $this->createAnalyticsPanel();
        $panels[] = $this->createEventsPanel();
        $panels[] = $this->createVersionsPanel();
		$panels[] = $this->createControllerPanel();
		$panels[] = $this->createModelsPanel();
		$panels[] = $this->createLayoutPanel();
        $panels[] = $this->createBlocksPanel();
        $panels[] = $this->createPerformancePanel();
        $panels[] = $this->createConfigPanel();
        $panels[] = $this->createUtilsPanel();
        $panels[] = $this->createModulesPanel();

        // TODO: Implement preferences panel (toggle panels visibility from toolbar)
//        $panels[] = $this->createPreferencesPanel();

        return $this->_sort($panels);
    }

    protected function _sort($panels){

        $newPanels = array();
        foreach ($panels as $panel) {
            $order = (isset($newPanels[$panel['order']]))? $panel['order']+1 : $panel['order'];
            $newPanels[$order] = $panel;
        }
        ksort($newPanels);
        return $newPanels;
    }
    public function getVisiblePanels()
    {
        /* @var $helper Magneto_Debug_Helper_Data */
        $helper = Mage::helper('debug');
        $panels = $this->getPanels();
        $visiblePanels = array();

        foreach ($panels as $panel) {
            if ($helper->isPanelVisible($panel['title'])){
                $visiblePanels[] = $panel;
            }
        }

        return $visiblePanels;
    }

    public function getDebugMediaUrl() {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/debug/';
    }
}
