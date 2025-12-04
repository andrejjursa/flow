<?php

namespace Kelemen\Flow\Action\Directory;

use Kelemen\Flow\Action\Action;
use Kelemen\Flow\Renderer\Renderer;

class DeleteDirectory extends Action
{
    /** @var string */
    private $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function run(Renderer $renderer): void
    {
        $renderer->writeln($this, 'Deleting directory '.$renderer->highlight($this->dir));

        if (!is_dir($this->dir)) {
            $renderer->writeSkip($this, 'Directory '.$renderer->highlight($this->dir).' not found');
            return;
        }

        $this->remove($this->dir)
            ? $renderer->writeSuccess($this, 'Directory '.$renderer->highlight($this->dir).' was deleted')
            : $renderer->writeError($this, 'Directory '.$renderer->highlight($this->dir).' was not deleted');
    }

    /**
     * Remove dir recursive
     */
    private function remove(string $dir): bool
    {
        $dirFiles = scandir($dir);
        if ($dirFiles === false) {
            return false;
        }

        $files = array_diff($dirFiles, ['.', '..']);
        foreach ($files as $file) {
            $path = sprintf('%s/%s', $dir, $file);
            if (is_dir($path)) {
                $this->remove($path);
                continue;
            }

            @unlink($path);
        }

        return @rmdir($dir);
    }
}
