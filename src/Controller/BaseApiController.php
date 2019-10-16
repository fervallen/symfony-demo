<?php

namespace App\Controller;

use App\Traits\ServicesTrait;
use App\Traits\VirtualPropertyGetterTrait;
use Helpcrunch\Controller\HelpcrunchController;

abstract class BaseApiController extends HelpcrunchController
{
    use CachingTrait, ServicesTrait;

    const DEFAULT_PAGINATION_LIMIT = 50;
}
