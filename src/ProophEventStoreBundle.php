<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/event-store-symfony-bundle for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/event-store-symfony-bundle/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Prooph\Bundle\EventStore;

use Prooph\Bundle\EventStore\DependencyInjection\Compiler\MetadataEnricherPass;
use Prooph\Bundle\EventStore\DependencyInjection\Compiler\RegisterPluginLocatorPass;
use Prooph\Bundle\EventStore\DependencyInjection\Compiler\AddPluginsToEventStorePass;
use Prooph\Bundle\EventStore\DependencyInjection\Compiler\RegisterProjectionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ProophEventStoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MetadataEnricherPass());
        $container->addCompilerPass(new AddPluginsToEventStorePass());
        $container->addCompilerPass(new RegisterProjectionsPass());
        $container->addCompilerPass(new RegisterPluginLocatorPass());
    }
}
