<?php 

namespace Strukt\Contract;

/**
* Filesystem Interface
*
* @author Moderator <pitsolu@gmail.com>
*/
interface FsStaticInterface{

	// public static function path(string $path):string;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function isDir(string $dir):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function isFile(string $file):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function isPath(string $path):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function cat(string $file):bool|string;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function touch(string $file):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function touchWrite(string $file, string $contents):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function rename(string $from, string $to):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function overwrite(string $file, string $contents, bool $noLockEx):mixed;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function appendWrite(string $file, string $contents, bool $noLockEx):mixed;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function rm(string $file):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function rmdir(string $dir):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function mkdir(string $dir, int $mode, bool $recursive):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function isWritable(string $file):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function isReadable(string $file):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function copyRecur(string $source, string $dest):void;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function cpr(string $source, string $dest):void;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function listFiles(string $path):array;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function ls(string $path):array;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function listFilesRecur(string $path):array;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function lsr(string $path):array;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function tail(string $filepath, int $lines):string;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function zip(string $path, ?string $zipfile):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function unzip(string $zipfile, string $topath):bool;
	
	/**
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function lsz(string $zippath):string;
}
