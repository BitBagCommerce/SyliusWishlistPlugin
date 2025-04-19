<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->import('vendor/bitbag/coding-standard/ecs.php');
    $config->paths(['src', 'spec', 'tests/Integration', 'tests/Behat', 'tests/Functional']);
};
