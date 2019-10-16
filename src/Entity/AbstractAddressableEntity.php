<?php

namespace App\Entity;

use App\Traits\PositionableTrait;
use Doctrine\ORM\Mapping as ORM;
use Helpcrunch\Entity\HelpcrunchEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 * @property string $title
 * @property string $slug
 */
abstract class AbstractAddressableEntity extends HelpcrunchEntity
{
    use PositionableTrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="string", name="slug", unique=true, length=255, nullable=false)
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     * @var string
     */
    protected $slug;
}
