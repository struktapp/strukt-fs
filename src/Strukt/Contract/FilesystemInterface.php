<?php 

namespace Strukt\Contract;

/**
* Filesystem Interface
*
* @author Moderator <pitsolu@gmail.com>
*/
interface FilesystemInterface{

	// public static function path(string $path):string;
	public static function isDir(string $dir):bool;
	public static function isFile(string $file):bool;
	public static function isPath(string $path):bool;
	public static function cat(string $file):bool|string;
	public static function touch(string $file):bool;
	public static function touchWrite(string $file, string $contents):bool;
	public static function rename(string $from, string $to):bool;
	public static function overwrite(string $file, string $contents, bool $noLockEx):mixed;
	public static function appendWrite(string $file, string $contents, bool $noLockEx):mixed;
	public static function rm(string $file):bool;
	public static function rmdir(string $dir):bool;
	public static function mkdir(string $dir, int $mode, bool $recursive):bool;
	public static function isWritable(string $file):bool;
	public static function isReadable(string $file):bool;
	public static function copyRecur(string $source, string $dest):void;
	public static function cpr(string $source, string $dest):void;
	public static function listFiles(string $path):array;
	public static function ls(string $path):array;
	public static function listFilesRecur(string $path):array;
	public static function lsr(string $path):array;
	public static function tail(string $filepath, int $lines):string;
	public static function zip(string $path, ?string $zipfile):bool;
	public static function unzip(string $zipfile, string $topath):bool;
	public static function lsz(string $zippath):string;
}
