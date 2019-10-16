<?php

namespace App\EventListener;

use App\Entity\AbstractAddressableEntity;
use App\Repository\AbstractAddressableEntityRepository;
use Ausi\SlugGenerator\SlugGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class SlugGeneratorListener
{
    const SLUG_DELIMITER = '-';
    const SLUG_STEP = 1;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(AbstractAddressableEntity $entity): void
    {
        if (!$entity->slug) {
            if (!$entity->title) {
                throw new InvalidArgumentException('Entity ' . get_class($entity) . ' has to have title!');
            }

            $entity->slug = $this->generateSlug($entity->title);
        }

        $entity->slug = $this->addSlugSuffixIfDuplicate($entity->slug, get_class($entity));
    }

    private function generateSlug(string $slug): string
    {
        return (new SlugGenerator())->generate($slug);
    }

    private function addSlugSuffixIfDuplicate(string $slug, string $entityClass): string
    {
        /** @var AbstractAddressableEntityRepository $repository */
        $repository = $this->container->get('doctrine')->getRepository($entityClass);
        $entitiesWithSimilarSlug = $repository->findBySimilarSlug($slug);
        $usedSlugSuffixes = array_map(function ($entity) use ($slug) {
            return str_replace($slug, '', $entity['slug']);
        }, $entitiesWithSimilarSlug);

        if (!empty($usedSlugSuffixes)) {
            $uniqSuffixNumber = 1;
            do {
                $slugSuffix = self::SLUG_DELIMITER . $uniqSuffixNumber;
                $uniqSuffixNumber += self::SLUG_STEP;
            } while (in_array($slugSuffix, $usedSlugSuffixes));

            $slug .= $slugSuffix;
        }

        return $slug;
    }
}
