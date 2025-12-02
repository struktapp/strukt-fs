<?php

use Strukt\Fs\Fs;
use org\bovigo\vfs\vfsStream;

beforeEach(function(){

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
});

test("fs.mkdir", function(){

	expect(Fs::mkdir("vfs://payroll/public"))->toBeTrue();
	expect(Fs::isPath("vfs://payroll/public"))->toBeTrue();
});

test("fs.touchWrite", function(){

	expect(Fs::isWritable("vfs://payroll/cfg"))->toBeTrue();
	expect(Fs::touchWrite("vfs://payroll/cfg/setting.ini", ";settings"))->toBeTrue();
	expect(Fs::cat("vfs://payroll/cfg/setting.ini"))->toBe(";settings");

	$overwitten = Fs::overwrite("vfs://payroll/cfg/setting.ini", ";some settings");
	expect(gettype($overwitten) == "integer")->toBeTrue();

	expect(Fs::cat("vfs://payroll/cfg/setting.ini"))->toBe(";some settings");
});

test("fs.appendWrite", function(){

	expect(Fs::isWritable("vfs://payroll/cfg/app.ini"))->toBeTrue();

	$writeAppended = Fs::appendWrite("vfs://payroll/cfg/app.ini", ";['version'] = 1.0.0");
	expect(gettype($writeAppended) == "integer")->toBeTrue();

	expect(Fs::cat("vfs://payroll/cfg/app.ini"))->toBe(";app.ini;['version'] = 1.0.0");
});

test("fs.cat", function(){

	expect(Fs::isReadable("vfs://payroll/cfg/db.ini"))->toBeTrue();
	expect(Fs::cat("vfs://payroll/cfg/db.ini"))->toBe(";db.ini");
});

test("fs.rename", function(){

	expect(Fs::rename("vfs://payroll/cfg/module.ini", "vfs://payroll/cfg/mod.ini"))->toBeTrue();
});

test("fs.rm", function(){

	$path = "vfs://payroll/app/src/Payroll/AuthModule/Tests/UserTest.php";

	expect(Fs::isPath($path))->toBeTrue();
	expect(Fs::rm($path))->toBeTrue();
});

test("fs.rmdir", function(){

	expect(Fs::rmdir("vfs://payroll/logs"))->toBeFalse();
});

test("fs.lsr|[list_recursively]", function(){

	$ls = Fs::lsr("vfs://payroll");
	expect(count($ls))->toBe(13);
});

test("fs.cpr|[copy_recursively]", function(){

	Fs::mkdir("vfs://payroll/app/src/Payroll/AuthModule/Router");

	$src = "vfs://payroll/app/src/Payroll/AuthModule/Model";
	$dest = "vfs://payroll/app/src/Payroll/AuthModule/Router";

	Fs::cpr($src, $dest);
	$ls = Fs::lsr("vfs://payroll");

	expect(count($ls))->toBe(15);
});