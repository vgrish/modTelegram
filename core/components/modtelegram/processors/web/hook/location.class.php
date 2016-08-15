<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookLocationProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        $message = array();

        @list($uid) = $this->getProperty('options', array());

        $q = $this->modx->newQuery($this->classChat);
        $q->where(array(
            'mid' => $this->getProperty('from'),
        ));
        if (!empty($uid)) {
            $q->andCondition(array(
                'uid' => $uid,
            ));
        }
        else {
            $q->andCondition(array(
                'active' => true,
            ));
        }

        /** @var modTelegramChat $chat */
        if ($chat = $this->modx->getObject($this->classChat, $q)) {

            if ($user = $this->modx->getObject($this->classUser, array(
                'id' => $chat->getUser()
            ))
            ) {

                $properties = (array)$user->get('properties');
                $latitude = $this->modx->getOption('latitude', $properties);
                $longitude = $this->modx->getOption('longitude', $properties);

                if (
                    (!$latitude OR !$longitude)
                    AND
                    $url = $this->modx->getOption('api_url_sypexgeo', null, 'https://api.sypexgeo.net/json/', true)
                    AND
                    $ip = $this->modx->getOption('ip', $properties)
                    AND
                    $response = $this->modtelegram->request('', null, $url . $ip)
                ) {
                    $response = json_decode($response, true);
                    $row = $this->modtelegram->flattenArray($response);

                    $message[] = $this->modtelegram->lexicon('hook_info_success_' . $this->action);
                    $message[] = $this->modtelegram->lexicon('location', $row);

                    $this->sendMessage($message);

                    $latitude = $this->modx->getOption('city.lat', $row);
                    $longitude = $this->modx->getOption('city.lon', $row);
                }

                if ($latitude AND $longitude) {
                    $this->modtelegram->telegramSendLocation(array(
                        'chat_id'   => $chat->getManager(),
                        'latitude'  => $latitude,
                        'longitude' => $longitude,
                    ));
                }

                return $this->success('', $message);

            }

        }

        $message = $this->modtelegram->lexicon('hook_info_failure_' . $this->action);
        $this->sendMessage($message);

        return $this->failure('', $message);
    }

}

return 'modHookLocationProcessor';