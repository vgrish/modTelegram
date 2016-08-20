<?php

include_once 'errors.inc.php';
include_once 'setting.inc.php';

$_lang['modtelegram'] = 'modtelegram';
$_lang['modtelegram_helper'] = 'Помощник';


$_lang['modtelegram_chat_welcome'] = 'Приветствую!';
$_lang['modtelegram_chat_initialize'] = 'Начать чат';
$_lang['modtelegram_chat_message'] = 'Введите сообщение...';
$_lang['modtelegram_chat_sendmessage'] = 'Отправить сообщение';
$_lang['modtelegram_chat_attachfile'] = 'Отправить файл';


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
$_lang['modtelegram_description_action_history'] = '[[+action]] - получить историю чата';
$_lang['modtelegram_description_action_reply'] = '[[+action]] - написать в чат';
$_lang['modtelegram_description_action_status'] = '[[+action]] - статус';
$_lang['modtelegram_description_action_location'] = '[[+action]] - получить локацию';
$_lang['modtelegram_description_action_removeall'] = '[[+action]] - удалить все данные';

// info

$_lang['modtelegram_chatin'] = '/chatin_[[+uid]]';
$_lang['modtelegram_history'] = '[[+sender]]: [[+data]] - [[+message]]';
$_lang['modtelegram_status'] = '[[+user_username]]: статус - [[+active]], чатов - [[+chat_count]]';
$_lang['modtelegram_location'] = '[[+region.name_ru]], [[+city.name_ru]] [ [[+ip]] ]';

$_lang['modtelegram_default_user'] = 'Пользователь';
$_lang['modtelegram_default_manager'] = 'Менеджер';

$_lang['modtelegram_hook_info_success_login'] = 'Вы успешно вошли в систему';
$_lang['modtelegram_hook_info_failure_login'] = 'Ошибка входа';


$_lang['modtelegram_hook_info_success_logout'] = 'Вы успешно вышли из системы';
$_lang['modtelegram_hook_info_failure_logout'] = 'Ошибка выхода';

$_lang['modtelegram_hook_info_success_chatin'] = 'Вы успешно вошли в чат';
$_lang['modtelegram_hook_info_failure_chatin'] = 'Ошибка подключения чата';

$_lang['modtelegram_hook_info_success_chatout'] = 'Вы успешно вышли из чата';
$_lang['modtelegram_hook_info_failure_chatout'] = 'Ошибка отключения чата';

$_lang['modtelegram_hook_info_success_history'] = 'История чата';
$_lang['modtelegram_hook_info_failure_history'] = 'Ошибка получения истории';

$_lang['modtelegram_hook_info_success_status'] = 'Статус';
$_lang['modtelegram_hook_info_failure_status'] = 'Ошибка получения статуса';

$_lang['modtelegram_hook_info_success_location'] = 'Локация пользователя';
$_lang['modtelegram_hook_info_failure_location'] = 'Ошибка получения локации';

$_lang['modtelegram_hook_info_success_removeall'] = 'Данные успешно удалены';
$_lang['modtelegram_hook_info_failure_removeall'] = 'Ошибка удаления данных';


$_lang['modtelegram_chatin_manager_info_failure_initialize'] = '';
$_lang['modtelegram_chatin_manager_info_success_initialize'] = 'У вас новый чат /chatin_[[+uid]]';

$_lang['modtelegram_chatin_user_info_failure_initialize'] = 'Нет доступных менеджеров';
$_lang['modtelegram_chatin_user_info_success_initialize'] = 'Приветствую, чем могу помочь?';

$_lang['modtelegram_chatin_user_info_failure_sendmessage'] = 'Ошибка отправки сообщения';
$_lang['modtelegram_chatin_user_info_success_sendmessage'] = '';


$_lang['modtelegram_event_user_chatout_success'] = 'Пользователь покинул чат /chatout_[[+uid]]';
$_lang['modtelegram_event_user_chatout_failure'] = '';


//$_lang['modtelegram_chat_user_info_failure_initialize'] = 'Нет доступных менеджеров';
//$_lang['modtelegram_chat_user_info_success_initialize'] = 'Приветствую, чем могу вам помочь?';


