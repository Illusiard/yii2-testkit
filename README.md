# illusiard/yii2-testkit

**Short Description**

Минимальный каркас для тестирования Yii2 расширений и приложений. Пакет даёт базовые TestCase и шаблоны Codeception без лишней логики.

**Installation**

Установите пакет как dev-зависимость:

```bash
composer require --dev illusiard/yii2-testkit
```

**Using In Yii2 Extension (Extension Mode)**

В расширении используйте `YiiTestCase` для поднятия минимального приложения:

```php
use Illusiard\Yii2Testkit\Testing\YiiTestCase;

final class MyServiceTest extends YiiTestCase
{
    protected function appConfig(): array
    {
        return [
            'id' => 'test-app',
            'basePath' => __DIR__,
        ];
    }

    public function testSomething(): void
    {
        $this->assertNotNull(\Yii::$app);
    }
}
```

**Using In Yii2 Application (Basic / Advanced)**

В приложении используйте шаблоны `resources/codeception/*.yml` и настройте `configFile`/`entryScript` под структуру вашего проекта. Приложение поднимается самим Codeception через модуль Yii2.

**Test Stand**

Пакет теперь включает минимальный встроенный слой test stand для Docker: `nginx + php-fpm` поднимаются всегда, а дополнительные сервисы выбираются через `Illusiard\Yii2Testkit\TestStand\TestServices`.

Для работы test stand нужен установленный `docker compose`.

Пример:

```php
use Illusiard\Yii2Testkit\TestStand\StandConfig;
use Illusiard\Yii2Testkit\TestStand\StandManager;

$config = StandConfig::load(__DIR__ . '/tests/_config/stand.php');
$stand = new StandManager();

$stand->up($config, true);
$stand->logs($config);
$stand->down($config, true);
```

Структура конфига:

```php
return [
    'services' => ['mysql', 'redis'],
    'envFiles' => ['/abs/path/to/test.env'],
    'appConfig' => [
        'projectRoot' => '/abs/path/to/project',
        'webRoot' => '/abs/path/to/project/web',
        'entryScript' => 'index-test.php',
    ],
];
```

Особенности:

- `app` не указывается в `services`: `nginx` и `php-fpm` поднимаются автоматически.
- Логи сохраняются в `tests/_output/testkit/` файлами `docker-compose.log` и `services.log`.
- Host port для nginx задаётся через переменную окружения `TESTKIT_HTTP_PORT`. Если она не задана, используется `8080`.
- `envFiles` передаются в контейнеры как runtime env-файлы, но не используются для выбора host port.

Поддерживаемые сервисы:

- `mysql`
- `postgresql`
- `redis`
- `rabbitmq`

Ограничения текущей версии: только встроенная docker-структура пакета (без внешних compose файлов), только HTTP, без миграций и фикстур, без HTTP-клиента и без app runner вне Docker.

**Test Suites Overview (Unit / Integration / Functional)**

- `unit`: тесты без Yii, базовый `UnitTestCase`.
- `integration`: тесты с Yii, обычно на основе `YiiTestCase`.
- `functional`: функциональные тесты в App Mode через модуль Yii2 и `configFile` приложения.

**License**

BSD-3-Clause. Подробности в `LICENSE`.
