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

use Prooph\EventStore\Metadata\MetadataEnricherPlugin;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class MetadataEnricherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasParameter('prooph_event_store.stores')) {
            return;
        }

        $storeNames = array_keys($container->getParameter('prooph_event_store.stores'));

        $globalPluginIds = array_keys($container->findTaggedServiceIds('prooph_event_store.metadata_enricher'));

        foreach ($storeNames as $name) {
            $storeEnricherPluginIds = array_keys($container->findTaggedServiceIds("prooph_event_store.$name.metadata_enricher"));
            $pluginIds = array_merge($globalPluginIds, $storeEnricherPluginIds);
            $enrichers = [];

            foreach ($pluginIds as $id) {
                $enrichers[] = new Reference($id);
            }

            $metadataEnricherAggregateId = "prooph_event_store.metadata_enricher_aggregate.$name";
            $metadataEnricherAggregateDefinition = $container->getDefinition($metadataEnricherAggregateId);
            $metadataEnricherAggregateDefinition->setArguments([$enrichers]);

            $metadataEnricherId = "prooph_event_store.metadata_enricher_plugin.$name";
            $metadataEnricherDefinition = $container->getDefinition($metadataEnricherId);
            $metadataEnricherDefinition->setClass(MetadataEnricherPlugin::class);
            $metadataEnricherDefinition->addTag("prooph_event_store.$name.plugin");
            $metadataEnricherDefinition->setArguments([new Reference($metadataEnricherAggregateId)]);
        }
    }
}
