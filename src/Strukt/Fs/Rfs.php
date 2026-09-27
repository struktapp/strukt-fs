<?php

namespace Strukt\Fs;

use Strukt\Contract\RfsInterface;

/** Relative filesystem facade that normalizes paths once at the boundary. */
final class Rfs implements RfsInterface
{
    /** Root directory for all relative operations. */
    private string $root;

    /**
     * Creates a rooted filesystem facade.
     *
     * @param string $path Root directory.
     */
    public function __construct(string $path)
    {
        $path = Fs::ds($path);
        $this->root = rtrim($path, '/\\');
        if ($this->root === '') {
            $this->root = '.';
        }
    }

    /**
     * Resolves a relative path below the root.
     *
     * @param string $path Relative path.
     * @return string Resolved path.
     */
    public function path(string $path): string
    {
        $path = trim($path);
        if ($path === '' || $path === '.') {
            return $this->root;
        }

        return Fs::ds($this->root . DIRECTORY_SEPARATOR . ltrim($path, '/\\'));
    }

    /** @param string $dir Relative directory path. @return bool Whether it exists. */
    public function isDir(string $dir): bool
    {
        return Fs::isDir($this->path($dir));
    }

    /** @param string $file Relative file path. @return bool Whether it exists. */
    public function isFile(string $file): bool
    {
        return Fs::isFile($this->path($file));
    }

    /** @param string $path Relative path. @return bool Whether it exists. */
    public function isPath(string $path): bool
    {
        return Fs::isPath($this->path($path));
    }

    /** @param string $file Relative file path. @return string|false Contents. */
    public function cat(string $file): string|false
    {
        return Fs::cat($this->path($file));
    }

    /** @param string $file Relative file path. @return bool Whether created. */
    public function touch(string $file): bool
    {
        return Fs::touch($this->path($file));
    }

    /** @param string $file Relative file path. @param string $contents Contents. @return bool Whether written. */
    public function touchWrite(string $file, string $contents): bool
    {
        return Fs::touchWrite($this->path($file), $contents);
    }

    /** @param string $from Relative source. @param string $to Relative destination. @return bool Whether renamed. */
    public function rename(string $from, string $to): bool
    {
        return Fs::rename($this->path($from), $this->path($to));
    }

    /** @param string $file Relative file path. @param string $contents Contents. @param bool $noLockEx Whether to omit locking. @return int|false Bytes written. */
    public function overwrite(string $file, string $contents, bool $noLockEx = true): int|false
    {
        return Fs::overwrite($this->path($file), $contents, $noLockEx);
    }

    /** @param string $file Relative file path. @param string $contents Contents. @param bool $noLockEx Whether to omit locking. @return int|false Bytes written. */
    public function appendWrite(string $file, string $contents, bool $noLockEx = true): int|false
    {
        return Fs::appendWrite($this->path($file), $contents, $noLockEx);
    }

    /** @param string $file Relative file path or wildcard. @return bool Whether removed. */
    public function rm(string $file): bool
    {
        return Fs::rm($this->path($file));
    }

    /** @param string $dir Relative directory path. @return bool Whether removed. */
    public function rmdir(string $dir): bool
    {
        return Fs::rmdir($this->path($dir));
    }

    /** @param string $dir Relative directory path. @param int $mode Permissions. @param bool $recursive Whether to create parents. @return bool Whether it exists. */
    public function mkdir(string $dir, int $mode = 0755, bool $recursive = true): bool
    {
        return Fs::mkdir($this->path($dir), $mode, $recursive);
    }

    /** @param string $file Relative path. @return bool Whether writable. */
    public function isWritable(string $file): bool
    {
        return Fs::isWritable($this->path($file));
    }

    /** @param string $file Relative path. @return bool Whether readable. */
    public function isReadable(string $file): bool
    {
        return Fs::isReadable($this->path($file));
    }

    /** @param string $source Relative source. @param string $dest Relative destination. @return void */
    public function copyRecur(string $source, string $dest): void
    {
        Fs::copyRecur($this->path($source), $this->path($dest));
    }

    /** @param string $source Relative source. @param string $dest Relative destination. @return void */
    public function cpr(string $source, string $dest): void
    {
        $this->copyRecur($source, $dest);
    }

    /** @param string $path Relative directory path. @return list<string> Entry names. */
    public function listFiles(string $path = '.'): array
    {
        return Fs::listFiles($this->path($path));
    }

    /** @param string $path Relative directory path. @return list<string> Entry names. */
    public function ls(string $path = '.'): array
    {
        return $this->listFiles($path);
    }

    /** @param string $path Relative directory path. @return list<string> File paths. */
    public function listFilesRecur(string $path = '.'): array
    {
        return Fs::listFilesRecur($this->path($path));
    }

    /** @param string $path Relative directory path. @return list<string> File paths. */
    public function lsr(string $path = '.'): array
    {
        return $this->listFilesRecur($path);
    }

    /** @param string $filepath Relative file path. @param int $lines Number of lines. @return string Trailing contents. */
    public function tail(string $filepath, int $lines = 20): string
    {
        return Fs::tail($this->path($filepath), $lines);
    }

    /** @param string $path Relative source directory. @param string|null $zipfile Relative archive path. @return bool Whether archived. */
    public function zip(string $path, ?string $zipfile = null): bool
    {
        return Fs::zip($this->path($path), $zipfile === null ? null : $this->path($zipfile));
    }

    /** @param string $zipfile Relative archive path. @param array<int, string> $files Relative files. @return bool Whether archived. */
    public function addZipList(string $zipfile, array $files): bool
    {
        $absoluteFiles = array_map(fn (string $file): string => $this->path($file), $files);

        return Fs::addZipList($this->path($zipfile), $absoluteFiles);
    }

    /** @param string $zipfile Relative archive path. @param string $topath Relative destination parent. @return bool Whether extracted. */
    public function unzip(string $zipfile, string $topath = './'): bool
    {
        return Fs::unzip($this->path($zipfile), $this->path($topath));
    }

    /** @param string $zippath Relative archive path. @return string Entry names. */
    public function lsz(string $zippath): string
    {
        return Fs::lsz($this->path($zippath));
    }

    /**
     * Parses a rooted INI file.
     *
     * @param string $path Relative INI path.
     * @param bool $sections Whether to preserve sections.
     * @return array<int|string, mixed> Parsed values.
     */
    public function ini(string $path, bool $sections = true): array
    {
        return Fs::ini($this->path($path), $sections);
    }

    /**
     * Includes a rooted PHP file.
     *
     * @param string $path Relative PHP path.
     * @return mixed Included file result.
     */
    public function req(string $path): mixed
    {
        return Fs::req($this->path($path));
    }
}
