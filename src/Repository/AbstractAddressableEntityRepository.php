<?php

namespace App\Repository;

use App\Entity\AbstractAddressableEntity;
use Helpcrunch\Repository\HelpcrunchRepository;

abstract class AbstractAddressableEntityRepository extends HelpcrunchRepository
{
    /**
     * @param string $slug
     * @return AbstractAddressableEntity[]
     */
    public function findBySimilarSlug(string $slug): array
    {
        $queryBuilder = $this->createQueryBuilder('entity');

        return $queryBuilder
            ->select('entity.slug')
            ->where($queryBuilder->expr()->like('entity.slug', ':slug'))
            ->setParameter('slug', $slug . '%')
            ->orderBy('entity.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
