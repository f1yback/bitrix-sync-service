**Требования:**

1) PHP 8.1
2) MySQL 8+
3) Redis

**Перед выходом в продакшен:**

1) Настроить params
2) Настроить конфигурацию БД
3) Настроить Redis
4) Запустить `php yii webhook/subscribe`
5) Демонизировать `php yii queue/listen`
6) Запустить `php yii sync/manager`

**Запуск cron скриптов:**

1) `php yii log/rotate & //время 00 23 * * *`
2) `php yii sync/clients & //время * * * * *`
3) `php yii sync/client & //время * * * * *`
4) `php yii sync/manager & //время 0 * * * *`
5) `php yii sync/bitrix & //время */5 * * * *`
6) `php yii sync/task & //время 50 23 * * *`