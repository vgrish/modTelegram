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

    /** @var modRestCurlClient $curlClient */
    public $curlClient;

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
    public function telegramGetFile(array $params = array())
    {
        $mode = '/getFile/';
        $params = array_merge(array(
            'file_id' => null,
        ), $params);
        $data = $this->request($mode, $params);

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
            'reply_to_message_id'      => null,
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
     *
     * https://core.telegram.org/bots/api#sendphoto
     *
     * @param array $params
     */
    public function telegramSendPhoto(array $params = array())
    {
        $mode = '/sendPhoto/';
        $params = array_merge(array(
            'chat_id'              => null,
            'photo'                => null,
            'caption'              => null,
            'disable_notification' => $this->getOption('disable_notification', null, false, true),
            'reply_to_message_id'  => null,
            'reply_markup'         => $this->getOption('reply_markup', null, '{}', true),
        ), $params);

        if ($fromPath = $this->getOption('from_path', $params)) {
            if (strpos($fromPath, MODX_BASE_PATH) !== 0) {
                $fromPath = MODX_BASE_PATH . $fromPath;
            }
            $params['photo'] = $this->telegramEncodeFile($fromPath);
        }

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#sendaudio
     *
     * @param array $params
     */
    public function telegramSendAudio(array $params = array())
    {
        $mode = '/sendAudio/';
        $params = array_merge(array(
            'chat_id'              => null,
            'audio'                => null,
            'duration'             => null,
            'performer'            => null,
            'title'                => null,
            'disable_notification' => $this->getOption('disable_notification', null, false, true),
            'reply_to_message_id'  => null,
            'reply_markup'         => $this->getOption('reply_markup', null, '{}', true),
        ), $params);

        if ($fromPath = $this->getOption('from_path', $params)) {
            if (strpos($fromPath, MODX_BASE_PATH) !== 0) {
                $fromPath = MODX_BASE_PATH . $fromPath;
            }
            $params['audio'] = $this->telegramEncodeFile($fromPath);
        }

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#senddocument
     *
     * @param array $params
     */
    public function telegramSendDocument(array $params = array())
    {
        $mode = '/sendDocument/';
        $params = array_merge(array(
            'chat_id'              => null,
            'document'             => null,
            'caption'              => null,
            'disable_notification' => $this->getOption('disable_notification', null, false, true),
            'reply_to_message_id'  => null,
            'reply_markup'         => $this->getOption('reply_markup', null, '{}', true),
        ), $params);

        if ($fromPath = $this->getOption('from_path', $params)) {
            if (strpos($fromPath, MODX_BASE_PATH) !== 0) {
                $fromPath = MODX_BASE_PATH . $fromPath;
            }
            $params['document'] = $this->telegramEncodeFile($fromPath);
        }

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#sendsticker
     *
     * @param array $params
     */
    public function telegramSendSticker(array $params = array())
    {
        $mode = '/sendSticker/';
        $params = array_merge(array(
            'chat_id'              => null,
            'sticker'              => null,
            'disable_notification' => $this->getOption('disable_notification', null, false, true),
            'reply_to_message_id'  => null,
            'reply_markup'         => $this->getOption('reply_markup', null, '{}', true),
        ), $params);

        if ($fromPath = $this->getOption('from_path', $params)) {
            if (strpos($fromPath, MODX_BASE_PATH) !== 0) {
                $fromPath = MODX_BASE_PATH . $fromPath;
            }
            $params['sticker'] = $this->telegramEncodeFile($fromPath);
        }

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#sendvideo
     *
     * @param array $params
     */
    public function telegramSendVideo(array $params = array())
    {
        $mode = '/sendVideo/';
        $params = array_merge(array(
            'chat_id'              => null,
            'video'                => null,
            'duration'             => null,
            'width'                => null,
            'height'               => null,
            'caption'              => null,
            'disable_notification' => $this->getOption('disable_notification', null, false, true),
            'reply_to_message_id'  => null,
            'reply_markup'         => $this->getOption('reply_markup', null, '{}', true),
        ), $params);

        if ($fromPath = $this->getOption('from_path', $params)) {
            if (strpos($fromPath, MODX_BASE_PATH) !== 0) {
                $fromPath = MODX_BASE_PATH . $fromPath;
            }
            $params['video'] = $this->telegramEncodeFile($fromPath);
        }

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#sendvoice
     *
     * @param array $params
     */
    public function telegramSendVoice(array $params = array())
    {
        $mode = '/sendVoice/';
        $params = array_merge(array(
            'chat_id'              => null,
            'voice'                => null,
            'duration'             => null,
            'disable_notification' => $this->getOption('disable_notification', null, false, true),
            'reply_to_message_id'  => null,
            'reply_markup'         => $this->getOption('reply_markup', null, '{}', true),
        ), $params);

        if ($fromPath = $this->getOption('from_path', $params)) {
            if (strpos($fromPath, MODX_BASE_PATH) !== 0) {
                $fromPath = MODX_BASE_PATH . $fromPath;
            }
            $params['voice'] = $this->telegramEncodeFile($fromPath);
        }

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#sendlocation
     *
     * @param array $params
     */
    public function telegramSendLocation(array $params = array())
    {
        $mode = '/sendLocation/';
        $params = array_merge(array(
            'chat_id'              => null,
            'latitude'             => null,
            'longitude'            => null,
            'disable_notification' => $this->getOption('disable_notification', null, false, true),
            'reply_to_message_id'  => null,
            'reply_markup'         => $this->getOption('reply_markup', null, '{}', true),
        ), $params);

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#sendcontact
     *
     * @param array $params
     */
    public function telegramSendContact(array $params = array())
    {
        $mode = '/sendContact/';
        $params = array_merge(array(
            'chat_id'              => null,
            'phone_number'         => null,
            'first_name'           => null,
            'last_name'            => null,
            'disable_notification' => $this->getOption('disable_notification', null, false, true),
            'reply_to_message_id'  => null,
            'reply_markup'         => $this->getOption('reply_markup', null, '{}', true),
        ), $params);

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#sendchataction
     *
     * @param array $params
     */
    public function telegramSendChatAction(array $params = array())
    {
        $mode = '/sendChatAction/';
        $params = array_merge(array(
            'chat_id' => null,
            'action'  => null,
        ), $params);

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#kickchatmember
     *
     * @param array $params
     */
    public function telegramKickChatMember(array $params = array())
    {
        $mode = '/kickChatMember/';
        $params = array_merge(array(
            'chat_id' => null,
            'user_id' => null,
        ), $params);

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#leavechat
     *
     * @param array $params
     */
    public function telegramLeaveChat(array $params = array())
    {
        $mode = '/leaveChat/';
        $params = array_merge(array(
            'chat_id' => null,
        ), $params);

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#getchat
     *
     * @param array $params
     */
    public function telegramGetChat(array $params = array())
    {
        $mode = '/getChat/';
        $params = array_merge(array(
            'chat_id' => null,
        ), $params);

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#getchatadministrators
     *
     * @param array $params
     */
    public function telegramGetChatAdministrators(array $params = array())
    {
        $mode = '/getChatAdministrators/';
        $params = array_merge(array(
            'chat_id' => null,
        ), $params);

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#getchatmemberscount
     *
     * @param array $params
     */
    public function telegramGetChatMembersCount(array $params = array())
    {
        $mode = '/getChatMembersCount/';
        $params = array_merge(array(
            'chat_id' => null,
        ), $params);

        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     *
     * https://core.telegram.org/bots/api#getchatmemberscount
     *
     * @param array $params
     */
    public function telegramGetChatMember(array $params = array())
    {
        $mode = '/getChatMember/';
        $params = array_merge(array(
            'chat_id' => null,
            'user_id' => null,
        ), $params);

        $data = $this->request($mode, $params);

        return $data;
    }


    /**
     * @param array $params
     *
     * @return modRestResponse
     */
    public function telegramDownloadFile(array $params = array())
    {
        $fromPath = $this->getOption('from_path', $params, '', true);
        $toPath = $this->getOption('to_path', $params, '', true);
        $toPath = rtrim($toPath, '/') . '/';

        $name = explode('/', $fromPath);
        $name = array_pop($name);

        if (strpos($toPath, MODX_BASE_PATH) !== 0) {
            $toPath = MODX_BASE_PATH . $toPath;
        }

        $cacheManager = $this->modx->getCacheManager();
        if (!file_exists($toPath) OR !is_dir($toPath)) {
            if (!$cacheManager->writeTree($toPath)) {
                $this->log('', 'Could not create directory: ' . $toPath, true);
            }
        }

        $url = $this->telegramGetFileUrl($fromPath);

        $data = $this->request('', null, $url);
        if (!is_string($data)) {
            $this->log('', $data, true);
        }

        if (!file_put_contents($toPath . $name, $data)) {
            $this->log('Write file error', $data, true);
        }

        return $data;
    }


    /**
     * @param string $mode
     * @param string $sfx
     *
     * @return mixed|null|string
     */
    protected function telegramGetApiUrl($mode = '', $sfx = 'bot')
    {
        $url = $this->getOption('apiUrl', null, 'https://api.telegram.org/', true);
        $key = $this->getOption('apiKey', null, '', true);
        $url = rtrim($url, '/') . '/' . $sfx . $key . '/' . $mode;

        return $url;
    }

    /**
     * @param string $path
     * @param string $sfx
     *
     * @return mixed|null|string
     */
    protected function telegramGetFileUrl($path = '', $sfx = 'file/bot')
    {
        $url = $this->getOption('apiUrl', null, 'https://api.telegram.org/', true);
        $key = $this->getOption('apiKey', null, '', true);
        $url = rtrim($url, '/') . '/' . $sfx . $key . '/' . $path;

        return $url;
    }

    protected function telegramEncodeFile($path = '')
    {
        $file = "@{$path}";

        return $file;
    }

    public function request($mode = '', $params = null, $url = '')
    {
        $mode = trim($mode, '/');

        if (empty($url)) {
            $url = $this->telegramGetApiUrl($mode);
        }

        $post = is_array($params);
        $contentType = $post ? 'multipart/form-data' : 'application/json';

        $ch = curl_init($url);
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_POST           => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => array('Content-Type: ' . $contentType),
                CURLOPT_SSL_VERIFYPEER => 0
            )
        );

        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $data = curl_exec($ch);
        curl_close($ch);
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
    protected function prepareData($data, $mode = '')
    {
        $mode = strtolower($mode);

        $this->modx->log(1, print_r($mode, 1));

        switch ($mode) {
            case '':
                break;
            default:
                $data = json_decode($data, true);
                if (empty($data['ok'])) {
                    $this->log('', $data, true);
                }
                $data = isset($data['result']) ? $data['result'] : array();
                break;
        }

        return $data;
    }

}