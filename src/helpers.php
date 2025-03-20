<?php

helper("filesystem");

if(helper_add("fs")){

	function fs(?string $dir = null){

		if(!is_null($dir))
			return new Strukt\Local\Fs(Strukt\Fs::ds($dir));

		return new Strukt\Fs;
	}
}

if(helper_add("tail")){

	function tail(string $filepath, int $lines = 20){

		return Strukt\Fs::tail($filepath, $lines);
	}
}

if(helper_add("ds")){

	function ds(string $path){

		return Strukt\Fs::ds(sprintf("%s/", trim($path, "/")));
	}
}

if(helper_add("path_exists")){

	function path_exists(string $path){

		return fs()->isDir($path) || fs()->isPath($path);
	}
}

if(helper_add("phar")){

	function phar(?string $path = null):mixed{

		return new class($path){

			private string $path;

			public function __construct(?string $path){

				if(is_null($path))
					$path = "";

				$this->path = $path;
			}

			public function active():bool{

				return Strukt\Phar::isPhar();
			}

			public function adapt():string{

				return Strukt\Phar::adapt($this->path);
			}
		};
	}
}