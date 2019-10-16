<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Rating;
use App\Entity\Section;
use App\Service\ReportService;
use App\Traits\ReportQueryBuilderTrait;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends AbstractAddressableEntityRepository
{
    use ReportQueryBuilderTrait;

    const REPORT_DATE_FIELD_ALIAS = ReportService::RATINGS_ALIAS;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Article[]
     */
    public function findAllPublic(): array
    {
        return $this->findBy(
            ['status' => Article::STATUS_PUBLIC],
            ['id' => 'DESC']
        );
    }

    /**
     * @param string $previewKey
     * @return Article|null
     */
    public function findByPreviewKey(string $previewKey)
    {
        return $this->createQueryBuilder('articles')
            ->where('articles.previewKey = :previewKey')
            ->andWhere('articles.status = :status')
            ->setParameter('previewKey', $previewKey)
            ->setParameter('status', Article::STATUS_DRAFT)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $searchQuery
     * @param array $options
     * @return Article[]
     */
    public function findArticlesByText(string $searchQuery, array $options = []): array
    {
        $queryBuilder = $this->getArticleSearchQueryBuilder($searchQuery);
        $queryBuilder = $this->addOptionsToSearchRequest($queryBuilder, $options);

        return $queryBuilder->getQuery()->getResult();
    }

    private function addOptionsToSearchRequest(QueryBuilder $queryBuilder, array $options): QueryBuilder
    {
        if (!empty($options['limit'])) {
            $queryBuilder->setMaxResults($options['limit']);
        }

        if (!empty($options['offset'])) {
            $queryBuilder->setFirstResult($options['offset']);
        }

        if (!empty($options['order']) && !empty($options['orderBy'])) {
            $queryBuilder->orderBy($options['orderBy'], $options['order']);
        }

        return $queryBuilder;
    }

    public function countArticlesByText(string $searchQuery): int
    {
        return $this->getArticleSearchQueryBuilder($searchQuery)
            ->select('COUNT(articles.id) as articlesCount')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getArticleSearchQueryBuilder(string $searchQuery): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('articles');

        return $queryBuilder->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('LOWER(articles.content)', ':searchQuery'),
                $queryBuilder->expr()->like('LOWER(articles.description)', ':searchQuery'),
                $queryBuilder->expr()->like('LOWER(articles.title)', ':searchQuery')
            ))
            ->andWhere('articles.status = :status')
            ->setParameter('searchQuery', '%' . strtolower($searchQuery) . '%')
            ->setParameter('status', Article::STATUS_PUBLIC);
    }

    /**
     * @param array $parameters
     * @param string $type
     * @return Article[]
     */
    public function findForReporting(array $parameters, string $type): array
    {
        $this->getEntityName();
        $groupByField = ReportService::ARTICLES_ALIAS;

        $queryBuilder = $this->createQueryBuilder(ReportService::ARTICLES_ALIAS)
            ->select(ReportService::ARTICLES_ALIAS, 'COUNT(' . ReportService::RATINGS_ALIAS . '.type) as badRatingsCount')
            ->innerJoin(
                ReportService::ARTICLES_ALIAS . '.' . ReportService::RATINGS_ALIAS,
                ReportService::RATINGS_ALIAS,
                Join::WITH,
                ReportService::RATINGS_ALIAS . '.type = :type'
            )->setParameter('type', $type);

        return $this->processQueryBuilder($queryBuilder, $parameters)
            ->groupBy($groupByField)
            ->orderBy('badRatingsCount')
            ->getQuery()
            ->getResult();
    }
}
