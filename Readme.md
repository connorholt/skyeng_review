  1. DecoratorManager название не раскрывает что делает класс
  2. $this->logger->critical('Error'); не информативное сообщение в логе, хотя есть $exception, и можно нормально сообщение сделать
  3. Размытые обязанности у декоратора, он и кеширует (+обязанность уметь делать ключ кеша) и логирует, и при этом т.к. наследник DataProvider,
  весь функционал и обязанности дата провайдера тоже переходят ему.
  4. Логер устанавливается отдельным методом, по дефолту никакого нет, т.е. если я вызову getResponse
  могу словить вызов $this->logger->critical('Error') на null
  5. У декоратора должна храниться ссылка на объект, который он декоррирует и индентичный интерфейс, в этом классе DecoratorManager
  сделано не так, и еще ловится исключение
  6. Чтобы сохранить кеш надо вызвать $this->cache->save($cacheItem);

 
```php
<?php declare(strict_types = 1);
namespace src\Integration;

class DataProvider
{
    private $host;
    private $user;
    private $password;

    /**
     * @param $host
     * @param $user
     * @param $password
     */
    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    public function get(array $request)
    {
        // returns a response from external service
    }
}


namespace src\Decorator;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;

class DecoratorManager extends DataProvider
{
    public $cache;
    public $logger;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param CacheItemPoolInterface $cache
     */
    public function __construct($host, $user, $password, CacheItemPoolInterface $cache)
    {
        parent::__construct($host, $user, $password);
        $this->cache = $cache;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(array $input)
    {
        try {
            $cacheKey = $this->getCacheKey($input);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = parent::get($input);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify('+1 day')
                );

            return $result;
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }

    public function getCacheKey(array $input)
    {
        return json_encode($input);
    }
}
