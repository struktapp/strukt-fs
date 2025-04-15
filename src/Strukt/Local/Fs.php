<?php 

namespace Strukt\Local;

use Strukt\Fs as Filesystem;

/**
* File System Class (Local)
*
* @author Moderator <pitsolu@gmail.com>
*/
class Fs implements \Strukt\Contract\LocalFilesystemInterface{

	private $path;

	/**
	 * @param string $path
	 */
	public function __construct(string $path){

		$this->path = Filesystem::ds($path);
	}


	/**
	 * @param string $path
	 * 
	 * @return string
	 */
	#[\Override]
	public function path(string $path):string{

		return Filesystem::ds(sprintf("%s/%s", rtrim($this->path, "/"), trim($path, "/")));
	}

	/**
	* Check if dir exists
	*
	* @param string $dir
	*
	* @return boolean
	*/
	#[\Override]
	public function isDir(string $dir):bool{

		return Filesystem::isDir($this->path($dir));
	}

	/**
	* Check if file exists
	*
	* @param string $file
	*
	* @return boolean
	*/
	#[\Override]
	public function isFile(string $file):bool{
		
		return Filesystem::isFile($this->path($file));
	}

	/**
	* Check if path exists
	*
	* @param string $path
	*
	* @return boolean
	*/
	#[\Override]
	public function isPath(string $path):bool{

    	return Filesystem::isPath($this->path($path));
  	}

  	/**
	* Dump file contents
	*
	* @param string $file
	*
	* @return string
	*/
	#[\Override]
	public function cat(string $file):string{

		return Filesystem::cat($this->path($file));
	}

	/**
	* Create file
	*
	* @param string $file
	*
	* @return boolean
	*/
	#[\Override]
	public function touch(string $file):bool{

		return Filesystem::touch($this->path($file));
	}

	/**
	* Create and write to file
	*
	* @param string $file 
	* @param string $contents
	*
	* @return boolean
	*/
	#[\Override]
	public function touchWrite(string $file, string $contents):bool{

		return Filesystem::touchWrite($this->path($file), $contents);
	}

	/**
	* Rename file
	*
	* @param string $from 
	* @param string $to
	*
	* @return boolean
	*/
	#[\Override]
	public function rename(string $from, string $to):bool{

		return Filesystem::rename($this->path($from), $this->path($to));
	}

	/**
	* Overwrite contents of file
	*
	* @param string $file 
	* @param string $contents
	* @param boolean $noLockEx
	*
	* @return boolean
	*/
	#[\Override]
	public function overwrite(string $file, string $contents, bool $noLockEx = true):mixed{

		return Filesystem::overwrite($this->path($file), $contents, $noLockEx);
	}

	/**
	* Append contents to file
	*
	* @param string $file 
	* @param string $contents
	* @param boolean $noLockEx
	*
	* @return boolean
	*/
	#[\Override]
	public function appendWrite(string $file, string $contents, bool $noLockEx = true):mixed{

		return Filesystem::appendWrite($this->path($file), $contents, $noLockEx);
	}

	/**
	* Delete file
	*
	* @param string $file 
	*
	* @return boolean
	*/
	#[\Override]
	public function rm(string $file):bool{

		return Filesystem::rm($this->path($file));
	}

	/**
	* Recursively remove directory and sub resources
	*
	* @param string $dir
	*
	* @return boolean 
	*/
	#[\Override]
	public function rmdir(string $dir):bool{ 

		return Filesystem::rmdir($this->path($dir));
	}

	/**
	* Recursively make directory
	*
	* @param string $dir
	* @param int $mode
	* @param bool $recursive 
	*
	* @return boolean
	*/
	#[\Override]
	public function mkdir(string $dir, int $mode = 0755, bool $recursive = true):bool{

		return Filesystem::mkdir($this->path($dir), $mode, $recursive);
	}

	/**
	* Check if file or directory is writeable
	*
	* @param string $file 
	*
	* @return boolean
	*/
	#[\Override]
	public function isWritable(string $file):bool{

		return Filesystem::isWritable($this->path($file));
	}

	/**
	* Check if file or directory is readable
	*
	* @param string $file 
	*
	* @return boolean
	*/
	#[\Override]
	public function isReadable(string $file):bool{

		return Filesystem::isReadable($this->path($file));
	}

	/**
	 * Copy a file or recursively copy a directories contents
	 *
	 * @param string $source The path to the source file/directory
	 * @param string $dest The path to the destination directory
	 */
	#[\Override]
	public function copyRecur(string $source, string $dest):void{

	    Filesystem::copyRecur($this->path($source), $this->path($dest));
	}

	/**
	 * Alias for Strukt\Fs::copyRecur
	 *
	 * @param string $source The path to the source file/directory
	 * @param string $dest The path to the destination directory
	 */
	#[\Override]
	public function cpr(string $source, string $dest):void{

		self::copyRecur($source, $dest);
	}

	/**
	* List files
	*
	* @param string $path The path to directory
	* 
	* @return array
	*/
	#[\Override]
	public function listFiles(string $path="."):array{

	    return Filesystem::listFiles($this->path($path));
	}

	/**
	* Alias of Strukt/Fs::listFiles
	*
	* @param string $path The path to directory
	* 
	* @return array
	*/
	#[\Override]
	public function ls(string $path="."):array{

		return self::listFiles($path);
	}

	/**
	* List files recursively
	*
	* @param string $path The path to directory
	* 
	* @return array
	*/
	#[\Override]
	public function listFilesRecur(string $path="."):array{

	    return Filesystem::listFilesRecur($this->path($path));
	}

	/**
	* Alias Strukt/Fs::listFilesRecur
	*
	* @param string $path The path to directory
	* 
	* @return array
	*/
	#[\Override]
	public function lsr(string $path="."):array{

		return self::listFilesRecur($path);
	}

	/**
	* Read last lines of file
	* 
	* @param string $filepath 
	* @param int $lines
	* 
	* @return string
	*/
	#[\Override]
	public function tail(string $filepath, int $lines = 20):string{

		return Filesystem::tail($this->path($filepath), $lines);
	}

	/**
	* Zip a directory
	* 
	* @param string $path
	* @param string $zipfile
	*
	* @return boolean
	*/
	#[\Override]
	public function zip(string $path, ?string $zipfile = null):bool{

		return Filesystem::zip($this->path($path), $zipfile);
	}

	/**
	* Zip files to zip via list
	* 
	* @param string zipfile
	* @param array $files
	*
	* @return boolean
	*/
	#[\Override]
	public function addZipList(string $zipfile, array $files):bool{

		return Filesystem::addZipList($this->path($zipfile), $files);
	}

	/**
	* Unzip dir
	* 
	* @param string $zipfile
	* @param string $topath
	*
	* @return boolean
	*/
	#[\Override]
	public function unzip(string $zipfile, string $topath = "./"):bool{

		return Filesystem::unzip($this->path($zipfile), $this->path($topath));
	}

	/**
	* List what is in a *.zip file
	* 
	* @param string $zippath
	* 
	* @return string
	*/
	#[\Override]
	public function lsz(string $zippath):string{

		return Filesystem::lsz($this->path($zippath));
	}

	/**
	* Parse initialization file
	* 
	* @param string $path
	* @param bool $sections
	* 
	* @return string
	*/
	public function ini(string $path, bool $sections = true):array{

		return Filesystem::ini($this->path($path), $sections);
	}

	/**
	* Require a file
	* 
	* @param string $path
	*
	* @return string
	*/
	public function req(string $path):mixed{

		return Filesystem::req($this->path($path));
	}
}