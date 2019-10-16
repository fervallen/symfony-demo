<?php

namespace App\Entity;

use App\Entity\VirtualEntity\Author;
use App\Helper\EntityHelper;
use App\Helper\View\UrlHelper;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table(name="articles")
 * @ORM\EntityListeners({
 *     "App\EventListener\ArticleRevisionListener",
 *     "App\EventListener\PositionSetterListener",
 *     "App\EventListener\SlugGeneratorListener",
 *     "App\EventListener\EntityNotificationSocketListener"
 * })
 *
 * @property string $content
 * @property DateTime $createdAt
 * @property string $description
 * @property string|null $metaDescription
 * @property string|null $metaKeywords
 * @property string|null $ogDescription
 * @property string|null $ogImage
 * @property string|null $ogTitle
 * @property string|null $pageTitle
 * @property string|null $previewKey
 * @property bool $seoVisible
 * @property string $status
 * @property DateTime $updatedAt
 */
class Article extends AbstractAddressableEntity implements EntityWithVirtualPropertiesInterface
{
    const ORDER_BY_FIELDS = [
        'title',
        'content',
        'createdAt',
    ];
    const PREVIEW_KEY_LENGTH = 16;
    const STATUS_PUBLIC = 'public';
    const STATUS_DRAFT = 'draft';

    /**
     * @ORM\Column(type="text", name="content", nullable=true)
     * @Assert\Type(type="string")
     * @var string
     */
    protected $content;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     * @Assert\DateTime()
     * @Serializer\Type("DateTime<'Y-m-d H:i'>")
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     * @var string|null
     */
    protected $description = null;

    /**
     * @ORM\Column(type="string", name="meta_description", length=255, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     * @var string|null
     */
    protected $metaDescription = null;

    /**
     * @ORM\Column(type="string", name="meta_keywords", length=255, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     * @var string|null
     */
    protected $metaKeywords = null;

    /**
     * @ORM\Column(type="string", name="og_description", length=255, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     * @var string|null
     */
    protected $ogDescription = null;

    /**
     * @ORM\Column(type="string", name="og_image", length=50, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=50)
     * @var string|null
     */
    protected $ogImage = null;

    /**
     * @ORM\Column(type="string", name="og_title", length=255, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     * @var string|null
     */
    protected $ogTitle = null;

    /**
     * @ORM\Column(type="string", name="page_title", length=255, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     * @var string|null
     */
    protected $pageTitle = null;

    /**
     * @ORM\Column(type="string", name="preview_key", length=32, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=32)
     * @var string|null
     */
    protected $previewKey = null;

    /**
     * @ORM\Column(type="boolean", name="seo_visible", options={"default"=false})
     * @Assert\Type(type="bool")
     * @var bool
     */
    protected $seoVisible = false;

    /**
     * @ORM\Column(type="string", name="status", length=50, nullable=false, options={"default"="draft"})
     * @Assert\Type(type="string")
     * @Assert\Length(max=50)
     * @var string
     */
    protected $status = self::STATUS_DRAFT;

    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=false)
     * @Assert\DateTime()
     * @Serializer\Type("DateTime<'Y-m-d H:i'>")
     * @var DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->setStatus(self::STATUS_DRAFT);
    }

    // Use this setter for doctrine's 'postUpdate' event. It doesn't work without calling setter directly.
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, [self::STATUS_DRAFT, self::STATUS_PUBLIC])) {
            $status = self::STATUS_DRAFT;
        }
        $this->status = $status;
        if ($this->isDraft() && !$this->previewKey) {
            $this->generatePreviewKey();
        } elseif ($this->isPublic()) {
            $this->previewKey = null;
        }

        return $this;
    }

    public function isDraft(): bool
    {
        return $this->status == self::STATUS_DRAFT;
    }

    private function generatePreviewKey(): void
    {
        $this->previewKey = bin2hex(random_bytes(self::PREVIEW_KEY_LENGTH));
    }

    public function isPublic(): bool
    {
        return $this->status == self::STATUS_PUBLIC;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("preview")
     */
    public function getPreviewUrl(): string
    {
        return UrlHelper::getArticleUrl($this);
    }
}
