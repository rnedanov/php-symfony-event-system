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

# Рефакторинг кода (Задание 1)

```php
<?php
//src/Controller/UserController.php
declare(strict_types=1);

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/users', name: 'user_index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('query')->getData();
            $users = $userRepository->findByName((string) $query);
        } else {
            $users = $userRepository->findAll();
        }

        return $this->render('users/list.html.twig', [
            'users' => $users,
            'form' => $form->createView()
        ]);
    }
}


<?php
//src/Repository/UserRepository.php
namespace App\Repository;

use App\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.name = :name')
            ->setParameter('name', $name)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
