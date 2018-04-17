<?php

declare(strict_types=1);

namespace Prooph\Bundle\EventStore\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class RegisterPluginLocatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasParameter('prooph_event_store.stores')) {
            return;
        }

        $storeNames = array_keys($container->getParameter('prooph_event_store.stores'));

        $globalPluginIds = array_keys($container->findTaggedServiceIds('prooph_event_store.plugin'));
        $storePluginIds = [];

        foreach ($storeNames as $name) {
            $storePluginIds[] = array_keys($container->findTaggedServiceIds("prooph_event_store.$name.plugin"));
        }

        $pluginIds = array_merge($globalPluginIds, ...$storePluginIds);

        $pluginsLocator = [];

        foreach ($pluginIds as $id) {
            $pluginsLocator[$id] = new ServiceClosureArgument(new Reference($id));
        }

        $container
            ->setDefinition(
                'prooph_event_store.plugins_locator',
                new Definition(ServiceLocator::class, [$pluginsLocator])
            )
            ->addTag('container.service_locator');
    }
}
