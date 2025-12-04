<?php

namespace Kelemen\Flow\Action\Command;

use Kelemen\Flow\Action\Action;
use Kelemen\Flow\Renderer\Renderer;
use Symfony\Component\Process\Process;

class RunCommand extends Action
{
    const DEFAULT_TIMEOUT = 60;

    /** @var string */
    private $command;

    /** @var bool */
    private $printOutput;

    /** @var string|null */
    private $cwd;

    /** @var array<string, scalar|null>|null */
    private $env;

    /** @var string|null */
    private $input;

    /** @var int */
    private $timeout;

    /**
     * @param array<string, scalar|null>|null $env
     */
    public function __construct(
        string $command,
        bool $printOutput,
        ?string $cwd = null,
        ?array $env = null,
        ?string $input = null,
        int $timeout = self::DEFAULT_TIMEOUT
    ) {
        $this->command = $command;
        $this->printOutput = $printOutput;
        $this->cwd = $cwd;
        $this->env = $env;
        $this->input = $input;
        $this->timeout = $timeout;
    }

    public function run(Renderer $renderer): void
    {
        $renderer->writeln($this, 'Executing command '.$renderer->highlight($this->command));
        $process = Process::fromShellCommandline($this->command, $this->cwd, $this->env, $this->input, $this->timeout);

        $callback = $this->printOutput
            ? function ($type, string $buffer) use ($renderer): void {
                static $data;

                if ($buffer !== PHP_EOL) {
                    $data .= $buffer;
                    return;
                }

                $renderer->writeln($this, $data, 1);
                $data = '';
            }
            : null;

        $process->run($callback);

        if ($process->isSuccessful()) {
            $renderer->writeSuccess($this, 'Command '.$renderer->highlight($this->command).' was executed');
            return;
        }

        $renderer->writeError($this, 'Command '.$renderer->highlight($this->command).' was not executed');
        $renderer->writeError($this, 'Reason: '.$process->getErrorOutput());
    }
}
