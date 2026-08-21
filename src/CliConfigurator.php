<?php

declare(strict_types=1);

namespace DoctrineORMModule;

use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Laminas\Stdlib\ArrayUtils;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputOption;

use function class_exists;

class CliConfigurator
{
    private string $defaultObjectManagerName = 'doctrine.entitymanager.orm_default';

    /** @var string[] */
    private array $commands = [
        'doctrine.dbal_cmd.runsql',
        'doctrine.orm_cmd.clear_cache_metadata',
        'doctrine.orm_cmd.clear_cache_result',
        'doctrine.orm_cmd.clear_cache_query',
        'doctrine.orm_cmd.schema_tool_create',
        'doctrine.orm_cmd.schema_tool_update',
        'doctrine.orm_cmd.schema_tool_drop',
        'doctrine.orm_cmd.generate_proxies',
        'doctrine.orm_cmd.run_dql',
        'doctrine.orm_cmd.validate_schema',
        'doctrine.orm_cmd.info',
    ];

    /** @var string[] */
    private array $migrationCommands = [
        'doctrine.migrations_cmd.current',
        'doctrine.migrations_cmd.diff',
        'doctrine.migrations_cmd.dumpschema',
        'doctrine.migrations_cmd.execute',
        'doctrine.migrations_cmd.generate',
        'doctrine.migrations_cmd.latest',
        'doctrine.migrations_cmd.list',
        'doctrine.migrations_cmd.migrate',
        'doctrine.migrations_cmd.rollup',
        'doctrine.migrations_cmd.status',
        'doctrine.migrations_cmd.syncmetadatastorage',
        'doctrine.migrations_cmd.version',
        'doctrine.migrations_cmd.uptodate',
    ];

    public function __construct(private ContainerInterface $container)
    {
    }

    public function configure(Application $cli): void
    {
        $commands = $this->getAvailableCommands();
        foreach ($commands as $commandName) {
            $command = $this->container->get($commandName);
            $command->getDefinition()->addOption($this->createObjectManagerInputOption());

            $cli->add($command);
        }

        $cli->getHelperSet()->set(new QuestionHelper(), 'dialog');
    }

    private function createObjectManagerInputOption(): InputOption
    {
        return new InputOption(
            'object-manager',
            null,
            InputOption::VALUE_OPTIONAL,
            'The name of the object manager to use.',
            $this->defaultObjectManagerName,
        );
    }

    /** @return string[] */
    private function getAvailableCommands(): array
    {
        $commands = $this->commands;
        if (class_exists(ReservedWordsCommand::class)) {
            $commands[] = 'doctrine.dbal_cmd.reserved_words';
        }

        if (class_exists(VersionCommand::class)) {
            $commands = ArrayUtils::merge($commands, $this->migrationCommands);
        }

        return $commands;
    }
}
