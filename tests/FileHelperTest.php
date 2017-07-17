<?php

#
# WordPress Extension Manager
# Copyright (C) 2008 Christian Schenk
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
#

require_once('PHPUnit/Framework.php');
require_once('../FileHelper.php');
 
class FileHelperTest extends PHPUnit_Framework_TestCase {

	protected $fileHelper;

	protected function setUp() {
		$this->fileHelper = new FileHelper();
	}

	/**
	 * @test
	 */
	public function getWebFilename() {
		$this->assertEquals('test.txt', $this->fileHelper->getWebFilename('http://example.com/test.txt'));
		$this->assertEquals('test.txt', $this->fileHelper->getWebFilename('ftp://example.com/test.txt'));
		$this->assertEquals('test.txt', $this->fileHelper->getWebFilename('ftp://example.com/test.txt?q=hurz'));
		$this->assertEquals('', $this->fileHelper->getWebFilename('http://example.com/', false));
		# probably not desired
		$this->assertEquals('example.com', $this->fileHelper->getWebFilename('http://example.com'));
		$this->assertEquals('example.com', $this->fileHelper->getWebFilename('http://example.com', false));
	}

	/**
	 * @test
	 * @dataProvider getWebFilenameExceptionProvider 
	 * @expectedException Exception
	 */
	public function getWebFilenameException($file) {
		$this->fileHelper->getWebFilename($file);
	}

	public static function getWebFilenameExceptionProvider() {
        return array(
			array('blah'),
			array('htp://example.com'),
			array('fp://example.com'),
			array('http://example.com/')
		);
	}

	/**
	 * @test
	 */
	public function getFilesInDirectory() {
		$files = $this->fileHelper->getFilesInDirectory('testdir');
		$this->assertEquals(3, count($files));
		for ($i = 1; $i <= 3; $i++) {
			$this->assertTrue(in_array('test'.$i, $files));
		}
	}

	/**
	 * Method name 'fileExists' already exists
	 */
	public function testFileExists() {
		for ($i = 1; $i <= 3; $i++) {
			$this->assertTrue($this->fileHelper->fileExists('testdir/test'.$i));
		}
		$this->assertFalse($this->fileHelper->fileExists('testdir/blah'));
	}

}

?>
