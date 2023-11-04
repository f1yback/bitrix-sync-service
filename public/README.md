**Требования:**

1) PHP 8.1+
2) MySQL 8+
3) Redis 5+
4) nginx 1.20+

**Установка:**

1) Git:
   1) делаем `git clone` репозитория 
   2) `composer install`
2) Настроить конфигурацию БД `config/db.php`
   3) прописать корректное значение dsn (host & dbname)
   4) прописать корректное значение username
   5) прописать корректное значение password
3) `php yii migrate`

**Перед выходом в продакшен:**

1) Настроить `config/params.php`:
   1) указать новый (если нужно) вебхук битрикс24
   2) указать новые (если нужно) креды приложения
   3) **указать свой вебхук сервера (обязательно)**
2) Установить Redis и убедиться, что он активен:
   1) `sudo apt install redis`
   2) `sudo systemctl status redis`
   3) в `config/redis.php` в hostname прописать корректный host, если он отличается от localhost
3) Демонизировать `php yii queue/listen`:
   1) `sudo vim /etc/systemd/system/redis-listener.service`
   2) пишем туда то, что указано в файле `./daemon`
   3) `/path/to/project/root`, который был указан в `./daemon` меняем на путь к проекту (корень), где лежит файл yii
   4) сохраняем
   5) `sudo systemctl enable redis-listener`
   6) `sudo systemctl start redis-listener`
   7) проверяем, что демон работает - `sudo systemctl status redis-listener`
4) Запустить один раз `php yii sync/manager`
5) Запустить один раз `php yii webhook/subscribe`

**Устанавливаем задачи cron:**

1) `50 23 * * * /usr/bin/php -f /path/to/project/root/yii log/rotate`
2) `*/30 * * * * /usr/bin/php -f /path/to/project/root/yii sync/clients`
3) `0 * * * * /usr/bin/php -f /path/to/project/root/yii sync/client`
4) `0 * * * * /usr/bin/php -f /path/to/project/root/yii sync/manager`
5) `0 * * * * /usr/bin/php -f /path/to/project/root/yii sync/bitrix`
6) `50 23 * * * /usr/bin/php -f /path/to/project/root/yii sync/task`

**Дополнительно:**

1) В архиве есть готовое окружение, для запуска через докер, при желании
2) Логи запросов к АПИ и Битрикс24 можно посмотреть в папке `./logs`. Данные логи автоматически очищаются со временем
3) Также пишется лог битых запросов (где ответ от сервера отличается от 200) в таблицу `broken_requests`