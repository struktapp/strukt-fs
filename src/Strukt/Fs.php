<?php 

namespace Strukt;

/**
* File System Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Fs implements Contract\FilesystemInterface{

	/**
	* Check if dir exists
	*
	* @param string $dir
	*
	* @return boolean
	*/
	#[\Override]
	public static function isDir(string $dir):bool{

		return is_dir($dir);
	}

	/**
	* Check if file exists
	*
	* @param string $file
	*
	* @return boolean
	*/
	#[\Override]
	public static function isFile(string $file):bool{

		clearstatcache();
		
		return is_file($file);
	}

	/**
	* Check if path exists
	*
	* @param string $path
	*
	* @return boolean
	*/
	#[\Override]
	public static function isPath(string $path):bool{

		clearstatcache();

    	return file_exists($path);
  	}

  	/**
	* Dump file contents
	*
	* @param string $file
	*
	* @return boolean|string
	*/
	#[\Override]
	public static function cat(string $file):bool|string{

		if(self::isFile($file))
			return @file_get_contents($file);

		return false;
	}

	/**
	* Create file
	*
	* @param string $file
	*
	* @return boolean
	*/
	#[\Override]
	public static function touch(string $file):bool{

		if(self::isFile($file))
			return false;
	
		return touch($file);
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
	public static function touchWrite(string $file, string $contents):bool{

		if(self::touch($file))
			if(self::overwrite($file, $contents))
				return true;

		return false;
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
	public static function rename(string $from, string $to):bool{

		if($from == $to)
			return false;

		$toFileExists = false;
		if(self::isFile($to))
			$toFileExists = true;

		$fromFileExists = false;
		if(self::isFile($from))
			$fromFileExists = true;
		
		if($toFileExists && $fromFileExists)
			if(rename($to, sprintf("%s_%s_%s.bak", $to, date("Y-m-d_H-i-s"), rand())))
				return rename($from, $to);
		
		if($fromFileExists && !$toFileExists)
			return rename($from, $to);

		return false;
	}

	/**
	* Overwrite contents of file
	*
	* @param string $file 
	* @param string $contents
	* @param bool $noLockEx
	*
	* @return mixed
	*/
	#[\Override]
	public static function overwrite(string $file, string $contents, bool $noLockEx = true):mixed{

		if(!self::isFile($file))
			return false;

		if($noLockEx)
			return file_put_contents($file, $contents);

		return file_put_contents($file, $contents, LOCK_EX);
	}

	/**
	* Append contents to file
	*
	* @param string $file 
	* @param string $contents
	* @param bool $noLockEx
	*
	* @return mixed
	*/
	#[\Override]
	public static function appendWrite(string $file, string $contents, bool $noLockEx = true):mixed{

		if(!self::isFile($file))
			return false;

		if($noLockEx)
			return file_put_contents($file, $contents, FILE_APPEND);

		return file_put_contents($file, $contents, FILE_APPEND | LOCK_EX);
	}

	/**
	* Delete file
	*
	* @param string $file 
	*
	* @return boolean
	*/
	#[\Override]
	public static function rm(string $file):bool{

		if(preg_match("/\*/", $file)){

			$files = glob($file);
			if(empty($files))
				return false;
		}
		else $files[] = $file;

		foreach($files as $file)
			if(self::isFile($file))
				unlink($file);

		return true;
	}

	/**
	* Recursively remove directory and sub resources
	*
	* @param string $dir
	*
	* @return boolean 
	*/
	#[\Override]
	public static function rmdir(string $dir):bool{ 

		foreach(glob($dir . '/*') as $file){

	    	if(is_dir($file)) 
	      		self::rmdir($file); 

	    	if(is_file($file)) 
	      		unlink($file);
	    }

	  	return @rmdir($dir);
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
	public static function mkdir(string $dir, int $mode = 0755, bool $recursive = true):bool{

		return @mkdir($dir, $mode, $recursive);
	}

	/**
	* Check if file or directory is writeable
	*
	* @param string $file 
	*
	* @return boolean
	*/
	#[\Override]
	public static function isWritable($file):bool{

		if(self::isPath($file))
			return is_writable($file);

		return false;
	}

	/**
	* Check if file or directory is readable
	*
	* @param string $file 
	*
	* @return boolean
	*/
	#[\Override]
	public static function isReadable($file):bool{

		if(self::isFile($file))
			return is_readable($file);

		return false;
	}

	/**
	 * Copy a file or recursively copy a directories contents
	 *
	 * @param string $source The path to the source file/directory
	 * @param string $dest The path to the destination directory
	 */
	#[\Override]
	public static function copyRecur(string $source, string $dest):void{

	    if (is_dir($source)){

	        $iterator = new \RecursiveIteratorIterator(

	            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
	            \RecursiveIteratorIterator::SELF_FIRST
	        );

	        foreach ($iterator as $file)
	            if ($file->isDir())
	                mkdir($dest.DIRECTORY_SEPARATOR.$iterator->getSubPathName());
	            else
	                copy($file, $dest.DIRECTORY_SEPARATOR.$iterator->getSubPathName());
	    }
	    else
	        copy($source, $dest);
	}

	/**
	 * Alias for Strukt\Fs::copyRecur
	 *
	 * @param string $source The path to the source file/directory
	 * @param string $dest The path to the destination directory
	 */
	#[\Override]
	public static function cpr(string $source, string $dest):void{

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
	public static function listFiles(string $path="."):array{

		return array_values(array_diff(scandir($path), array('..', '.')));
	}

	/**
	* Alias of Strukt/Fs::listFiles
	*
	* @param string $path The path to directory
	* 
	* @return array
	*/
	#[\Override]
	public static function ls(string $path="."):array{

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
	public static function listFilesRecur(string $path="."):array{

	    $rItrItr = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

		$files = []; 

		foreach ($rItrItr as $file) {

		    if ($file->isDir())
		        continue;

		    $files[] = $file->getPathname(); 
		}

		return $files;
	}

	/**
	* Alias of Strukt/Fs::listFilesRecur
	*
	* @param string $path The path to directory
	* 
	* @return array
	*/
	#[\Override]
	public static function lsr(string $path="."):array{

		return self::listFilesRecur($path);
	}

	/**
	* Which OS
	*
	* @return bool
	*/
	public static function isWindows():bool{

		return strtoupper(substr(PHP_OS, 0, 3)) == "WIN";
	}

	/**
	* Change directory separator according to operating system
	*
	* @param string $path 
	* 
	* @return string
	*/
	public static function dirSep(string $path):string{

		return preg_replace("/(\/|\\\)/", DIRECTORY_SEPARATOR, $path);
	}

	/**
	* Alias for Strukt\Fs::dirsep
	* 
	* @param string $path 
	*
	* @return string
	*/
	public static function ds(string $path):string{

		return self::dirSep($path);
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
	public static function tail(string $filepath, int $lines = 20):string{

		$file = new \SplFileObject($filepath);
		$file->seek(PHP_INT_MAX);
		$total_lines = $file->key();

		$nlines = $total_lines - $lines;
		if($lines >= $total_lines)
			$nlines = 0;

		$file->seek($nlines);

		$ls = [];
		while (!$file->eof()) {
		    $ls[] = $file->current();
		    $file->next();
		}

		return implode("", $ls);
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
	public static function zip(string $path, ?string $zipfile = null):bool{

		if(is_null($zipfile)){

			$date = (new \DateTime())->format("YmdHis");
			$rand = substr(sha1(rand()), 0, 10);
			$zipfile = sprintf("%s-%s.zip", $date, $rand);
		}

		/**
		* @source https://bit.ly/3I6Cdu1
		*/
		// Get real path for our folder
		$rootPath = realpath($path);

		// Initialize archive object
		$zip = new \ZipArchive();
		$zip->open($zipfile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$files = new \RecursiveIteratorIterator(
		    new \RecursiveDirectoryIterator($rootPath),
		    \RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file){

		    // Skip directories (they would be added automatically)
		    if (!$file->isDir()){

		        // Get real and relative path for current file
		        $filePath = $file->getRealPath();
		        $relativePath = substr($filePath, strlen($rootPath) + 1);

		        // Add current file to archive
		        $zip->addFile($filePath, $relativePath);
		    }
		}

		// Zip archive will be created only after closing object
		$zip->close();

		return Fs::isFile($zipfile);
	}

	/**
	* Unzip dir
	* 
	* @param string zipfile
	* @param string $topath
	*
	* @return boolean
	*/
	#[\Override]
	public static function unzip(string $zipfile, string $topath = "./"):bool{

		$unzipdir = trim($zipfile, ".zip");

		$finalpath = sprintf(Fs::ds('%s%s'), $topath, $unzipdir);

		$zip = new \ZipArchive;
		$zip->open(realpath($zipfile));
		$zip->extractTo($finalpath);
		$zip->close(); 

		return Fs::isDir($finalpath);
	}

	/**
	* List what is in a *.zip file
	* 
	* @param string $zippath
	* 
	* @return string
	*/
	#[\Override]
	public static function lsz(string $zippath):string{

		$zip = new \ZipArchive;
		if ($zip->open($zippath) == TRUE)
			for ($i = 0; $i < $zip->numFiles; $i++) 
				$files[] = $zip->getNameIndex($i);

		return implode("\n", $files);
	}

	/**
	* Parse initialization file
	* 
	* @param string $path
	* @param bool $sections
	*
	* @return array
	*/
	public static function ini(string $path, bool $sections = true):array{

		return parse_ini_file($path, $sections);
	}

	/**
	* Require a file
	* 
	* @param string $path
	*
	* @return mixed
	*/
	public static function req(string $path):mixed{

		return require($path);
	}
}