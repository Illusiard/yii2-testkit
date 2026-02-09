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

**Test Suites Overview (Unit / Integration / Functional)**

- `unit`: тесты без Yii, базовый `UnitTestCase`.
- `integration`: тесты с Yii, обычно на основе `YiiTestCase`.
- `functional`: функциональные тесты в App Mode через модуль Yii2 и `configFile` приложения.

**License**

BSD-3-Clause. Подробности в `LICENSE`.
