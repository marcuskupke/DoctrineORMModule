<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\AbstractEntityManagerCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\ArgvInput;

use function assert;

/**
 * Builds ORM console commands that require a Doctrine\ORM\Tools\Console\EntityManagerProvider,
 * resolving the entity manager selected via the `--object-manager` CLI option.
 */
final class EntityManagerCommandFactory implements FactoryInterface
{
    private string $defaultObjectManagerName = 'doctrine.entitymanager.orm_default';

    /** @param class-string<AbstractEntityManagerCommand> $commandClass */
    public function __construct(private readonly string $commandClass)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @return AbstractEntityManagerCommand
     */
    public function __invoke(ContainerInterface $container, $requestedName, array|null $options = null)
    {
        $objectManager = $container->get($this->getObjectManagerName());
        assert($objectManager instanceof EntityManagerInterface);

        $commandClass = $this->commandClass;

        return new $commandClass(new SingleManagerProvider($objectManager));
    }

    private function getObjectManagerName(): string
    {
        $arguments = new ArgvInput();

        if (! $arguments->hasParameterOption('--object-manager')) {
            return $this->defaultObjectManagerName;
        }

        return $arguments->getParameterOption('--object-manager');
    }
}
