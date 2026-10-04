<?php
namespace Strukt\Contract;

/**
 * Relative filesystem contract rooted at a configured directory.
 */
interface RfsInterface
{
    /**
     * @param string $path Relative path.
     */
    public function path(string $path): string;

    /**
     * @param string $dir Relative directory path.
     */
    public function isDir(string $dir): bool;

    /**
     * @param string $file Relative file path.
     */
    public function isFile(string $file): bool;

    /**
     * @param string $path Relative path.
     */
    public function isPath(string $path): bool;

    /**
     * @param string $file Relative file path.
     */
    public function cat(string $file): string | false;

    /**
     * @param string $file Relative file path.
     */
    public function touch(string $file): bool;

    /**
     * @param string $file Relative file path.
     * @param string $contents Contents.
     */
    public function touchWrite(string $file, string $contents): bool;

    /**
     * @param string $from Relative source.
     * @param string $to Relative destination.
     */
    public function rename(string $from, string $to): bool;

    /**
     * @param string $file Relative file path.
     * @param string $contents Contents.
     * @param bool $noLockEx Whether to omit `LOCK_EX`.
     */
    public function overwrite(string $file, string $contents, bool $noLockEx = true): int | false;

    /**
     * @param string $file Relative file path.
     * @param string $contents Contents.
     * @param bool $noLockEx Whether to omit `LOCK_EX`.
     */
    public function appendWrite(string $file, string $contents, bool $noLockEx = true): int | false;

    /**
     * @param string $file Relative file path or wildcard.
     */
    public function rm(string $file): bool;

    /**
     * @param string $dir Relative directory path.
     */
    public function rmdir(string $dir): bool;

    /**
     * @param string $dir Relative directory path.
     * @param int $mode Permissions.
     * @param bool $recursive Whether to create parents.
     */
    public function mkdir(string $dir, int $mode = 0755, bool $recursive = true): bool;

    /**
     * @param string $file Relative path.
     */
    public function isWritable(string $file): bool;

    /**
     * @param string $file Relative path.
     */
    public function isReadable(string $file): bool;

    /**
     * @param string $source Relative source.
     * @param string $dest Relative destination.
     */
    public function copyRecur(string $source, string $dest): void;

    /**
     * @param string $source Relative source.
     * @param string $dest Relative destination.
     */
    public function cpr(string $source, string $dest): void;

    /**
     * @param string $path Relative directory path.
     */
    public function listFiles(string $path = '.'): array;

    /**
     * @param string $path Relative directory path.
     */
    public function ls(string $path = '.'): array;

    /**
     * @param string $path Relative directory path.
     */
    public function listFilesRecur(string $path = '.'): array;

    /**
     * @param string $path Relative directory path.
     */
    public function lsr(string $path = '.'): array;

    /**
     * @param string $filepath Relative file path.
     * @param int $lines Number of lines.
     */
    public function tail(string $filepath, int $lines = 20): string;

    /**
     * @param string $path Relative directory path.
     * @param string|null $zipfile Relative archive path.
     */
    public function zip(string $path, ?string $zipfile = null): bool;

    /**
     * @param string $zipfile Relative archive path.
     * @param array<int, string> $files Relative files.
     */
    public function addZipList(string $zipfile, array $files): bool;

    /**
     * @param string $zipfile Relative archive path.
     * @param string $topath Relative destination parent.
     */
    public function unzip(string $zipfile, string $topath = './'): bool;

    /**
     * @param string $zippath Relative archive path.
     */
    public function lsz(string $zippath): string;
}
