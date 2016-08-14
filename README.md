## modTelegram

### Требования

**https** для webhook.php

### Установка

* добавить бота @BotFather и создать бота
* получить токен и добавить его в настройки
* установить webhook, выполнив

```
$modtelegram = $modx->getService('modtelegram');
$modtelegram->telegramSetWebHook();
```

### Доступные действия

* **action** список доступных действий

```
/action
```

* **login** авторизоваться в системе

```
/login_username_password
```

* **logout** выйти из системы

```
/logout
```

* **chatin** подключить чат

```
/chatin_id
```

* **chatout** отключить чат

```
/chatout
```

* **history** получить историю чата

```
/history_id
```

* **reply** написать в активный чат

```
/reply_message
```

* **location** получить локацию

```
/location
```
