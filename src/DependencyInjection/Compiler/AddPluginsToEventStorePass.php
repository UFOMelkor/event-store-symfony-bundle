<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/event-store-symfony-bundle for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/event-store-symfony-bundle/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Prooph\Bundle\EventStore\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AddPluginsToEventStorePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasParameter('prooph_event_store.stores')) {
            return;
        }

        $storeNames = array_keys($container->getParameter('prooph_event_store.stores'));
        $globalPluginIds = array_keys($container->findTaggedServiceIds('prooph_event_store.plugin'));

        foreach ($storeNames as $name) {
            $storePluginIds = array_keys($container->findTaggedServiceIds("prooph_event_store.$name.plugin"));

            $pluginIds = array_merge($globalPluginIds, $storePluginIds);

            $eventStoreDefinition = $container->findDefinition("prooph_event_store.$name");
            $eventStoreDefinition->addArgument($pluginIds);
        }
    }
}
