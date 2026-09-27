Strukt Fs
=========

[![Latest Stable Version](https://poser.pugx.org/strukt/fs/v)](https://packagist.org/packages/strukt/fs)
[![Total Downloads](https://poser.pugx.org/strukt/fs/downloads)](https://packagist.org/packages/strukt/fs)
[![License](https://poser.pugx.org/strukt/fs/license)](https://packagist.org/packages/strukt/fs)

Fast local filesystem, rooted path, and ZIP archive helpers for Strukt. The
package provides a static Fs API, a rooted Rfs facade, and procedural helpers
for the operations used most often.

Requirements
------------

- PHP 8.2 or newer
- PHP ext-zip for ZIP operations
- strukt/base

Installation
------------

```sh
composer require strukt/fs
```

Composer loads src/helpers.php automatically. The package installs the global
filesystem helpers after the normal Composer autoloader has been required.

Quick start
-----------

```php
require __DIR__ . '/vendor/autoload.php';

$files = fs(__DIR__);
$files->touchWrite('storage/app.log', "ready\n");
echo $files->cat('storage/app.log');

$entries = $files->lsr('storage');
$recent = $files->tail('storage/app.log', 20);
```

Global helpers
--------------

### fs()

```php
fs(?string $dir = null): Strukt\Fs\Fs|Strukt\Fs\Rfs
```

Returns a filesystem facade. With no root it returns an Fs instance and paths
are passed through unchanged. With a root it returns Rfs and all paths supplied
to its methods are resolved below that root.

```php
$absolute = fs();
$project = fs(__DIR__);

$project->touchWrite('var/cache/data.txt', 'cached');
```

### tail()

```php
tail(string $filepath, int $lines = 20): string
```

Returns the final lines of a file. It reads backwards in chunks instead of
loading the entire file prefix. Missing files and non-positive line counts
return an empty string.

### ds()

```php
ds(string $path): string
```

Replaces both slash styles with the current operating system's directory
separator.

### path_exists()

```php
path_exists(string $path): bool
```

Returns true when the path is an existing file or directory.

### phar()

```php
phar(?string $path = null): object
```

Returns a small adapter with the following methods:

```php
$adapter->active(): bool
$adapter->adapt(): string
```

active() reports whether execution is inside a Phar archive. adapt() resolves
the stored path against the active archive or current working directory.

### local()

```php
local(string $path): ?string
```

Converts an existing path to a file URI such as file:///tmp/report.txt. It
returns null when the path does not exist and preserves an existing file URI.

Helper inventory
---------------

The filesystem package registers these global helper names:

```text
fs, tail, ds, path_exists, phar, local
```

Static Fs API
-------------

Strukt\Fs\Fs exposes the complete local filesystem API through static methods.

### Existence and file access

```php
Fs::isDir(string $dir): bool
Fs::isFile(string $file): bool
Fs::isPath(string $path): bool
Fs::cat(string $file): string|false
Fs::touch(string $file): bool
Fs::touchWrite(string $file, string $contents): bool
Fs::rename(string $from, string $to): bool
Fs::overwrite(string $file, string $contents, bool $noLockEx = true): int|false
Fs::appendWrite(string $file, string $contents, bool $noLockEx = true): int|false
```

isDir(), isFile(), and isPath() perform direct filesystem checks. cat() reads
an existing file. touch() creates only a new file. touchWrite() creates parent
directories, creates a new file, and writes all contents; it returns false if
the file already exists. overwrite() and appendWrite() require an existing
file and return the number of bytes written or false. Set noLockEx to false to
use LOCK_EX. rename() backs up an existing destination before replacing it.

### Removal and permissions

```php
Fs::rm(string $file): bool
Fs::rmdir(string $dir): bool
Fs::mkdir(string $dir, int $mode = 0755, bool $recursive = true): bool
Fs::isWritable(string $file): bool
Fs::isReadable(string $file): bool
```

rm() removes one file, link, or glob match. rmdir() removes a complete
directory tree, including hidden entries. mkdir() is idempotent and returns
true when the directory exists after the call. isWritable() and isReadable()
delegate to the native permission checks.

### Copying and listing

```php
Fs::copyRecur(string $source, string $dest): void
Fs::cpr(string $source, string $dest): void
Fs::listFiles(string $path = '.'): array
Fs::ls(string $path = '.'): array
Fs::listFilesRecur(string $path = '.'): array
Fs::lsr(string $path = '.'): array
```

copyRecur() copies a file or directory tree and throws RuntimeException when
the source or destination operation is invalid. cpr() is its alias. listFiles()
and ls() return immediate entry names without . or ... listFilesRecur() and
lsr() return paths for every regular file below the directory. Traversal
includes hidden entries and uses iterators.

### Path and platform helpers

```php
Fs::isWindows(): bool
Fs::dirSep(string $path): string
Fs::ds(string $path): string
```

isWindows() identifies the Windows platform. dirSep() and ds() normalize slash
styles; ds() is the short alias.

### Tailing and archives

```php
Fs::tail(string $filepath, int $lines = 20): string
Fs::zip(string $path, ?string $zipfile = null): bool
Fs::addZipList(string $zipfile, array $files): bool
Fs::unzip(string $zipfile, string $topath = './'): bool
Fs::lsz(string $zippath): string
```

tail() returns the final lines of a file. zip() archives every file below a
directory; when zipfile is null it creates a timestamped archive in the current
working directory. addZipList() creates an archive from explicit source files
and stores each entry under its basename. unzip() validates archive paths
against absolute and parent-directory traversal, then extracts into
topath/archive-name. lsz() returns archive entry names separated by newlines.
All archive methods require ext-zip.

### PHP file helpers

```php
Fs::ini(string $path, bool $sections = true): array
Fs::req(string $path): mixed
```

ini() parses an INI file using PHP's typed scanner and returns an empty array
when parsing fails. req() includes a PHP file and returns the included value.

Rooted Rfs API
-------------

Strukt\Fs\Rfs normalizes a root once and delegates filesystem work to Fs.
Relative paths are joined to that root:

```php
new Rfs(string $path)
$rfs->path(string $path): string
```

Rfs exposes the same rooted methods as Fs:

```php
$rfs->isDir(string $dir): bool
$rfs->isFile(string $file): bool
$rfs->isPath(string $path): bool
$rfs->cat(string $file): string|false
$rfs->touch(string $file): bool
$rfs->touchWrite(string $file, string $contents): bool
$rfs->rename(string $from, string $to): bool
$rfs->overwrite(string $file, string $contents, bool $noLockEx = true): int|false
$rfs->appendWrite(string $file, string $contents, bool $noLockEx = true): int|false
$rfs->rm(string $file): bool
$rfs->rmdir(string $dir): bool
$rfs->mkdir(string $dir, int $mode = 0755, bool $recursive = true): bool
$rfs->isWritable(string $file): bool
$rfs->isReadable(string $file): bool
$rfs->copyRecur(string $source, string $dest): void
$rfs->cpr(string $source, string $dest): void
$rfs->listFiles(string $path = '.'): array
$rfs->ls(string $path = '.'): array
$rfs->listFilesRecur(string $path = '.'): array
$rfs->lsr(string $path = '.'): array
$rfs->tail(string $filepath, int $lines = 20): string
$rfs->zip(string $path, ?string $zipfile = null): bool
$rfs->addZipList(string $zipfile, array $files): bool
$rfs->unzip(string $zipfile, string $topath = './'): bool
$rfs->lsz(string $zippath): string
$rfs->ini(string $path, bool $sections = true): array
$rfs->req(string $path): mixed
```

All arguments except the constructor root are interpreted relative to the
root. listFiles() returns names relative to the listed directory; recursive
listing returns the resolved paths produced by Fs. Rfs is a path convenience
facade, not a security sandbox, so untrusted path input should be validated by
the application.

Phar API
--------

Strukt\Phar provides the typed adapter behind phar():

```php
Phar::adapt(string $path = ''): string
Phar::isPhar(): bool
```

adapt() uses the active archive path when available and otherwise the current
working directory. isPhar() checks whether the current code is executing from
a Phar archive.

Performance and safety notes
----------------------------

- Existence checks avoid clearing PHP's global stat cache on every call.
- Recursive operations use iterators and include dot-prefixed entries.
- tail() reads only the required suffix in chunks.
- mkdir() is idempotent, and touchWrite() creates missing parents.
- ZIP extraction rejects absolute and parent-traversal entry names.
- ZIP methods fail cleanly when an archive cannot be opened or written.

Testing
-------

```sh
composer test
```
