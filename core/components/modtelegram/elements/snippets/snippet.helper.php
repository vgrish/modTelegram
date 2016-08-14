<?php

/** @var array $scriptProperties */
/** @var modtelegram $modtelegram */
if (!$modtelegram = $modx->getService('modtelegram', 'modtelegram', $modx->getOption('modtelegram_core_path', null,
        $modx->getOption('core_path') . 'components/modtelegram/') . 'model/modtelegram/', $scriptProperties)
) {
    return 'Could not load modtelegram class!';
}

$wrapper = $scriptProperties['wrapper'] = $modtelegram->getOption('wrapper', $scriptProperties, '', true);

$helper = trim($modx->getOption('helper', $scriptProperties, '{}', true));
$helper = $scriptProperties['helper'] = strpos($helper, '{') === 0
    ? $modx->fromJSON($helper)
    : array();

$propkey = $scriptProperties['propkey'] = $modx->getOption('propkey', $scriptProperties,
    sha1(serialize($scriptProperties)), true);

$modtelegram->initialize($modx->context->key, $scriptProperties);
$modtelegram->saveProperties($scriptProperties);
$modtelegram->loadResourceJsCss($scriptProperties);

return '';