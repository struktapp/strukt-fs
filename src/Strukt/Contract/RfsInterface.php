<?php 

namespace Strukt\Contract;

/**
* Filesystem Interface
*
* @author Moderator <pitsolu@gmail.com>
*/
interface RfsInterface{

	public function path(string $path):string;
	public function isDir(string $dir):bool;
	public function isFile(string $file):bool;
	public function isPath(string $path):bool;
	public function cat(string $file):bool|string;
	public function touch(string $file):bool;
	public function touchWrite(string $file, string $contents):bool;
	public function rename(string $from, string $to):bool;
	public function overwrite(string $file, string $contents, bool $noLockEx):mixed;
	public function appendWrite(string $file, string $contents, bool $noLockEx):mixed;
	public function rm(string $file):bool;
	public function rmdir(string $dir):bool;
	public function mkdir(string $dir, int $mode, bool $recursive):bool;
	public function isWritable(string $file):bool;
	public function isReadable(string $file):bool;
	public function copyRecur(string $source, string $dest):void;
	public function cpr(string $source, string $dest):void;
	public function listFiles(string $path):array;
	public function ls(string $path):array;
	public function listFilesRecur(string $path):array;
	public function lsr(string $path):array;
	public function tail(string $filepath, int $lines):string;
	public function zip(string $path, ?string $zipfile):bool;
	public function addZipList(string $zipfile, array $files):bool;
	public function unzip(string $zipfile, string $topath):bool;
	public function lsz(string $zippath):string;
}
