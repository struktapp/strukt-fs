<?php 

namespace Strukt\Contract;

/**
* Filesystem Interface
*
* @author Moderator <pitsolu@gmail.com>
*/
interface FsInterface{

	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function path(string $path):string;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function isDir(string $dir):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function isFile(string $file):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function isPath(string $path):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function cat(string $file):bool|string;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function touch(string $file):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function touchWrite(string $file, string $contents):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function rename(string $from, string $to):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function overwrite(string $file, string $contents, bool $noLockEx):mixed;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function appendWrite(string $file, string $contents, bool $noLockEx):mixed;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function rm(string $file):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function rmdir(string $dir):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function mkdir(string $dir, int $mode, bool $recursive):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function isWritable(string $file):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function isReadable(string $file):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function copyRecur(string $source, string $dest):void;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function cpr(string $source, string $dest):void;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function listFiles(string $path):array;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function ls(string $path):array;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function listFilesRecur(string $path):array;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function lsr(string $path):array;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function tail(string $filepath, int $lines):string;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function zip(string $path, ?string $zipfile):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function unzip(string $zipfile, string $topath):bool;
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function lsz(string $zippath):string;
}
