<?php
namespace Strukt\Contract;

/**
 * Static filesystem contract for local and stream-wrapper paths.
 **/
interface FsInterface
{
    /**
     * @param string $dir Directory path.
     **/
    public static function isDir(string $dir): bool;

    /**
     * @param string $file File path.
     **/
    public static function isFile(string $file): bool;

    /**
     * @param string $path File or directory path.
     **/
    public static function isPath(string $path): bool;

    /**
     * @param string $file File path.
     **/
    public static function cat(string $file): string | false;

    /**
     * @param string $file File path.
     **/
    public static function touch(string $file): bool;

    /**
     * @param string $file File path.
     * @param string $contents Contents.
     **/
    public static function touchWrite(string $file, string $contents): bool;

    /**
     * @param string $from Source path.
     * @param string $to Destination path.
     **/
    public static function rename(string $from, string $to): bool;

    /**
     * @param string $file File path.
     * @param string $contents Contents.
     * @param bool $noLockEx Whether to omit `LOCK_EX`.
     **/
    public static function overwrite(string $file, string $contents, bool $noLockEx = true): int | false;

    /**
     * @param string $file File path.
     * @param string $contents Contents.
     * @param bool $noLockEx Whether to omit `LOCK_EX`.
     **/
    public static function appendWrite(string $file, string $contents, bool $noLockEx = true): int | false;

    /**
     * @param string $file File path or wildcard.
     **/
    public static function rm(string $file): bool;

    /**
     * @param string $dir Directory path.
     **/
    public static function rmdir(string $dir): bool;

    /**
     * @param string $dir Directory path.
     * @param int $mode Permissions.
     * @param bool $recursive Whether to create parents.
     **/
    public static function mkdir(string $dir, int $mode = 0755, bool $recursive = true): bool;

    /**
     * @param string $file File or directory path.
     **/
    public static function isWritable(string $file): bool;

    /**
     * @param string $file File or directory path.
     **/
    public static function isReadable(string $file): bool;

    /**
     * @param string $source Source path.
     * @param string $dest Destination path.
     **/
    public static function copyRecur(string $source, string $dest): void;

    /**
     * @param string $source Source path.
     * @param string $dest Destination path.
     **/
    public static function cpr(string $source, string $dest): void;

    /**
     * @param string $path Directory path.
     **/
    public static function listFiles(string $path = '.'): array;

    /**
     * @param string $path Directory path.
     **/
    public static function ls(string $path = '.'): array;

    /**
     * @param string $path Directory path.
     **/
    public static function listFilesRecur(string $path = '.'): array;

    /**
     * @param string $path Directory path.
     **/
    public static function lsr(string $path = '.'): array;

    /**
     * @param string $filepath File path.
     * @param int $lines Number of lines.
     **/
    public static function tail(string $filepath, int $lines = 20): string;

    /**
     * @param string $path Directory path.
     * @param string|null $zipfile Output archive path.
     **/
    public static function zip(string $path, ?string $zipfile = null): bool;

    /**
     * @param string $zipfile Output archive path.
     * @param array<int, string> $files Files to add.
     **/
    public static function addZipList(string $zipfile, array $files): bool;

    /**
     * @param string $zipfile Archive path.
     * @param string $topath Destination parent.
     **/
    public static function unzip(string $zipfile, string $topath = './'): bool;

    /**
     * @param string $zippath Archive path.
     **/
    public static function lsz(string $zippath): string;
}
