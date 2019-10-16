<?php

namespace App\Controller\Site;

use App\Controller\BaseSiteController;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Traits\QueryParametersParserTrait;
use App\Traits\SearchTrait;
use Helpcrunch\Annotation;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="article_")
 * @method ArticleRepository getRepository()
 */
class ArticleController extends BaseSiteController
{
    use HelpcrunchServicesTrait, SearchTrait, QueryParametersParserTrait;

    /**
     * @var string $entityClassName
     */
    public static $entityClassName = Article::class;

    /**
     * @Route("/preview/{previewKey}", name="preview")
     * @Annotation\UnauthorizedAction()
     * @param string $previewKey
     * @return Response
     */
    public function previewAction($previewKey): Response
    {
        $article = $this->getArticleRepository()->findByPreviewKey($previewKey);
        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }
        $article->seoVisible = false;

        return $this->renderArticle($article);
    }

    /**
     * @Route(
     *     "/{categorySlug}/{articleSlug}",
     *     name="show",
     *     requirements={"categorySlug": "[A-Za-z0-9-]+", "articleSlug": "[A-Za-z0-9-]+"}
     * )
     * @Annotation\UnauthorizedAction()
     * @param string $categorySlug
     * @param string $articleSlug
     * @return Response
     */
    public function showAction(string $categorySlug, string $articleSlug): Response
    {
        $article = $this->getArticleRepository()->findOneByArticleAndCategorySlug($articleSlug, $categorySlug);
        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }
        $article->incrementArticleViews();

        return $this->renderArticle($article);
    }

    private function renderArticle(Article $article): Response
    {
        /** @var Article $article */
        $article = $this->addVirtualProperties($article);
        $this->getArticleRatingFillerService()->fillEntity($article);

        return $this->render('article.html.php', [
            'article' => $article,
            'breadcrumbsEntity' => $article,
            'metaData' => $this->collectMetaData($article),
        ]);
    }
}
