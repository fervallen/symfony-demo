<?php

use App\Helper\View\I18ns;

$reflectionClass = new ReflectionClass(I18ns::class);
$messages = [];
foreach ($reflectionClass->getConstants() as $value) {
    $messages[$value] = $value;
}

return $messages;
