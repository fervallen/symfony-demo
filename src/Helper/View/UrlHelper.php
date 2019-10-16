<?php

namespace App\Helper\View;

use App\Entity\Article;
use App\Entity\AbstractAddressableEntity;
use Symfony\Component\Routing\Router;

class UrlHelper
{
    const CATEGORY_SHOW_ROUTE = 'category_show';
    const ARTICLE_SHOW_ROUTE = 'article_show';
    const ARTICLE_PREVIEW_ROUTE = 'article_preview';
    const HOMEPAGE_ROUTE = 'homepage';

    /**
     * @var Router
     */
    private static $router;

    public static function getEntityUrl(AbstractAddressableEntity $entity): string
    {
        if ($entity instanceof Article) {
            return self::getArticleUrl($entity);
        }

        throw new \Exception(get_class($entity) . ' is not configured to have a route');
    }

    public static function getArticleUrl(Article $article): string
    {
        if ($article->isPublic()) {
            return self::generateRouteUrl(self::ARTICLE_SHOW_ROUTE, [
                'articleSlug' => $article->slug,
            ]);
        }

        return self::generateRouteUrl(self::ARTICLE_PREVIEW_ROUTE, [
            'previewKey' => $article->previewKey,
        ]);
    }

    private static function generateRouteUrl(string $routeName, array $options = []): string
    {
        return self::$router->generate($routeName, $options, Router::ABSOLUTE_URL);
    }

    public static function setRouter(Router $router): void
    {
        self::$router = $router;
    }

    public static function getCurrentUrl(): string
    {
        $url = 'http';
        if (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
            $url .= 's';
        }
        $url .= '://' . $_SERVER['HTTP_HOST'];
        $url .= parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        return $url;
    }
}
