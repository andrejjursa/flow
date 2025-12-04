<?php

namespace Kelemen\Flow;

use Kelemen\Flow\Action\Action;
use Kelemen\Flow\Action\Command\RunCommand;
use Kelemen\Flow\Action\Composer\InstallComposer;
use Kelemen\Flow\Action\Composer\UpdateComposer;
use Kelemen\Flow\Action\Database\Mysql\CreateDatabaseMysql;
use Kelemen\Flow\Action\Database\Mysql\DropDatabaseMysql;
use Kelemen\Flow\Action\DefaultRenderer;
use Kelemen\Flow\Action\Directory\CreateDirectory;
use Kelemen\Flow\Action\Directory\DeleteDirectory;
use Kelemen\Flow\Action\Directory\MoveDirectory;
use Kelemen\Flow\Renderer\Renderer;
use Symfony\Component\Console\Output\OutputInterface;

class Flow
{
    const DEFAULT_MODE = 0777;

    const DEFAULT_TIMEOUT = 60;

    /** @var Renderer */
    private $renderer;

    /** @var array<Action> */
    private $actions = [];

    public function __construct(?Renderer $renderer = null)
    {
        $this->renderer = $renderer ?: new DefaultRenderer();
    }

    /**
     * @template T of Action
     * @param T $action
     * @return T
     */
    public function addAction(Action $action): Action
    {
        $this->actions[] = $action;
        return $action;
    }

    public function run(OutputInterface $output): void
    {
        $this->renderer->setOutput($output);

        foreach ($this->actions as $action) {
            $this->renderer->writeCommandSeparator();
            $action->run($this->renderer);
        }
    }

    /**********************************************************\
     * Wrapper functions
    \**********************************************************/

    public function createDirectory(string $dir, int $mode = self::DEFAULT_MODE, bool $recursive = false): CreateDirectory
    {
        return $this->addAction(new CreateDirectory($dir, $mode, $recursive));
    }

    /**
     * Create new directory, delete if already exists
     */
    public function createDirectoryForce(string $dir, int $mode = self::DEFAULT_MODE, bool $recursive = false): CreateDirectory
    {
        return $this->addAction(new CreateDirectory($dir, $mode, $recursive, true));
    }

    public function deleteDirectory(string $dir): DeleteDirectory
    {
        return $this->addAction(new DeleteDirectory($dir));
    }

    /**
     * Move directory to new destination
     */
    public function moveDirectory(string $oldDirName, string $newDirName): MoveDirectory
    {
        return $this->addAction(new MoveDirectory($oldDirName, $newDirName));
    }

    /**
     * Run any shell command
     * @param array<string, scalar|null>|null $env
     */
    public function runCommand(
        string $command,
        bool $printOutput = false,
        ?string $cwd = null,
        ?array $env = null,
        ?string $input = null,
        int $timeout = self::DEFAULT_TIMEOUT
    ): RunCommand {
        return $this->addAction(new RunCommand($command, $printOutput, $cwd, $env, $input, $timeout));
    }

    /**
     * Create new mysql database
     */
    public function createDatabaseMysql(string $user, string $password, string $dbName): CreateDatabaseMysql
    {
        return $this->addAction(new CreateDatabaseMysql($user, $password, $dbName));
    }

    /**
     * Drop mysql database
     */
    public function dropDatabaseMysql(string $user, string $password, string $dbName): DropDatabaseMysql
    {
        return $this->addAction(new DropDatabaseMysql($user, $password, $dbName));
    }

    /**
     * Execute composer update
     */
    public function composerUpdate(string $dir): UpdateComposer
    {
        return $this->addAction(new UpdateComposer($dir));
    }

    /**
     * Execute composer install
     */
    public function composerInstall(string $dir): InstallComposer
    {
        return $this->addAction(new InstallComposer($dir));
    }
}
