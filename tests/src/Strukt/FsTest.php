<?php

use Strukt\Fs;
use org\bovigo\vfs\vfsStream;

class FsTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		$structure = array(

			"app"=>array(
				"src"=>array(
					"Payroll"=>array(
						"AuthModule"=>array(
							"Controller"=>array(
								"User.php"=>"<?php\n//todo:User.php",
								"Role.php"=>"<?php\n//todo:Role.php"
							),
							"Model"=>array(
								"User.php"=>"<?php\n//todo:User.php",
								"Role.php"=>"<?php\n//todo:Role.php"
							),
							"Tests"=>array(
								"UserTest.php"=>"<?php\n//todo:UserTest.php",
								"RoleTest.php"=>"<?php\n//todo:RoleTest.php"
							)
						)
					)
				)
			),
			"bin"=>array(
				"config-do"=>"#todo:strukt-doctrine-config",
				"config-basic"=>"#todo:strukt-basic-config"
			),
			"cfg"=>array(
				"app.ini"=>";app.ini",
				"db.ini"=>";db.ini",
				"module.ini"=>";module.ini"
			),
			"logs"=>array(
				".keep"=>""
			),
			"bootstrap.php"=>"<?php\n//todo:bootstrap.php"
		);

		vfsStream::setup('payroll', null, $structure);
	}

	public function testMkdir(){

		$success = Fs::mkdir("vfs://payroll/public");

		$this->assertTrue($success);

		$isPath = Fs::isPath("vfs://payroll/public");

		$this->assertTrue($isPath);
	}

	public function testTouchWrite(){

		$isWritable = Fs::isWritable("vfs://payroll/cfg");

		$this->assertTrue($isWritable);

		$isTouched = Fs::touchWrite("vfs://payroll/cfg/setting.ini", ";settings");

		$this->assertTrue($isTouched);

		$settingsContent = Fs::cat("vfs://payroll/cfg/setting.ini");

		$this->assertEquals($settingsContent, ";settings");

		$overwitten = Fs::overwrite("vfs://payroll/cfg/setting.ini", ";some settings");

		$this->assertTrue(gettype($overwitten) == "integer");

		$settingsContent = Fs::cat("vfs://payroll/cfg/setting.ini");

		$this->assertEquals($settingsContent, ";some settings");
	}

	public function testAppendWrite(){

		$isWritable = Fs::isWritable("vfs://payroll/cfg/app.ini");

		$this->assertTrue($isWritable);		

		$writeAppended = Fs::appendWrite("vfs://payroll/cfg/app.ini", ";['version'] = 1.0.0");

		$this->assertTrue(gettype($writeAppended) == "integer");

		$settingsContent = Fs::cat("vfs://payroll/cfg/app.ini");

		$this->assertEquals($settingsContent, ";app.ini;['version'] = 1.0.0");
	}

	public function testDumpContents(){

		$isReadable = Fs::isReadable("vfs://payroll/cfg/db.ini");

		$this->assertTrue($isReadable);

		$dbIniContent = Fs::cat("vfs://payroll/cfg/db.ini");

		$this->assertEquals($dbIniContent, ";db.ini");
	}

	public function testRename(){

		$isRenamed = Fs::rename("vfs://payroll/cfg/module.ini", "vfs://payroll/cfg/mod.ini");

		$this->assertTrue($isRenamed);
	}

	public function testRemoveFile(){

		$path = "vfs://payroll/app/src/Payroll/AuthModule/Tests/UserTest.php";

		$isPath = Fs::isPath($path);

		$this->assertTrue($isPath);

		$isRemoved = Fs::rm($path);

		$this->assertTrue($isRemoved);
	}

	public function testRemoveDirectory(){

		$isDirRmd = Fs::rmdir("vfs://payroll/logs");

		$this->assertFalse($isDirRmd);
	}

	public function testListRecursively(){

		$ls = Fs::lsr("vfs://payroll");

		$this->assertEquals(count($ls), 13);
	}

	public function testCopyRecursively(){

		Fs::mkdir("vfs://payroll/app/src/Payroll/AuthModule/Router");

		$src = "vfs://payroll/app/src/Payroll/AuthModule/Model";
		$dest = "vfs://payroll/app/src/Payroll/AuthModule/Router";

		Fs::cpr($src, $dest);

		$ls = Fs::lsr("vfs://payroll");

		$this->assertEquals(count($ls), 15);
	}
 }