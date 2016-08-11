<?php

include_once 'errors.inc.php';
include_once 'setting.inc.php';

$_lang['modtelegram'] = 'modtelegram';


// actions

$_lang['modtelegram_actions'] = 'Действия';
$_lang['modtelegram_action_view'] = 'Просмотреть';
$_lang['modtelegram_action_repeat'] = 'Повторить';
$_lang['modtelegram_action_cart'] = 'В корзину';
$_lang['modtelegram_action_pay'] = 'Оплатить';
$_lang['modtelegram_action_cancel'] = 'Отменить';
$_lang['modtelegram_action_close'] = 'Закрыть';


// description

$_lang['modtelegram_description_action_action'] = '[[+action]] - получить список доступных действий';
$_lang['modtelegram_description_action_login'] = '[[+action]] - авторизоваться в системе';
$_lang['modtelegram_description_action_logout'] = '[[+action]] - выйти из системы';
$_lang['modtelegram_description_action_chatin'] = '[[+action]] - подключить чат';
$_lang['modtelegram_description_action_chatout'] = '[[+action]] - отключить чат';
$_lang['modtelegram_description_action_story'] = '[[+action]] - получить историю чата';


// info


$_lang['modtelegram_hook_info_success_login'] = 'Вы успешно вошли в систему';
$_lang['modtelegram_hook_info_failure_login'] = 'Ошибка входа';


$_lang['modtelegram_hook_info_success_logout'] = 'Вы успешно вышли из системы';
$_lang['modtelegram_hook_info_failure_logout'] = 'Ошибка выхода';

$_lang['modtelegram_hook_info_success_chatin'] = 'Вы успешно вошли в чат';
$_lang['modtelegram_hook_info_failure_chatin'] = 'Ошибка подключения чата';

$_lang['modtelegram_hook_info_success_chatout'] = 'Вы успешно вышли из чата';
$_lang['modtelegram_hook_info_failure_chatout'] = 'Ошибка отключения чата';


$_lang['modtelegram_chatin_manager_info_failure_initialize'] = '';
$_lang['modtelegram_chatin_manager_info_success_initialize'] = 'У вас новый чат /chatin_[[+uid]]';

$_lang['modtelegram_chatin_user_info_failure_initialize'] = 'Нет доступных менеджеров';
$_lang['modtelegram_chatin_user_info_success_initialize'] = '';




$_lang['modtelegram_chat_user_info_failure_initialize'] = 'Нет доступных менеджеров';
$_lang['modtelegram_chat_user_info_success_initialize'] = 'Приветствую, чем могу вам помочь?';


