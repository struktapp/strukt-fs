<?php

use Strukt\Fs\Fs;
use Strukt\Fs\Rfs;
use Strukt\Phar as PharAdapter;

if (function_exists('helper')) {
    helper('filesystem');
}

if (! function_exists('fs') && helper_add('fs')) {
    /**
     * Creates a filesystem facade.
     *
     * @param string|null $dir Optional root directory for relative operations.
     * @return Fs|Rfs Static or rooted filesystem facade.
     */
    function fs(?string $dir = null): Fs | Rfs
    {
        return $dir === null ? new Fs() : new Rfs(Fs::ds($dir));
    }
}

if (! function_exists('tail') && helper_add('tail')) {
    /**
     * Reads the final lines of a file.
     *
     * @param string $filepath File path.
     * @param int $lines Number of lines.
     * @return string Trailing file contents.
     */
    function tail(string $filepath, int $lines = 20): string
    {
        return Fs::tail($filepath, $lines);
    }
}

if (! function_exists('ds') && helper_add('ds')) {
    /**
     * Normalizes path separators for the current operating system.
     *
     * @param string $path Path to normalize.
     * @return string Normalized path.
     */
    function ds(string $path): string
    {
        return Fs::ds($path);
    }
}

if (! function_exists('path_exists') && helper_add('path_exists')) {
    /**
     * Checks whether a file or directory exists.
     *
     * @param string $path Path to inspect.
     * @return bool True when the path exists.
     */
    function path_exists(string $path): bool
    {
        return Fs::isPath($path);
    }
}

if (! function_exists('phar') && helper_add('phar')) {
    /**
     * Creates a Phar path adapter.
     *
     * @param string|null $path Optional relative path.
     * @return object Adapter exposing `active()` and `adapt()`.
     */
    function phar(?string $path = null): object
    {
        return new class($path)
        {
            /**
             * Optional path retained by the adapter.
             **/
            private string $path;

            /**
             * @param string|null $path Optional path.
             **/
            public function __construct(?string $path)
            {
                $this->path = $path ?? '';
            }

            /**
             * @return bool Whether Phar execution is active.
             **/
            public function active(): bool
            {
                return PharAdapter::isPhar();
            }

            /**
             * @return string Adapted path.
             **/
            public function adapt(): string
            {
                return PharAdapter::adapt($this->path);
            }
        };
    }
}

if (! function_exists('local') && helper_add('local')) {
    /**
     * Converts an existing path into a file URI.
     *
     * @param string $path Existing path.
     * @return string|null File URI, or `null` when absent.
     */
    function local(string $path): ?string
    {
        if (! Fs::isPath($path)) {
            return null;
        }

        $resolved = realpath($path);
        if ($resolved === false) {
            return null;
        }

        return str_starts_with($path, 'file://') ? $path : 'file://' . $resolved;
    }
}
