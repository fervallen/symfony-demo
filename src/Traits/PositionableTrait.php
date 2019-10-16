<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property int $position
 */
trait PositionableTrait
{
    /**
     * @Serializer\Exclude
     * @var int
     */
    public static $defaultPosition = 0;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     * @Assert\Type(type="integer")
     * @var int
     */
    protected $position;
}
