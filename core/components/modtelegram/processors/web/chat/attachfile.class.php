<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modChatAttachFileProcessor extends modTelegramResponseProcessor
{
    /** @var null $data */
    protected $data = null;

    function process()
    {
        $data = array();

        /** @var modTelegramUser $user */
        /** @var modTelegramChat $chat */
        if (
            $user = $this->modx->getObject($this->classUser, array(
                'id' => session_id(),
            ))
            AND
            $chat = $this->modx->getObject($this->classChat, array(
                'uid' => session_id(),
            ))
            AND
            $checkFile = $this->checkFile()
        ) {

            $tmp = $this->modx->getOption('tmp_name', $this->data, '', true);
            $name = $this->modx->getOption('hash', $this->data, session_id(), true);
            $type = $this->modx->getOption('type', $this->data, '', true);
            $size = $this->modx->getOption('size', $this->data, 0, true);

            if ($size > 10240000) {
                return $this->failure('err_file_ns', $data);
            }

            file_put_contents(MODX_BASE_PATH . 'tmp/' . $name . '.' . $type, file_get_contents($tmp));

            $response = $this->modtelegram->telegramSendDocument(array(
                'chat_id'   => $chat->getManager(),
                'from_path' => 'tmp/' . $name . '.' . $type,
            ));

            if (
                $response
                AND
                $document = $this->modx->getOption('document', $response)
                AND
                $fileId = $this->modx->getOption('file_id', $document)
            ) {

                $this->modtelegram->writeUserMessage(array(
                    'uid'     => $chat->getUser(),
                    'mid'     => $chat->getManager(),
                    'message' => $fileId,
                    'type'    => 'document'
                ));
            }

            return $this->success('', $data);
        }

        return $this->failure('', $data);
    }

    protected function checkFile()
    {
        if (empty($_FILES['file'])) {
            return $this->modtelegram->lexicon('err_file_ns');
        }
        if (!file_exists($_FILES['file']['tmp_name']) OR !is_uploaded_file($_FILES['file']['tmp_name'])) {
            return $this->modtelegram->lexicon('err_file_ns');
        }
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return $this->modtelegram->lexicon('err_file_ns');
        }

        $tnm = $_FILES['file']['tmp_name'];
        $name = $_FILES['file']['name'];

        $size = @filesize($tnm);

        $tim = getimagesize($tnm);
        $width = $height = 0;
        if (is_array($tim)) {
            $width = $tim[0];
            $height = $tim[1];
        }

        $type = explode('.', $name);
        $type = end($type);
        $name = rtrim(str_replace($type, '', $name), '.');
        $hash = hash_file('sha1', $tnm);

        $this->data = array(
            'tmp_name'   => $tnm,
            'size'       => $size,
            'type'       => $type,
            'name'       => $name,
            'width'      => $width,
            'height'     => $height,
            'hash'       => $hash,
            'properties' => $this->modx->toJSON(array(
                'w' => $width,
                'h' => $height,
                'f' => $type
            ))
        );

        return true;

    }

}

return 'modChatAttachFileProcessor';