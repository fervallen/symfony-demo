<?php

namespace App\Controller\Site;

use App\Controller\BaseSiteController;
use App\Entity\Article;
use App\Helper\View\UrlHelper;
use App\Traits\QueryParametersParserTrait;
use App\Traits\SearchTrait;
use App\Traits\ServicesTrait;
use Helpcrunch\Annotation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="search_")
 */
class SearchController extends BaseSiteController
{
    use SearchTrait, QueryParametersParserTrait, ServicesTrait;

    /**
     * @var string $entityClassName
     */
    public static $entityClassName = Article::class;

    /**
     * @Route("/search", name="results")
     * @Annotation\UnauthorizedAction()
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->render(
            'search.html.php',
            $this->searchEntities($request, $this->getSearchService())
        );
    }
}
