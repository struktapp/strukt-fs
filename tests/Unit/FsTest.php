<?php

use Strukt\Fs\Fs;

beforeEach(function (): void {
    $this->root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'strukt-fs-' . bin2hex(random_bytes(5));
    expect(Fs::mkdir($this->root))->toBeTrue();
});

afterEach(function (): void {
    Fs::rmdir($this->root);
});

test('filesystem writes, reads, appends, and resolves rooted paths', function (): void {
    $rooted = fs($this->root);

    expect($rooted->touchWrite('config/app.ini', 'name=payroll'))->toBeTrue()
        ->and($rooted->cat('config/app.ini'))->toBe('name=payroll')
        ->and($rooted->appendWrite('config/app.ini', "\nversion=3"))->toBeInt()
        ->and($rooted->ini('config/app.ini'))->toBe(['name' => 'payroll', 'version' => 3]);
});

test('filesystem copies trees, lists hidden files, and removes trees', function (): void {
    $source = $this->root . '/source';
    $target = $this->root . '/target';
    Fs::mkdir($source . '/nested');
    Fs::touchWrite($source . '/nested/.hidden', 'hidden');
    Fs::touchWrite($source . '/nested/file.txt', "one\ntwo\nthree\n");

    Fs::copyRecur($source, $target);

    expect(Fs::isFile($target . '/nested/.hidden'))->toBeTrue()
        ->and(count(Fs::listFilesRecur($target)))->toBe(2)
        ->and(Fs::tail($target . '/nested/file.txt', 2))->toBe("two\nthree\n")
        ->and(Fs::rmdir($target))->toBeTrue()
        ->and(Fs::isPath($target))->toBeFalse();
});

test('filesystem archives and extracts a directory safely', function (): void {
    $source = $this->root . '/archive-source';
    $zip    = $this->root . '/archive.zip';
    Fs::mkdir($source);
    Fs::touchWrite($source . '/file.txt', 'contents');

    expect(Fs::zip($source, $zip))->toBeTrue()
        ->and(Fs::lsz($zip))->toContain('file.txt')
        ->and(Fs::unzip($zip, $this->root))->toBeTrue()
        ->and(Fs::cat($this->root . '/archive/file.txt'))->toBe('contents');
});

test('filesystem helper registry includes every filesystem helper', function (): void {
    expect(helper('filesystem'))->toContain(
        'fs',
        'tail',
        'ds',
        'path_exists',
        'phar',
        'local',
    );
});
