<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\KnowledgeBase;
use App\Entity\MenuItem;
use App\Helper\EntityHelper;
use App\Helper\View\TranslateHelper;
use App\Service\KnowledgeBaseInitializerService;
use App\Traits\ServicesTrait;
use App\Traits\VirtualPropertyGetterTrait;
use Doctrine\ORM\EntityManagerInterface;
use Jenssegers\Date\Date;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Repository\HelpcrunchRepository;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper as SymfonyTranslatorHelper;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Templating\PhpEngine;

/**
 * @method EntityManagerInterface getDoctrine
 */
abstract class BaseSiteController  implements ContainerAwareInterface
{
    use CachingTrait, ServicesTrait, VirtualPropertyGetterTrait, ContainerAwareTrait, ControllerTrait;

    const CLASSES_WITH_METADATA = [
        KnowledgeBase::class,
        Article::class,
        Category::class,
    ];

    /**
     * @var string
     */
    protected static $entityClassName;

    /**
     * @var KnowledgeBaseInitializerService $knowledgeBaseInitializerService
     */
    protected $knowledgeBaseInitializerService;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->setLocale($container);
        $this->initializeTranslator($container);
        $this->knowledgeBaseInitializerService = $container->get(KnowledgeBaseInitializerService::class);
    }

    protected function setLocale(ContainerInterface $container): void
    {
        /** @var Translator $translator */
        $translator = $container->get('translator');
        $locale = EntityHelper::getKnowledgeBase()->locale;
        $translator->setLocale($locale);
        Date::setLocale($locale);
    }

    protected function initializeTranslator(ContainerInterface $container): void
    {
        /** @var PhpEngine $templating */
        $templating = $container->get('templating');
        if (empty($templating['translator'])) {
            throw new \Exception('Can not initialize knowledge base client without translator');
        }
        /** @var SymfonyTranslatorHelper $symfonyTranslatorHelper */
        $symfonyTranslatorHelper = $templating['translator'];
        TranslateHelper::setSymfonyTranslatorHelper($symfonyTranslatorHelper);
    }

    /**
     * @param Article|Category|KnowledgeBase|HelpcrunchEntity $entity
     * @return array
     */
    protected function collectMetaData(HelpcrunchEntity $entity): array
    {
        if (!in_array(get_class($entity), self::CLASSES_WITH_METADATA)) {
            return [];
        }

        if ($entity instanceof KnowledgeBase) {
            $pageTitle = $entity->title;
        } else {
            $pageTitle = $entity->pageTitle ?: $entity->title;
        }

        return [
            'pageTitle' => $pageTitle,
            'metaDescription' => $entity->metaDescription ?: $entity->description,
            'metaKeywords' => $entity->metaKeywords,
            'ogTitle' => $entity->ogTitle ?: $pageTitle,
            'ogDescription' => $entity->ogDescription ?: $entity->description,
            'ogImage' => empty($entity->ogImage) ? $entity->ogImage : null,
        ];
    }

    protected function getParameter(string $name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * @return ObjectRepository|HelpcrunchRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(static::$entityClassName);
    }
}
