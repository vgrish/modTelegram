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

    public $classUser = 'modTelegramUser';
    public $classManager = 'modTelegramManager';
    public $classChat = 'modTelegramChat';
    public $classMessage = 'modTelegramMessage';

    public $classModUser = 'modUser';
    public $classModUserProfile = 'modUserProfile';

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
            'hookUrl'         => $assetsUrl . 'webhook.php',
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
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * from
     * https://github.com/bezumkin/pdoTools/blob/19195925226e3f8cb0ba3c8d727567e9f3335673/core/components/pdotools/model/pdotools/pdotools.class.php#L320
     *
     * @param array  $array
     * @param string $plPrefix
     * @param string $prefix
     * @param string $suffix
     * @param bool   $uncacheable
     *
     * @return array
     */
    public function makePlaceholders(
        array $array = array(),
        $plPrefix = '',
        $prefix = '[[+',
        $suffix = ']]',
        $uncacheable = true
    ) {
        $result = array('pl' => array(), 'vl' => array());
        $uncachedPrefix = str_replace('[[', '[[!', $prefix);
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = array_merge_recursive($result,
                    $this->makePlaceholders($v, $plPrefix . $k . '.', $prefix, $suffix, $uncacheable));
            } else {
                $pl = $plPrefix . $k;
                $result['pl'][$pl] = $prefix . $pl . $suffix;
                $result['vl'][$pl] = $v;
                if ($uncacheable) {
                    $result['pl']['!' . $pl] = $uncachedPrefix . $pl . $suffix;
                    $result['vl']['!' . $pl] = $v;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @param array  $properties
     *
     * @return mixed|string
     */
    public function getChunk($name = '', array $properties = array())
    {
        if (class_exists('pdoTools') AND $pdo = $this->modx->getService('pdoTools')) {
            $output = $pdo->getChunk($name, $properties);
        } elseif (strpos($name, '@INLINE ') !== false) {
            $content = str_replace('@INLINE', '', $name);
            /** @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk', array('name' => 'inline-' . uniqid()));
            $chunk->setCacheable(false);
            $output = $chunk->process($properties, $content);
        } else {
            $output = $this->modx->getChunk($name, $properties);
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
     * @param array $properties
     */
    public function loadResourceJsCss(array $properties = array())
    {
        $properties = array_merge($this->config, $properties);
        $pls = $this->makePlaceholders($properties);

        if ($properties['frontendJs']) {
            $this->modx->regClientScript(str_replace($pls['pl'], $pls['vl'], $properties['frontendJs']));
        }
        if ($properties['frontendCss']) {
            $this->modx->regClientCSS(str_replace($pls['pl'], $pls['vl'], $properties['frontendCss']));
        }

        $config = array();
        $config['assetsBaseUrl'] = str_replace($pls['pl'], $pls['vl'], $properties['assetsBaseUrl']);
        $config['assetsUrl'] = str_replace($pls['pl'], $pls['vl'], $properties['assetsUrl']);
        $config['actionUrl'] = str_replace($pls['pl'], $pls['vl'], $properties['actionUrl']);
        $config['helper'] = (array)$properties['helper'];
        $config['propkey'] = "{$properties['propkey']}";
        $config['action'] = "{$properties['action']}";
        $config['ctx'] = "{$this->modx->context->get('key')}";

        $this->modx->regClientStartupScript("<script type=\"text/javascript\">modTelegramConfig={$this->modx->toJSON($config)};</script>",
            true);
    }

    /**
     * Sets data to cache
     *
     * @param mixed $data
     * @param mixed $options
     *
     * @return string $cacheKey
     */
    public function setCache($data = array(), $options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        if (!empty($cacheKey) AND !empty($cacheOptions) AND $this->modx->getCacheManager()) {
            $this->modx->cacheManager->set(
                $cacheKey,
                $data,
                $cacheOptions[xPDO::OPT_CACHE_EXPIRES],
                $cacheOptions
            );
        }

        return $cacheKey;
    }

    /**
     * Returns data from cache
     *
     * @param mixed $options
     *
     * @return mixed
     */
    public function getCache($options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        $cached = '';
        if (!empty($cacheOptions) AND !empty($cacheKey) AND $this->modx->getCacheManager()) {
            $cached = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
        }

        return $cached;
    }


    /**
     * @param array $options
     *
     * @return bool
     */
    public function clearCache($options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        $cacheOptions['cache_key'] .= $cacheKey;
        if (!empty($cacheOptions) AND $this->modx->getCacheManager()) {
            return $this->modx->cacheManager->clean($cacheOptions);
        }

        return false;
    }

    /**
     * Returns array with options for cache
     *
     * @param $options
     *
     * @return array
     */
    public function getCacheOptions($options = array())
    {
        if (empty($options)) {
            $options = $this->config;
        }
        $cacheOptions = array(
            xPDO::OPT_CACHE_KEY     => empty($options['cache_key'])
                ? 'default' : 'default/' . $this->namespace . '/',
            xPDO::OPT_CACHE_HANDLER => !empty($options['cache_handler'])
                ? $options['cache_handler'] : $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
            xPDO::OPT_CACHE_EXPIRES => $options['cacheTime'] !== ''
                ? (integer)$options['cacheTime'] : (integer)$this->modx->getOption('cache_resource_expires', null, 0),
        );

        return $cacheOptions;
    }

    /**
     * Returns key for cache of specified options
     *
     * @var mixed $options
     * @return bool|string
     */
    public function getCacheKey($options = array())
    {
        if (empty($options)) {
            $options = $this->config;
        }
        if (!empty($options['cache_key'])) {
            return $options['cache_key'];
        }
        $key = !empty($this->modx->resource) ? $this->modx->resource->getCacheKey() : '';

        return $key . '/' . sha1(serialize($options));
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
     * @return mixed|string
     */
    public function telegramGetWebHookUrl()
    {
        $url = $this->modx->getOption('site_url', null);
        $hookUrl = $this->getOption('hookUrl', null);
        $url = trim($url, '/') . '/' . trim($hookUrl, '/');
        $url = $this->getOption('web_hook_url', null, $url, true);

        $key = $this->namespace . '_web_hook_url';
        if (!$tmp = $this->modx->getObject('modSystemSetting', array('key' => $key))) {
            $tmp = $this->modx->newObject('modSystemSetting');
        }
        $tmp->fromArray(array(
            'xtype'     => 'textfield',
            'namespace' => $this->namespace,
            'area'      => $this->namespace . '_main',
            'key'       => $key,
            'value'     => $url,
        ), '', true, true);
        $tmp->save();

        return $url;
    }

    /**
     *
     * https://core.telegram.org/bots/api#setwebhook
     *
     * @param array $params
     */
    public function telegramSetWebHook(array $params = array())
    {
        $mode = '/setWebhook/';
        $params = array_merge(array(
            'url'         => $this->telegramGetWebHookUrl(),
            'certificate' => null,
        ), $params);

        if ($fromPath = $this->getOption('from_path', $params)) {
            if (strpos($fromPath, MODX_BASE_PATH) !== 0) {
                $fromPath = MODX_BASE_PATH . $fromPath;
            }
            $params['certificate'] = $this->telegramEncodeFile($fromPath);
        }

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
        $url = $this->getOption('api_url', null, 'https://api.telegram.org/', true);
        $key = $this->getOption('api_key', null, '', true);
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
        $url = $this->getOption('api_url', null, 'https://api.telegram.org/', true);
        $key = $this->getOption('api_key', null, '', true);
        $url = rtrim($url, '/') . '/' . $sfx . $key . '/' . $path;

        return $url;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function telegramEncodeFile($path = '')
    {
        $file = "@{$path}";

        return $file;
    }

    /**
     * @param string $mode
     * @param null   $params
     * @param string $url
     *
     * @return array|mixed
     */
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

    /**
     * @param string $email
     * @param string $password
     *
     * @return modUser|null
     */
    public function getUserByEmailPassword($email = '', $password = '')
    {
        $user = null;
        $q = $this->modx->newQuery($this->classModUser);
        $q->innerJoin($this->classModUserProfile, $this->classModUserProfile,
            "{$this->classModUser}.id = {$this->classModUserProfile}.internalKey");
        $q->where(array(
            "{$this->classModUser}.active"       => true,
            "{$this->classModUserProfile}.email" => $email
        ));
        /** @var modUser $user */
        if (
            $user = $this->modx->getObject($this->classModUser, $q)
            AND
            $user->passwordMatches($password)
        ) {
            $user = $user->get('id');
        }

        return $user;
    }

    public function getAvailableManagerByUid($uid = '')
    {
        $manager = null;

        // берем менеджера с минимальным кол-ом чатов
        $q = $this->modx->newQuery($this->classManager);
        $q->leftJoin($this->classChat, $this->classChat, "{$this->classChat}.mid = {$this->classManager}.id");

        $q->where(array(
            "{$this->classChat}.uid"       => $uid,
            "{$this->classManager}.active" => 1,
        ));
        $q->orCondition(array(
            "{$this->classManager}.active" => 1,
        ));

        $q->groupby("{$this->classManager}.id");
        $q->sortby("COUNT({$this->classChat}.mid)", "ASC");

        $manager = $this->modx->getValue($q->prepare());

        return $manager;
    }


    public function processChatMessage(array $row = array())
    {
        $row['data'] = date($this->getOption('data_format', null, 'd.m.Y H:i'), $row['timestamp']);
        $row['message'] = strip_tags(html_entity_decode($row['message'], ENT_QUOTES, 'UTF-8'));

        return $row;
    }

    public function processTelegramMessage($row = '')
    {
        if (is_array($row)) {
            $row = implode("\n", $row);
        }

        return $row;
    }

    public function sendMessage($message = '', $uid = '')
    {
        $message = $this->processTelegramMessage($message);
        if (empty($message) OR empty($uid)) {
            return false;
        }

        return $this->telegramSendMessage(array(
            'chat_id' => $uid,
            'text'    => $message,
        ));
    }


    public function writeMessage(array $data = array())
    {
        $message = $this->modx->getOption('message', $data, '', true);
        $message = $this->processTelegramMessage($message);

        $uid = $this->modx->getOption('uid', $data);
        $mid = $this->modx->getOption('mid', $data);
        $from = $this->modx->getOption('from', $data);
        $type = $this->modx->getOption('type', $data, 'text', true);

        if (empty($message) OR empty($uid) OR empty($mid) OR empty($from) OR empty($type)) {
            return false;
        }
        /** @var modTelegramMessage $msg */
        $msg = $this->modx->newObject($this->classMessage);
        $msg->fromArray(array(
            'uid'     => $uid,
            'mid'     => $mid,
            'from'    => $from,
            'type'    => $type,
            'message' => $message
        ), '', true, true);

        return $msg->save();
    }

    public function writeManagerMessage(array $data = array())
    {
        $data = array_merge($data, array(
            'from' => 'manager'
        ));

        return $this->writeMessage($data);
    }

    public function writeUserMessage(array $data = array())
    {
        $data = array_merge($data, array(
            'from' => 'user'
        ));

        return $this->writeMessage($data);
    }

    public function getUserData($id = 0)
    {
        $tmp = array(
            'cache_key' => 'managers/manager_' . $id,
            'cacheTime' => 0,
        );
        if (!$data = $this->getCache($tmp)) {
            $data = array();

            if (!empty($id)) {
                $q = $this->modx->newQuery($this->classUser);
                $q->leftJoin($this->classModUser, $this->classModUser,
                    "{$this->classModUser}.id = {$this->classUser}.user");
                $q->leftJoin($this->classModUserProfile, $this->classModUserProfile,
                    "{$this->classModUserProfile}.internalKey = {$this->classUser}.user");
                $q->where(array(
                    "{$this->classUser}.id" => $id,
                ));

                $q->select($this->modx->getSelectColumns($this->classUser, $this->classUser, '', array(),
                    true));
                $q->select($this->modx->getSelectColumns($this->classModUser, $this->classModUser, 'user_',
                    array('username'),
                    false));
                $q->select($this->modx->getSelectColumns($this->classModUserProfile, $this->classModUserProfile,
                    'profile_', array('sessionid'), true));

                if ($q->prepare() AND $q->stmt->execute()) {
                    $data = (array)$q->stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            $this->setCache($data, $tmp);
        }

        return $data;
    }

    public function getManagerData($id = 0)
    {
        $tmp = array(
            'cache_key' => 'managers/manager_' . $id,
            'cacheTime' => 0,
        );
        if (!$data = $this->getCache($tmp)) {
            $data = array();

            if (!empty($id)) {
                $q = $this->modx->newQuery($this->classManager);
                $q->leftJoin($this->classModUser, $this->classModUser,
                    "{$this->classModUser}.id = {$this->classManager}.user");
                $q->leftJoin($this->classModUserProfile, $this->classModUserProfile,
                    "{$this->classModUserProfile}.internalKey = {$this->classManager}.user");
                $q->where(array(
                    "{$this->classManager}.id" => $id,
                ));

                $q->select($this->modx->getSelectColumns($this->classManager, $this->classManager, '', array(),
                    true));
                $q->select($this->modx->getSelectColumns($this->classModUser, $this->classModUser, 'user_',
                    array('username'),
                    false));
                $q->select($this->modx->getSelectColumns($this->classModUserProfile, $this->classModUserProfile,
                    'profile_', array('sessionid'), true));

                if ($q->prepare() AND $q->stmt->execute()) {
                    $data = (array)$q->stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            $this->setCache($data, $tmp);
        }

        return $data;
    }


}