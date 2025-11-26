Strukt Fs
=========

[![Build Status](https://travis-ci.org/pitsolu/strukt-fs.svg?branch=master)](https://packagist.org/packages/strukt/fs)
[![Latest Stable Version](https://poser.pugx.org/strukt/fs/v/stable)](https://packagist.org/packages/strukt/fs)
[![Total Downloads](https://poser.pugx.org/strukt/fs/downloads)](https://packagist.org/packages/strukt/fs)
[![Latest Unstable Version](https://poser.pugx.org/strukt/fs/v/unstable)](https://packagist.org/packages/strukt/fs)
[![License](https://poser.pugx.org/strukt/fs/license)](https://packagist.org/packages/strukt/fs)

Basic filesystem functionality. 

## Usage

### API

```php
Strukt\Fs::isDir(file) //Directory Exists
Strukt\Fs::isFile(file) //File Exists
Strukt\Fs::isPath(path) //Path Esists
Strukt\Fs::cat(file) //Dump File Contents
Strukt\Fs::touch(file) //Create File
Strukt\Fs::touchWrite(file, contents) //Create Write To File
Strukt\Fs::rename(from, to) //Rename File
Strukt\Fs::overwrite(file, contents) //Overwrite File Contents
Strukt\Fs::appendWrite(file, contents) //Append Contents To File
Strukt\Fs::rm(file) //Delete File
Strukt\Fs::rmdir(dir) //Recursively Delete
Strukt\Fs::mkdir(dir) //Recursively Create
Strukt\Fs::isWritable(file) //Check If File Is Writeable
Strukt\Fs::isReadable(file) //Check If File is Readable
Strukt\Fs::copyRecur(source,destination) //Copy recursively
Strukt\Fs::cpr(source,destination) //Alias for copyRecur
Strukt\Fs::listFilesRecur(path) //List directory files recursively
Strukt\Fs::lsr(path) //Alias for listFilesRecur
Strukt\Fs::tail(filepath, lines = 20) //read last line of file: default 20 lines
Strukt\Fs::isWindows()//Is OS Windows
Strukt\Fs::dirSep(path)//OS appropriate directory separator on path
Strukt\Fs::ds(path)//Alisas for dirSep
```

### Helpers

```php
fs(); #Strukt\Fs
fs("."); #Strukt\Local\Fs
tail("README.md");
ds("src/Strukt/Contract"); # Change directory separator depending on OS
path_exists("src/Strukt/Contract");
phar()->active(); # Detect is code is inside a Phar archive
phar("src/Strukt/Contract")->adapt(); # Adapt to Phar path if in Phar archive, otherwise return qualified path
```

