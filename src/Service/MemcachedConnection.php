<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Memcached;

/**
 * TODO: I believe this class belongs to HelpcrunchSymfony
 */
class MemcachedConnection
{
    /**
     * @var Memcached $connection
     */
    private $connection;

    public function __construct(ContainerInterface $container)
    {
        $this->connection = MemcachedAdapter::createConnection(
            $container->getParameter('memcached_connection')
        );
    }

    public function get(string $id)
    {
        return $this->connection->get($id);
    }

    public function set(string $id, $data, int $expiration = null): void
    {
        $this->connection->set($id, $data, $expiration);
    }

    public function delete(string $id): void
    {
        $this->connection->delete($id);
    }
}
