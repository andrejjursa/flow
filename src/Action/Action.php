<?php

namespace Kelemen\Flow\Action;

use Kelemen\Flow\Renderer\Renderer;

abstract class Action
{
    /** @var int */
    private $level = 1;

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getNextLevel(): int
    {
        return $this->level + 1;
    }

    /**
     * Run another sub-action
     */
    protected function runAction(Action $action, Renderer $renderer): void
    {
        $action->setLevel($this->getNextLevel());
        $action->run($renderer);
    }

    /**
     * Execute action
     */
    abstract public function run(Renderer $renderer): void;
}
