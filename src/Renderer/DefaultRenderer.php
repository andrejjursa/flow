<?php

namespace Kelemen\Flow\Action;

use Kelemen\Flow\Renderer\Renderer;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultRenderer implements Renderer
{
    const MULTIPLICATION = 2;

    /** @var OutputInterface */
    private $output;

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
        $this->registerStyles($this->output);
    }

    public function write(Action $action, string $msg, int $innerLevel = 0): void
    {
        $this->output->write(
            str_repeat(' ', ($action->getLevel() + $innerLevel) * self::MULTIPLICATION).'-> '.$msg
        );
    }

    public function writeln(Action $action, string $msg, int $innerLevel = 0): void
    {
        $this->output->writeln(
            str_repeat(' ', ($action->getLevel() + $innerLevel) * self::MULTIPLICATION).'-> '.$msg
        );
    }

    public function writeSkip(Action $action, string $msg, int $innerLevel = 0): void
    {
        $this->writeln($action, '<yellow>[SKIPPING]</yellow> '.$msg, $innerLevel);
    }

    public function writeSuccess(Action $action, string $msg, int $innerLevel = 0): void
    {
        $this->writeln($action, '<info>[SUCCESS]</info> '.$msg, $innerLevel);
    }

    public function writeError(Action $action, string $msg, int $innerLevel = 0): void
    {
        $this->writeln($action, '<error>[ERROR]</error> '.$msg, $innerLevel);
    }

    public function highlight(string $msg): string
    {
        return '<magenta>'.$msg.'</magenta>';
    }

    public function writeCommandSeparator(): void
    {
        $this->output->writeln("\n **** Command ****\n");
    }

    /**
     * Register new styles
     */
    private function registerStyles(OutputInterface $output): void
    {
        $output->getFormatter()->setStyle('error', new OutputFormatterStyle('red', 'black'));
        $output->getFormatter()->setStyle('yellow', new OutputFormatterStyle('yellow', 'black'));
        $output->getFormatter()->setStyle('magenta', new OutputFormatterStyle('magenta', 'black'));
    }
}
