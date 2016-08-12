<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modChatAttachFileProcessor extends modTelegramResponseProcessor
{
    /** @var null $data */
    protected $data = null;

    function process()
    {
        $data = array();

        $checkFile = $this->checkFile();
        if ($checkFile !== true) {
            return $this->failure($checkFile, $data);
        }

        if (empty($this->data)) {
            return $this->failure('err_file_ns', $data);
        }

        $tmp = $this->modx->getOption('tmp_name', $this->data, '', true);
        $name = $this->modx->getOption('hash', $this->data, session_id(), true);
        $type = $this->modx->getOption('type', $this->data, '', true);
        $size = $this->modx->getOption('size', $this->data, 0, true);

        if ($size > 10240000) {
            return $this->failure('err_file_ns', $data);
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