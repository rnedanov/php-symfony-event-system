# php-symfony-event-system

Система управления подписками пользователей с email-уведомлениями на Symfony.

## Технологии

- Symfony 7.3
- PHP 8.3+
- MySQL 8.0+
- Docker
- MailHog (для тестирования email)
- Symfony Messenger (очереди сообщений)

## Требования

- Docker и Docker Compose
- PHP 8.3+ (если запуск без Docker)
- Composer

## Установка и запуск с помощью Docker

1. Склонируйте репозиторий:
   ```bash
   git clone https://github.com/rnedanov/php-symfony-event-system.git
   cd php-symfony-event-system
2. Запустите контейнеры:
    ```bash
    docker compose up -d --build
3. Создайте файл .env из примера:
    ```bash
    cd app
    cp .env.dev.local .env
4. Установите зависимости:
    ```bash
    docker exec -it symfony_event_app composer install
5. Выполните миграции базы данных:
    ```bash
    docker exec symfony_event_app php bin/console doctrine:schema:update --force
6. Создайте администратора:

    ```bash
    docker exec -it symfony_event_app php bin/console app:create-admin
    ```
    Следуйте инструкциям в консоли.

7. Запустите воркер для обработки очереди сообщений:
    ```bash
    docker exec -it symfony_event_app php bin/console messenger:consume async
    ```
Приложение будет доступно по адресу: http://localhost:8000

MailHog (просмотр отправленных писем): http://localhost:8025



## Основные команды
Запускать внутри контейнера symfony_event_app

Создать нового администратора:
```bash
php bin/console app:create-admin
```
Просмотр очереди сообщений:

```bash
php bin/console messenger:stats
```
Сбросить все подписки:

```bash
php bin/console doctrine:query:sql "TRUNCATE user_subscription;"
```
Очистить очередь сообщений:

```bash
php bin/console doctrine:query:sql "TRUNCATE messenger_messages;"
```
