<?php

abstract class modTelegramResponseProcessor extends modProcessor
{
    /** @var  modtelegram $modtelegram */
    var $modtelegram;

    function __construct(modX &$modx, array $properties = array())
    {
        parent::__construct($modx, $properties);

        if (!$namespace = $modx->getObject('modNamespace', 'modtelegram')) {
            $error = "[modtelegram] Not found modNamespace: modtelegram ";
            $this->modx->log(modX::LOG_LEVEL_ERROR, $error);

            return $this->failure($error);
        }
    }

    public function initialize()
    {
        /** @var modtelegram $modtelegram */
        $corePath = $this->modx->getOption('modtelegram_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modtelegram/');
        /** @var modtelegram $modtelegram */
        $this->modtelegram = $this->modx->getService(
            'modtelegram',
            'modtelegram',
            $corePath . 'model/modtelegram/',
            array_merge($this->properties, array('core_path' => $corePath))
        );

        $propKey = $this->getProperty('propkey');
        if (empty($propKey)) {
            return $this->modtelegram->lexicon('err_propkey_ns');
        }

        $properties = $this->modtelegram->getProperties($propKey);
        if (empty($properties)) {
            return $this->modtelegram->lexicon('err_properties_ns');
        }

        $this->setProperties(array_merge($properties, $this->getProperties()));
        $this->modtelegram->initialize($this->getProperty('ctx', $this->modx->context->key), $this->getProperties());

        if (!$this->checkAction()) {
            return $this->modtelegram->lexicon('err_lock');
        }

        return true;
    }

    protected function checkAction()
    {
        $action = $this->getProperty('action');
        $action = end($this->modtelegram->explodeAndClean($action, '/'));

        $actions = $this->getProperty('actions');
        $actions = $this->modtelegram->explodeAndClean($actions);

        return in_array($action, $actions);
    }

}

return 'modtelegramResponseProcessor';