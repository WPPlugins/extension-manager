<?php
/**
 * FileHelper
 *
 * This file contains the FileHelper and ZipFileHelper class.
 *
 * @author Christian Schenk
 * @version 1.1
 * @package helper
 */

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

require_once('PhpHelper.php');

/**
 * This class reads, downloads, writes and deletes files. And more.
 */
class FileHelper {

	/** true if we need 'allow_url_fopen' set to true */
	private $requireAllowUrlFopen;
	/** true if 'allow_url_fopen' is 'false' and we can't set it to true */
	private $useCurl;

	/**
	 * Constructor
	 * @param bool $requireAllowUrlFopen set this to true if you want to download files over HTTP or FTP
	 */
	public function __construct($requireAllowUrlFopen = false) {
		$this->requireAllowUrlFopen = $requireAllowUrlFopen;
		if ($this->requireAllowUrlFopen) {
			# try setting 'allow_url_fopen' to true if it's false
			if (ini_get('allow_url_fopen') == false) ini_set('allow_url_fopen', 1);
			# if setting 'allow_url_fopen' to true didn't work we'll use cURL
			$this->useCurl = !((bool) ini_get('allow_url_fopen'));
			if ($this->useCurl) {
				$cv = curl_version();
				if (empty($cv)) throw new Exception("You'll have to set 'allow_url_fopen' to 'On' or install cURL.");
			}
		}
	}

	/**
	 * Reads a file and returns the content as a string.
	 * @param string $file a file
	 * @return string a string with the contents of the file
	 */
	public function read($file) {
		if (! $handle = fopen($file, 'rb')) throw new Exception("Can't open file '".$file."'");;
		$content = fread($handle, filesize($file));
		fclose($handle);
		return $content;
	}

	/**
	 * Downloads a file and returns the content as a string.
	 * @param string $file HTTP or FTP location of the file
	 * @return string a string with the contents of the file
	 */
	public function download($file) {
		# hint for users of this class
		if ($this->requireAllowUrlFopen == false) throw new Exception("You'll have to set requireAllowUrlFopen to true");

		if ($this->useCurl) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $file);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$content = curl_exec($ch);
			curl_close($ch);
		} else {
			if (! $handle = fopen($file, 'rb')) throw new Exception("Can't open file '".$file."'");
			$content = stream_get_contents($handle);
			fclose($handle);
		}
		return $content;
	}

	/**
	 * Writes content to a file.
	 * @param string $filename the filename
	 * @param string $content the content for the file
	 */
	public function write($filename, $content) {
		if (! $handle = fopen($filename, 'w')) throw new Exception("Can't open file '".$filename."'");
		if (fwrite($handle, $content) === false) throw new Exception("Can't write to file '".$filename."'");
		if (! fclose($handle)) throw new Exception("Can't close file '".$filename."'");
	}

	/**
	 * Deletes a file or a directory tree.
	 * @param string the filename or path
	 */
	public function delete($file) {
		PhpHelper::deltree($file);
	}

	/**
	 * Here we'll try to guess the filename from a given HTTP or FTP location.
	 *
	 * Almost copied from here: http://php.net/manual/en/function.basename.php#72093
	 *
	 * @param string $file HTTP or FTP location
	 * @param bool $fileNameNotEmpty if the filename turns out to be empty -> throw an exception (defaults to true)
	 * @return string filename
	 */
	public function getWebFilename($file = null, $fileNameNotEmpty = true) {
		if($file === null || strlen($file) <= 0) throw new Exception('Empty location');
		if (! preg_match('/^(ht|f)tp/i', $file)) throw new Exception('You have to specify a HTTP or FTP location');
		$file = explode('?', $file);
		$file = explode('/', $file[0]);
		$basename = $file[count($file)-1];
		if ($fileNameNotEmpty and empty($basename)) throw new Exception("Couldn't get filename from '".$file."'");
		return $basename;    
	}

	/**
	 * Returns an array of all downloaded files without special files like '.'
	 * or '..'.
	 * @param string $directory a directory
	 * @return array an array with all entries in the given directory
	 */
	public function getFilesInDirectory($directory) {
		return array_filter(scandir($directory), 'FileHelper::noDots');
	}
	private static function noDots($file) { return ($file != '.' and $file != '..'); }

	/**
	 * Touch, chmod and check a file for write permission.
	 * @param string $filename a filename
	 * @param int $mode permissions mode of the file
	 * @param bool $checkWriteable should we check write permissions, defaults to true
	 */
	public function touchAndCheckFile($filename, $mode = 0666, $checkWriteable = true) {
		if (! touch($filename)) throw new Exception("Couldn't create file '".$filename."'");
		if (! chmod($filename, $mode)) throw new Exception("Couldn't change permissions of file '".$filename."' to '".$mode."'");
		if ($checkWriteable and ! is_writable($filename)) throw new Exception("The file '".$filename."' isn't writeable");
	}

	/**
	 * Checks whether the given file exists.
	 * @param string $file a file
	 * @return bool true if the file exists, otherwise false
	 */
	public function fileExists($file) {
		return is_file($file);
	}

	/**
	 * Checks whether the given path is a file or a directory.
	 * @param string $path some path
	 * @return bool true if the given path is a file or directory
	 */
	public function fileOrDirExists($path) {
		return $this->fileExists($path) or is_dir($path);
	}

}


/**
 * This class helps us to cope with ZIP files.
 */
class ZipFileHelper {

	/**
	 * Extracts the contents of a ZIP file into some directory.
	 * @param string $zipFile path to a ZIP file
	 * @param string $dir the contents of the ZIP file will be extracted in this directory
	 * @throws Exception in case we can't open the ZIP file
	 */
	public static function extract($zipFile, $dir) {
		$zip = new ZipArchive();
		if ($zip->open($zipFile) !== true) throw new Exception("Coudln't open file '".$zipFile."'");
		if ($zip->extractTo($dir) !== true) throw new Exception("Couldn't extract file '".$zipFile."'");
		if ($zip->close() !== true) throw new Exception("Couldn't close file '".$zipFile."'");
	}

	/**
	 * Retrieves the contents of a ZIP file.
	 * @param string $zipFile path to a ZIP file
	 * @param bool $onlyTopLevel if set to true we'll only include top level files an directories, otherwise we'll include everything (defaults to true)
	 * @throws Exception in case we can't open the ZIP file
	 */
	public static function getContents($zipFile, $onlyTopLevel = true) {
		$zip = new ZipArchive();
		if ($zip->open($zipFile) !== true) throw new Exception("Coudln't open file '".$zipFile."'");
		$zipFileContent = array();
		$i = 0;
		while (true) {
			$stat = $zip->statIndex($i);
			if ($stat === false) break;

			$name = $stat['name'];
			if ($onlyTopLevel === true and strpos($name, '/') !== false) {
				$topLevelDir = explode('/', $name);
				$name = $topLevelDir[0];
			}
			if (in_array($name, $zipFileContent) === false) $zipFileContent[] = $name;

			# don't forget to increment the counter
			$i++;
		}
		if ($zip->close() !== true) throw new Exception("Couldn't close file '".$zipFile."'");
		return $zipFileContent;
	}

}

?>
