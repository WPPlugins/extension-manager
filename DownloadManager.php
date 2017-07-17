<?php
/**
 * DownloadManager
 *
 * This file contains the DownloadManager class.
 *
 * @author Christian Schenk
 * @version 1.0
 * @package wpextmgr
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

/** FileHelper */
require_once('FileHelper.php');

/**
 * This class can be used to download files from a HTTP or FTP location into
 * a download directory. You can delete downloaded files too.
 */
class DownloadManager {

	/** FileHelper class */
	private $fileHelper;
	/** the downloads will be saved here */
	private $downloadDir;

 	/**
	 * You can supply a parameter to change the download directory.
	 * @param string $downloadDir download directory of your choice (defaults to 'download')
	 */
	public function __construct($downloadDir = 'download') {
		$this->fileHelper = new FileHelper(true);
		# set download directory and check whether it's writeable
		$this->downloadDir = $downloadDir;
		if (! is_writable($this->downloadDir)) throw new Exception("The download directory '".$this->downloadDir."' isn't writeable");
	}

	/**
	 * Downloads a file to the local download directory.
	 * @retrun string the name of the saved file
	 */
	public function download($file) {
		# get the filename and check whether this is a valid location
		$filename = $this->composeFileName($this->fileHelper->getWebFilename($file));
		# download the file into a string
		$content = $this->fileHelper->download($file);
		# create a file for the downloaded content
		$this->fileHelper->touchAndCheckFile($filename);
		# write content to file
		$this->fileHelper->write($filename, $content);

		return $filename;
	}

	/**
	 * Composes a filename that consists of the download directory and the
	 * given filename.
	 */
	private function composeFileName($file) {
		return $this->downloadDir.'/'.$file;
	}

	/**
	 * Returns an array of all downloaded files.
	 * @return array an array with all files from the download directory
	 */
	public function getDownloadedFiles() {
		return $this->fileHelper->getFilesInDirectory($this->downloadDir);
	}

	/**
	 * Returns the name of the given file in the download directory.
	 * @param string $filename a filename
	 * @return string filename in the download directory
	 */
	public function getDownloadedFile($filename) {
		$file = $this->downloadDir.'/'.$filename;
		if ($this->fileHelper->fileExists($file) == false) throw new Exception("The file '".$file."' doesn't exist");
		return $file;
	}

	/**
	 * Deletes one of the downloaded files.
	 */
	public function deleteDownloadedFile($file) {
		#if (! in_array($file, $this->getDownloadedFiles())) throw new Exception("There's no downloaded file '".$file."'");
		$filename = $this->getDownloadedFile($file);
		$this->fileHelper->delete($filename);
	}

}

?>
