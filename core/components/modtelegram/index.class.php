<?php

/**
 * Class modtelegramMainController
 */
abstract class modtelegramMainController extends modExtraManagerController
{
    /** @var modtelegram $modtelegram */
    public $modtelegram;


    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('modtelegram_core_path', null,
            $this->modx->getOption('core_path') . 'components/modtelegram/');
        require_once $corePath . 'model/modtelegram/modtelegram.class.php';

        $this->modtelegram = new modtelegram($this->modx);
        $this->addCss($this->modtelegram->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->modtelegram->config['jsUrl'] . 'mgr/modtelegram.js');
        $this->addHtml('
		<script type="text/javascript">
			modtelegram.config = ' . $this->modx->toJSON($this->modtelegram->config) . ';
			modtelegram.config.connector_url = "' . $this->modtelegram->config['connectorUrl'] . '";
		</script>
		');

        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('modtelegram:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends modtelegramMainController
{

    /**
     * @return string
     */
    public static function getDefaultController()
    {
        return 'home';
    }
}