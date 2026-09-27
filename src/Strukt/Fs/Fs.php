<?php

namespace Strukt\Fs;

use Strukt\Contract\FsInterface;

/** Efficient static filesystem operations for local and stream-wrapper paths. */
final class Fs implements FsInterface
{
    /**
     * Checks whether a directory exists.
     *
     * @param string $dir Directory path.
     * @return bool True when the path is a directory.
     */
    public static function isDir(string $dir): bool
    {
        return is_dir($dir);
    }

    /**
     * Checks whether a regular file exists.
     *
     * @param string $file File path.
     * @return bool True when the path is a regular file.
     */
    public static function isFile(string $file): bool
    {
        return is_file($file);
    }

    /**
     * Checks whether a file or directory exists.
     *
     * @param string $path Path to inspect.
     * @return bool True when the path exists.
     */
    public static function isPath(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Reads a file without emitting a warning for unreadable paths.
     *
     * @param string $file File path.
     * @return string|false Contents, or `false` when unavailable.
     */
    public static function cat(string $file): string|false
    {
        if (!is_file($file)) {
            return false;
        }

        return @file_get_contents($file);
    }

    /**
     * Creates a file only when it does not already exist.
     *
     * @param string $file File path.
     * @return bool True when the file was created.
     */
    public static function touch(string $file): bool
    {
        return !is_file($file) && @touch($file);
    }

    /**
     * Creates and writes a new file.
     *
     * @param string $file File path.
     * @param string $contents Contents to write.
     * @return bool True when the complete contents were written.
     */
    public static function touchWrite(string $file, string $contents): bool
    {
        $parent = dirname($file);
        if ($parent !== '.' && !self::mkdir($parent)) {
            return false;
        }

        if (!self::touch($file)) {
            return false;
        }

        return self::overwrite($file, $contents) === strlen($contents);
    }

    /**
     * Renames a file, backing up an existing destination first.
     *
     * @param string $from Source file.
     * @param string $to Destination file.
     * @return bool True when the rename succeeds.
     */
    public static function rename(string $from, string $to): bool
    {
        if ($from === $to || !is_file($from)) {
            return false;
        }

        if (is_file($to)) {
            $backup = self::backupPath($to);
            if (!@rename($to, $backup)) {
                return false;
            }
        }

        return @rename($from, $to);
    }

    /**
     * Replaces a file's contents.
     *
     * @param string $file File path.
     * @param string $contents Contents to write.
     * @param bool $noLockEx Whether to omit an exclusive lock.
     * @return int|false Number of bytes written, or `false`.
     */
    public static function overwrite(string $file, string $contents, bool $noLockEx = true): int|false
    {
        if (!is_file($file)) {
            return false;
        }

        $flags = $noLockEx ? 0 : LOCK_EX;

        return @file_put_contents($file, $contents, $flags);
    }

    /**
     * Appends contents to a file.
     *
     * @param string $file File path.
     * @param string $contents Contents to append.
     * @param bool $noLockEx Whether to omit an exclusive lock.
     * @return int|false Number of bytes written, or `false`.
     */
    public static function appendWrite(string $file, string $contents, bool $noLockEx = true): int|false
    {
        if (!is_file($file)) {
            return false;
        }

        $flags = FILE_APPEND | ($noLockEx ? 0 : LOCK_EX);

        return @file_put_contents($file, $contents, $flags);
    }

    /**
     * Removes a file or all files matching a wildcard.
     *
     * @param string $file File path or glob pattern.
     * @return bool True when every target was removed.
     */
    public static function rm(string $file): bool
    {
        $targets = strpbrk($file, '*?[') === false
            ? [$file]
            : (glob($file, GLOB_NOSORT) ?: []);

        if ($targets === []) {
            return false;
        }

        foreach ($targets as $target) {
            if (!is_file($target) && !is_link($target)) {
                return false;
            }
            if (!@unlink($target)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Removes a directory tree, including hidden entries.
     *
     * @param string $dir Directory path.
     * @return bool True when the tree was removed.
     */
    public static function rmdir(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST,
            );

            foreach ($iterator as $item) {
                $path = $item->getPathname();
                if ($item->isDir() && !$item->isLink()) {
                    if (!@rmdir($path)) {
                        return false;
                    }
                } elseif (!@unlink($path)) {
                    return false;
                }
            }
        } catch (\UnexpectedValueException) {
            return false;
        }

        return @rmdir($dir);
    }

    /**
     * Creates a directory tree idempotently.
     *
     * @param string $dir Directory path.
     * @param int $mode Permissions.
     * @param bool $recursive Whether to create missing parents.
     * @return bool True when the directory exists after the call.
     */
    public static function mkdir(string $dir, int $mode = 0755, bool $recursive = true): bool
    {
        if (is_dir($dir)) {
            return true;
        }

        return @mkdir($dir, $mode, $recursive) || is_dir($dir);
    }

    /**
     * Checks writability without an extra existence call.
     *
     * @param string $file File or directory path.
     * @return bool True when writable.
     */
    public static function isWritable(string $file): bool
    {
        return is_writable($file);
    }

    /**
     * Checks readability without an extra existence call.
     *
     * @param string $file File or directory path.
     * @return bool True when readable.
     */
    public static function isReadable(string $file): bool
    {
        return is_readable($file);
    }

    /**
     * Copies a file or directory tree.
     *
     * @param string $source Source path.
     * @param string $dest Destination path.
     * @return void
     * @throws \RuntimeException When a source or copy operation is invalid.
     */
    public static function copyRecur(string $source, string $dest): void
    {
        if (is_file($source)) {
            $target = is_dir($dest)
                ? self::ds(rtrim($dest, '/\\') . DIRECTORY_SEPARATOR . basename($source))
                : $dest;
            if (!self::mkdir(dirname($target)) || !@copy($source, $target)) {
                throw new \RuntimeException(sprintf('Unable to copy [%s] to [%s].', $source, $target));
            }

            return;
        }

        if (!is_dir($source)) {
            throw new \RuntimeException(sprintf('Source path [%s] does not exist.', $source));
        }

        if (!self::mkdir($dest)) {
            throw new \RuntimeException(sprintf('Unable to create destination [%s].', $dest));
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST,
            );

            foreach ($iterator as $item) {
                $target = self::ds(rtrim($dest, '/\\') . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                if ($item->isDir()) {
                    if (!self::mkdir($target)) {
                        throw new \RuntimeException(sprintf('Unable to create destination [%s].', $target));
                    }
                    continue;
                }

                if (!@copy($item->getPathname(), $target)) {
                    throw new \RuntimeException(sprintf('Unable to copy [%s] to [%s].', $item->getPathname(), $target));
                }
            }
        } catch (\UnexpectedValueException $exception) {
            throw new \RuntimeException(sprintf('Unable to read source [%s].', $source), 0, $exception);
        }
    }

    /**
     * Alias for `copyRecur()`.
     *
     * @param string $source Source path.
     * @param string $dest Destination path.
     * @return void
     */
    public static function cpr(string $source, string $dest): void
    {
        self::copyRecur($source, $dest);
    }

    /**
     * Lists immediate entries excluding `.` and `..`.
     *
     * @param string $path Directory path.
     * @return list<string> Sorted entry names.
     */
    public static function listFiles(string $path = '.'): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $entries = scandir($path);

        return $entries === false ? [] : array_values(array_diff($entries, ['.', '..']));
    }

    /**
     * Alias for `listFiles()`.
     *
     * @param string $path Directory path.
     * @return list<string> Entry names.
     */
    public static function ls(string $path = '.'): array
    {
        return self::listFiles($path);
    }

    /**
     * Lists every regular file below a directory.
     *
     * @param string $path Directory path.
     * @return list<string> File paths.
     */
    public static function listFilesRecur(string $path = '.'): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $files = [];
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            );
            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    $files[] = $item->getPathname();
                }
            }
        } catch (\UnexpectedValueException) {
            return [];
        }

        return $files;
    }

    /**
     * Alias for `listFilesRecur()`.
     *
     * @param string $path Directory path.
     * @return list<string> File paths.
     */
    public static function lsr(string $path = '.'): array
    {
        return self::listFilesRecur($path);
    }

    /**
     * Checks whether PHP is running on Windows.
     *
     * @return bool True on Windows.
     */
    public static function isWindows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    /**
     * Converts both slash styles to the current directory separator.
     *
     * @param string $path Path to normalize.
     * @return string Normalized path.
     */
    public static function dirSep(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Alias for `dirSep()`.
     *
     * @param string $path Path to normalize.
     * @return string Normalized path.
     */
    public static function ds(string $path): string
    {
        return self::dirSep($path);
    }

    /**
     * Reads the final lines of a file without scanning its prefix.
     *
     * @param string $filepath File path.
     * @param int $lines Number of lines to return.
     * @return string Trailing file contents.
     */
    public static function tail(string $filepath, int $lines = 20): string
    {
        if ($lines <= 0 || !is_file($filepath)) {
            return '';
        }

        $handle = @fopen($filepath, 'rb');
        if ($handle === false) {
            return '';
        }

        $position = fseek($handle, 0, SEEK_END) === 0 ? ftell($handle) : false;
        if ($position === false || $position === 0) {
            fclose($handle);
            return '';
        }

        $buffer = '';
        $chunkSize = 8192;
        do {
            $readSize = min($chunkSize, $position);
            $position -= $readSize;
            fseek($handle, $position);
            $chunk = fread($handle, $readSize);
            if ($chunk === false) {
                fclose($handle);
                return '';
            }
            $buffer = $chunk . $buffer;
        } while ($position > 0 && substr_count($buffer, "\n") <= $lines);

        fclose($handle);

        $newlineCount = substr_count($buffer, "\n");
        $lineCount = str_ends_with($buffer, "\n") ? $newlineCount : $newlineCount + 1;
        $skip = $lineCount - $lines;
        if ($skip <= 0) {
            return $buffer;
        }

        $offset = 0;
        for ($index = 0; $index < $skip; $index++) {
            $newline = strpos($buffer, "\n", $offset);
            if ($newline === false) {
                return '';
            }
            $offset = $newline + 1;
        }

        return substr($buffer, $offset);
    }

    /**
     * Creates a ZIP archive from a directory tree.
     *
     * @param string $path Source directory.
     * @param string|null $zipfile Archive path, or a generated path when null.
     * @return bool True when the archive is written.
     */
    public static function zip(string $path, ?string $zipfile = null): bool
    {
        self::requireZip();
        $root = realpath($path);
        if ($root === false || !is_dir($root)) {
            return false;
        }

        $zipfile ??= self::ds(sprintf(
            '%s/%s-%s.zip',
            getcwd() ?: '.',
            date('YmdHis'),
            bin2hex(random_bytes(5)),
        ));

        $archive = new \ZipArchive();
        if ($archive->open($zipfile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY,
            );
            foreach ($iterator as $item) {
                if (!$item->isFile()) {
                    continue;
                }

                $relative = substr($item->getPathname(), strlen($root) + 1);
                if (!$archive->addFile($item->getPathname(), str_replace('\\', '/', $relative))) {
                    $archive->close();
                    return false;
                }
            }
        } catch (\UnexpectedValueException) {
            $archive->close();
            return false;
        }

        return $archive->close() && is_file($zipfile);
    }

    /**
     * Creates an archive from an explicit file list.
     *
     * @param string $zipfile Archive path.
     * @param array<int, string> $files Source file paths.
     * @return bool True when all files are archived.
     */
    public static function addZipList(string $zipfile, array $files): bool
    {
        self::requireZip();
        $archive = new \ZipArchive();
        if ($archive->open($zipfile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        foreach ($files as $file) {
            $real = realpath($file);
            if ($real === false || !is_file($real) || !$archive->addFile($real, basename($file))) {
                $archive->close();
                return false;
            }
        }

        return $archive->close() && is_file($zipfile);
    }

    /**
     * Extracts an archive into a directory named after the archive.
     *
     * Zip-slip paths are rejected before extraction.
     *
     * @param string $zipfile Archive path.
     * @param string $topath Destination parent directory.
     * @return bool True when extraction succeeds.
     */
    public static function unzip(string $zipfile, string $topath = './'): bool
    {
        self::requireZip();
        if (!is_file($zipfile)) {
            return false;
        }

        $archive = new \ZipArchive();
        if ($archive->open($zipfile) !== true) {
            return false;
        }

        for ($index = 0; $index < $archive->numFiles; $index++) {
            $name = str_replace('\\', '/', (string) $archive->getNameIndex($index));
            if (str_starts_with($name, '/') || preg_match('#(^|/)\.\.(/|$)#', $name) === 1) {
                $archive->close();
                return false;
            }
        }

        $destination = self::ds(rtrim($topath, '/\\') . DIRECTORY_SEPARATOR . pathinfo($zipfile, PATHINFO_FILENAME));
        if (!self::mkdir($destination) || !$archive->extractTo($destination)) {
            $archive->close();
            return false;
        }

        $archive->close();

        return is_dir($destination);
    }

    /**
     * Lists archive entries separated by newlines.
     *
     * @param string $zippath Archive path.
     * @return string Entry names.
     */
    public static function lsz(string $zippath): string
    {
        self::requireZip();
        $archive = new \ZipArchive();
        if ($archive->open($zippath) !== true) {
            return '';
        }

        $files = [];
        for ($index = 0; $index < $archive->numFiles; $index++) {
            $files[] = (string) $archive->getNameIndex($index);
        }
        $archive->close();

        return implode("\n", $files);
    }

    /**
     * Parses an INI file with native PHP typing.
     *
     * @param string $path INI path.
     * @param bool $sections Whether to preserve sections.
     * @return array<int|string, mixed> Parsed values, or an empty array.
     */
    public static function ini(string $path, bool $sections = true): array
    {
        $values = parse_ini_file($path, $sections, INI_SCANNER_TYPED);

        return $values === false ? [] : $values;
    }

    /**
     * Includes a PHP file and returns its value.
     *
     * @param string $path PHP file path.
     * @return mixed Included file result.
     */
    public static function req(string $path): mixed
    {
        return require $path;
    }

    /**
     * Builds a non-conflicting backup path for a destination file.
     *
     * @param string $path Existing destination.
     * @return string Backup path.
     */
    private static function backupPath(string $path): string
    {
        $base = $path . '_' . date('Y-m-d_H-i-s');
        $candidate = $base . '.bak';
        $counter = 1;
        while (file_exists($candidate)) {
            $candidate = sprintf('%s_%d.bak', $base, $counter++);
        }

        return $candidate;
    }

    /**
     * Ensures the ZIP extension is available.
     *
     * @return void
     * @throws \RuntimeException When ext-zip is unavailable.
     */
    private static function requireZip(): void
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('Filesystem archive operations require ext-zip.');
        }
    }
}
