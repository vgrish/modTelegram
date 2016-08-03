<?php

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

/**
 * The base class for modtelegram.
 */
class modtelegram
{
    /* @var modX $modx */
    public $modx;

    /** @var mixed|null $namespace */
    public $namespace = 'modtelegram';
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modtelegram/');
        $assetsPath = $this->getOption('assets_path', $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/modtelegram/');
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/modtelegram/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'namespace'       => $this->namespace,
            'connectorUrl'    => $connectorUrl,
            'assetsBasePath'  => MODX_ASSETS_PATH,
            'assetsBaseUrl'   => MODX_ASSETS_URL,
            'assetsPath'      => $assetsPath,
            'assetsUrl'       => $assetsUrl,
            'actionUrl'       => $assetsUrl . 'action.php',
            'cssUrl'          => $assetsUrl . 'css/',
            'jsUrl'           => $assetsUrl . 'js/',
            'corePath'        => $corePath,
            'modelPath'       => $corePath . 'model/',
            'handlersPath'    => $corePath . 'handlers/',
            'processorsPath'  => $corePath . 'processors/',
            'templatesPath'   => $corePath . 'elements/templates/mgr/',
            'jsonResponse'    => true,
            'prepareResponse' => true,
            'showLog'         => false,

        ), $config);

        $this->modx->addPackage('modtelegram', $this->getOption('modelPath'));
        $this->modx->lexicon->load('modtelegram:default');
        $this->namespace = $this->getOption('namespace', $config, 'modtelegram');
        $this->curlClient = $this->modx->getService('rest.modRestCurlClient');
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    /**
     * @param string $ctx
     * @param array  $scriptProperties
     *
     * @return bool
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->modx->error->reset();
        $this->config = array_merge($this->config, $scriptProperties, array('ctx' => $ctx));

        if (!empty($this->initialized[$ctx])) {
            return true;
        }

        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') OR !MODX_API_MODE) {

                    $this->initialized[$ctx] = true;
                }
                break;
        }

        return true;
    }

    /** @inheritdoc} */
    public function getPropertiesKey(array $properties = array())
    {
        return !empty($properties['propkey']) ? $properties['propkey'] : false;
    }

    /** @inheritdoc} */
    public function saveProperties(array $properties = array())
    {
        return !empty($properties['propkey']) ? $_SESSION[$this->namespace][$properties['propkey']] = $properties : false;
    }

    /** @inheritdoc} */
    public function getProperties($key = '')
    {
        return !empty($_SESSION[$this->namespace][$key]) ? $_SESSION[$this->namespace][$key] : array();
    }

    /**
     * return lexicon message if possibly
     *
     * @param string $message
     *
     * @return string $message
     */
    public function lexicon($message, $placeholders = array())
    {
        $key = '';
        if ($this->modx->lexicon->exists($message)) {
            $key = $message;
        } elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
            $key = $this->namespace . '_' . $message;
        }
        if ($key !== '') {
            $message = $this->modx->lexicon->process($key, $placeholders);
        }

        return $message;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function failure($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }


    /**
     * @param string $action
     * @param array  $data
     *
     * @return array|modProcessorResponse|string
     */
    public function runProcessor($action = '', $data = array())
    {
        $this->modx->error->reset();
        $processorsPath = !empty($this->config['processorsPath']) ? $this->config['processorsPath'] : MODX_CORE_PATH;
        /* @var modProcessorResponse $response */
        $response = $this->modx->runProcessor($action, $data, array('processors_path' => $processorsPath));

        return $this->config['prepareResponse'] ? $this->prepareResponse($response) : $response;
    }

    /**
     * This method returns prepared response
     *
     * @param mixed $response
     *
     * @return array|string $response
     */
    public function prepareResponse($response)
    {
        if ($response instanceof modProcessorResponse) {
            $output = $response->getResponse();
        } else {
            $message = $response;
            if (empty($message)) {
                $message = $this->lexicon('err_unknown');
            }
            $output = $this->failure($message);
        }
        if ($this->config['jsonResponse'] AND is_array($output)) {
            $output = $this->modx->toJSON($output);
        } elseif (!$this->config['jsonResponse'] AND !is_array($output)) {
            $output = $this->modx->fromJSON($output);
        }

        return $output;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param bool   $showLog
     * @param bool   $writeLog
     */
    public function log($message = '', $data = array(), $showLog = false)
    {
        if ($showLog OR $this->getOption('showLog', null, false, true)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $message);
            if (!empty($data)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($data, 1));
            }
        }
    }


    /**
     * https://core.telegram.org/bots/api#getme
     *
     * @param array $params
     *
     * @return array
     */
    public function telegramGetMe(array $params = array())
    {
        $mode = '/getMe/';
        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     * https://core.telegram.org/bots/api#getupdates
     *
     * @param array $params
     *
     * @return array
     */
    public function telegramGetUpdates(array $params = array())
    {
        $mode = '/getUpdates/';
        $params = array_merge(array(
            'offset'  => 0,
            'limit'   => 100,
            'timeout' => 0
        ), $params);
        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     * https://core.telegram.org/bots/api#getuserprofilephotos
     *
     * @param array $params
     *
     * @return array
     */
    public function telegramGetUserProfilePhotos(array $params = array())
    {
        $mode = '/getUserProfilePhotos/';
        $params = array_merge(array(
            'user_id' => null,
            'offset'  => 0,
            'limit'   => 100
        ), $params);
        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    public function telegramGetFile($file = '')
    {
        $data = $this->request($file, array(), array(), 'file/bot');

        return $data;
    }


    /**
     * https://core.telegram.org/bots/api#sendmessage
     *
     * @param array $params
     *
     * @return array
     */
    public function telegramSendMessage(array $params = array())
    {
        $mode = '/sendMessage/';
        $params = array_merge(array(
            'chat_id'                  => null,
            'text'                     => null,
            'parse_mode'               => $this->getOption('parse_mode', null, 'html', true),
            'disable_web_page_preview' => $this->getOption('disable_web_page_preview', null, false, true),
            'disable_notification'     => $this->getOption('disable_notification', null, false, true),
            'reply_to_message_id'      => $this->getOption('reply_to_message_id', null),
            'reply_markup'             => $this->getOption('reply_markup', null, '{}', true),
        ), $params);
        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     * https://core.telegram.org/bots/api#forwardmessage
     *
     * @param array $params
     *
     * @return array
     */
    public function telegramForwardMessage(array $params = array())
    {
        $mode = '/forwardMessage/';
        $params = array_merge(array(
            'chat_id'              => null,
            'from_chat_id'         => null,
            'message_id'           => null,
            'disable_notification' => $this->getOption('disable_notification', null, false, true),
        ), $params);
        $data = $this->request($mode, $params);

        return $data;
    }


    /**
     * @param string $mode
     * @param array  $params
     * @param array  $options
     *
     * @return array
     */
    public function request($mode = '', array $params = array(), array $options = array(), $sfx = 'bot')
    {
        $mode = trim($mode, '/');

        $apiKey = $this->getOption('apiKey', null, '', true);
        $apiUrl = $this->getOption('apiUrl', null, 'https://api.telegram.org/', true);

        $params = array_merge(array(), $params);

        $options = array_merge(array(
            'contentType' => 'json',
        ), $options);

        $url = rtrim($apiUrl, '/') . '/' . $sfx . $apiKey . '/' . $mode;


        $this->modx->log(1, print_r($url, 1));


        $data = $this->curlClient->request($url, '', 'POST', $params, $options);
        $data = (array)json_decode($data, true);
        if (empty($data['ok'])) {
            $this->log('', $data, true);
        }
        $data = $this->prepareData($data, $mode);
        $this->log('', $data);

        return $data;
    }

    /**
     * @param array  $response
     * @param string $mode
     *
     * @return array
     */
    protected function prepareData(array $response = array(), $mode = '')
    {
        $data = array();
        $mode = strtolower($mode);

        $this->modx->log(1, print_r($mode, 1));

        switch ($mode) {

            case 'getme':
            case 'getupdates':
            case 'getuserprofilephotos':
            case 'sendmessage':
                $data = isset($response['result']) ? $response['result'] : array();
                break;

            default:
                $data = $response;
                break;
        }

        return (array)$data;
    }

}