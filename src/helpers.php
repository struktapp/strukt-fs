<?php

use Strukt\Fs;
use Strukt\Local\Fs as LocalFs;

helper("filesystem");

if(helper_add("fs")){

	/**
	 * @param string $dir 
	 * 
	 * @return Strukt\Local\Fs|Strukt\Fs
	 */
	function fs(?string $dir = null):LocalFs|Fs{

		if(!is_null($dir))
			return new Strukt\Local\Fs(Strukt\Fs::ds($dir));

		return new Strukt\Fs;
	}
}

if(helper_add("tail")){

	/**
	 * @param string $filepath
	 * @param integer $lines
	 * 
	 * @return string
	 */
	function tail(string $filepath, int $lines = 20):string{

		return Strukt\Fs::tail($filepath, $lines);
	}
}

if(helper_add("ds")){

	/**
	 * @param string $path
	 * 
	 * @return string
	 */
	function ds(string $path):string{

		return Strukt\Fs::ds(sprintf("%s/", trim($path, "/")));
	}
}

if(helper_add("path_exists")){

	/**
	 * @param string $path
	 * 
	 * @return bool
	 */
	function path_exists(string $path):bool{

		return fs()->isDir($path) || fs()->isPath($path);
	}
}

if(helper_add("phar")){

	/**
	 * @param string $path
	 * 
	 * @return mixed
	 */
	function phar(?string $path = null):mixed{

		return new class($path){

			private string $path;

			/**
			 * @param string $path
			 */
			public function __construct(?string $path){

				if(is_null($path))
					$path = "";

				$this->path = $path;
			}

			/**
			 * @return bool
			 */
			public function active():bool{

				return Strukt\Phar::isPhar();
			}

			/**
			 * @return string
			 */
			public function adapt():string{

				return Strukt\Phar::adapt($this->path);
			}
		};
	}
}

if(helper_add("local")){

	/**
	 * Localize string path to url
	 * 
	 * @param string $path
	 * 
	 * @return string|null
	 */
	function local(string $path):string|null{

		if(path_exists($path))
			return sprintf("file://%s", realpath($path));

		return null;
	}
}