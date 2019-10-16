<?php

namespace App\Controller;

use \Memcached;

/**
 * @property Memcached $memcached
 */
trait CachingTrait
{
    public function getFromCache(string $id)
    {
        return $this->memcached->get($id);
    }

    public function setToCache(string $id, $data, int $expiration = null): void
    {
        $this->memcached->set($id, $data, $expiration);
    }

    public function deleteFromCache(string $id): void
    {
        $this->memcached->delete($id);
    }
}
