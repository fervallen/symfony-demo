<?php

namespace App\EventListener;

use App\Entity\AbstractAddressableEntity;
use App\Entity\Article;
use App\Entity\MenuItem;
use App\Entity\Section;
use App\Repository\AbstractAddressableEntityRepository;
use App\Traits\PositionableTrait;
use App\Traits\ServicesTrait;
use Helpcrunch\Entity\HelpcrunchEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PositionSetterListener
{
    use ServicesTrait;

    const POSITION_STEP = 1;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(HelpcrunchEntity $entity): void
    {
        if ((
            ($entity instanceof AbstractAddressableEntity) ||
            (in_array(PositionableTrait::class, class_uses($entity)))
        ) && !$entity->position && ($entity->position !== 0) && ($entity->position !== "0")) {
            $entity->position = $this->getPosition($entity);
        }
    }

    private function getPosition(HelpcrunchEntity $entity): int
    {
        $criteria = [];
        if ($entity instanceof Article) {
            $criteria = [
                'category' => $entity->category,
                'section' => $entity->section,
            ];
        }

        $lastCreatedEntity = $this->getLastCreatedEntity(get_class($entity), $criteria);

        return !empty($lastCreatedEntity)
            ? ($lastCreatedEntity->position + self::POSITION_STEP)
            : AbstractAddressableEntity::$defaultPosition;
    }

    /**
     * @param string $entityClass
     * @param array $criteria
     * @return object|null
     */
    private function getLastCreatedEntity(string $entityClass, array $criteria)
    {
        /** @var AbstractAddressableEntityRepository $repository */
        $repository = $this->getEntityManager()->getRepository($entityClass);

        return $repository->findOneBy($criteria, [
            'position' => 'DESC',
        ]);
    }
}
