<?php

/**
 * The home manager controller for modtelegram.
 *
 */
class modtelegramHomeManagerController extends modtelegramMainController
{
    /* @var modtelegram $modtelegram */
    public $modtelegram;


    /**
     * @param array $scriptProperties
     */
    public function process(array $scriptProperties = array())
    {
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('modtelegram');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->modtelegram->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->modtelegram->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->modtelegram->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->modtelegram->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->modtelegram->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->modtelegram->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->modtelegram->config['jsUrl'] . 'mgr/sections/home.js');
        $this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "modtelegram-page-home"});
		});
		</script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->modtelegram->config['templatesPath'] . 'home.tpl';
    }
}