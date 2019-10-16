<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Helpcrunch\Entity\HelpcrunchEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SearchQueryLogRepository")
 * @ORM\Table(name="search_query_logs")
 *
 * @property string $text
 * @property \DateTime $createdAt
 * @property int $resultsCount
 */
class SearchQueryLog extends HelpcrunchEntity
{
    /**
     * @ORM\Column(type="string", name="text", length=300, nullable=false)
     * @Assert\Type(type="string")
     * @Assert\Length(max=300)
     * @Assert\NotBlank()
     * @var int
     */
    protected $text;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     * @Assert\DateTime()
     * @Serializer\Type("DateTime<'Y-m-d H:i'>")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer", name="results_count", length=10, nullable=false)
     * @Assert\Type(type="integer")
     * @Assert\Length(max=10)
     * @var int
     */
    protected $resultsCount;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}
