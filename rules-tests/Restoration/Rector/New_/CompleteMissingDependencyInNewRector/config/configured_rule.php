<?php

declare(strict_types=1);

use Rector\Restoration\Rector\New_\CompleteMissingDependencyInNewRector;
use Rector\Tests\Restoration\Rector\New_\CompleteMissingDependencyInNewRector\Source\RandomDependency;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(CompleteMissingDependencyInNewRector::class)
        ->call('configure', [[
            CompleteMissingDependencyInNewRector::CLASS_TO_INSTANTIATE_BY_TYPE => [
                RandomDependency::class => RandomDependency::class,
            ],
        ]]);
};
