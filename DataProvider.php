<?php declare(strict_types = 1);

namespace src\Integration;

/**
 * Class DataProvider
 *
 * @package src\Integration
 */
class DataProvider implements Providerable
{
    private $host;
    private $user;
    private $password;

    private $logger;

    /**
     * DataProvider constructor.
     *
     * @param string               $host
     * @param string               $user
     * @param string               $password
     * @param LoggerInterface|null $logger
     */
    public function __construct(string $host, string $user, string $password, LoggerInterface $logger = null)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;

        $this->logger = ($logger === null) ? $this->container->get('service.logger') : $logger;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    public function get(array $request)
    {
        try {

            // returns a response from external service

        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
