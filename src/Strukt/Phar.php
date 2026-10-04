<?php
namespace Strukt;

use Strukt\Fs\Fs;

/**
 * Adapts relative paths when code runs from a Phar archive.
 **/
final class Phar
{
    /**
     * Resolves a path against the active Phar or current working directory.
     *
     * @param string $path Relative path.
     * @return string Adapted path.
     */
    public static function adapt(string $path = ''): string
    {
        $root = \Phar::running() ?: (getcwd() ?: '.');

        return Fs::ds(rtrim($root, '/\\') . DIRECTORY_SEPARATOR . ltrim($path, '/\\'));
    }

    /**
     * Checks whether the current file is executing inside a Phar.
     *
     * @return bool True when running from a Phar archive.
     */
    public static function isPhar(): bool
    {
        if (! class_exists('Phar')) {
            return false;
        }

        $archive = \Phar::running(true);

        return $archive !== '' && str_starts_with(__FILE__, $archive);
    }
}
