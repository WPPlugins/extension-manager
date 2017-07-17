<?php
/**
 * ExtensionManager
 *
 * This file contains ExtensionManager and WordPressExtension.
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
/** DownloadManager */
require_once('DownloadManager.php');
/** PhpHelper */
require_once('PhpHelper.php');
/** DateHelper */
require_once('DateHelper.php');

/**
 * This abstract class manages extensions for WordPress, i.e. plugins and themes.
 */
abstract class ExtensionManager {

	/** FileHelper class */
	private $fileHelper;
	/** DownloadManager class */
	private $downloadManager;
	/** XML file with information about the extensions */
	private $extensionFile;
	/** File with a single date (for update checks)*/
	private $extensionUpdateFile;
	/** Download location of the XML file with information about the extensions */
	private $extensionLocation;
	/** True if the XML file is gzipped, otherwise false */
	private $extensionLocationIsGzip;
	/** Download location of the file with update information */
	private $extensionUpdateLocation;
	/** Installation directory for the extensions (extensions will be unzipped here) */
	private $extensionInstallDir;

 	/**
	 * If there's no extensionFile, we'll download it. If it's already there,
	 * we'll check for a new version. If there's a new version we'll download
	 * it.
	 */
	protected function __construct($downloadDir, $extensionFile, $extensionUpdateFile, $extensionLocation, $extensionUpdateLocation, $extensionLocationIsGzip, $extensionInstallDir) {
		$this->fileHelper = new FileHelper();
		$this->downloadManager = new DownloadManager($downloadDir);

		$this->extensionFile = $extensionFile;
		$this->extensionUpdateFile = $extensionUpdateFile;
		$this->extensionLocation = $extensionLocation;
		$this->extensionLocationIsGzip = $extensionLocationIsGzip;
		$this->extensionUpdateLocation = $extensionUpdateLocation;
		$this->extensionInstallDir = $extensionInstallDir;
		if (! is_writable($this->extensionInstallDir)) throw new Exception("The installation directory '".$this->extensionInstallDir."' isn't writeable");

		if (! $this->fileHelper->fileExists($this->extensionFile)) {
			$this->updateExtensionFile();
		} else {
			$oldDate = $this->getDateFromExtensionFile();
			$this->downloadManager->download($this->extensionUpdateLocation);
			$newDate = $this->fileHelper->read($this->extensionUpdateFile);
			if (DateHelper::isNewer($newDate, $oldDate)) $this->updateExtensionFile();
		}
	}

	/**
	 * Updates the local plugins file.
	 */
	private function updateExtensionFile() {
		$this->downloadManager->download($this->extensionLocation);
		# gunzip file
		if ($this->extensionLocationIsGzip) {
			$extensionFile = PhpHelper::gzfile_get_contents($this->extensionFile.'.gz');
			$this->fileHelper->write($this->extensionFile, $extensionFile);
		}
	}

	protected function getExtensions($type = NULL) {
		if ($type == NULL) throw new Exception("Supply a type.");
		$extensions = array();
		$xml = $this->getXML();
		foreach ($xml->plugin as $plugin) {
			$extensions[] = WordPressExtension::get($type, $plugin['name'], $plugin['version'], $plugin['download'], $plugin['href']);
		}
		return $extensions;
	}

	private function getXML() {
		$pluginsXML = $this->fileHelper->read($this->extensionFile);
		return new SimpleXMLElement($pluginsXML);
	}

	/**
	 * This convenience method returns the date attribute from the plugins
	 * file.
	 * @return string the date attribute as a string
	 */
	private function getDateFromExtensionFile() {
		$xml = $this->getXML();
		$date = $xml['date'];
		if (empty($date)) throw new Exception("Couldn't get date from extensions file '".basename($this->extensionFile)."'. Corrupt?");
		return $date;
	}

	/**
	 * Downloads an extension.
	 */
	public function downloadAndInstall($file) {
		# download file
		$extensionFilename = $this->downloadManager->download($file);
		# extract file to WP dir
		ZipFileHelper::extract($extensionFilename, $this->extensionInstallDir);
	}

	/**
	 * Returns the filename of a file in the installation directory.
	 * @return string filename
	 */
	private function getInstalledFile($file) {
		return $this->extensionInstallDir.'/'.$file;
	}

	/**
	 * Deletes a installed extension.
	 */
	public function deleteInstalledExtension($file) {
		$this->fileHelper->delete($this->getInstalledFile($file));
	}

	/**
	 * Deletes a downloaded extension.
	 */
	public function deleteDownloadedExtension($file) {
		$this->downloadManager->deleteDownloadedFile($file);
	}

	/**
	 * Returns an array of all installed (i.e. downloaded or installed)
	 * extensions. This method is a combination if getInstalledExtensions() and
	 * getDownloadedExtensions().
	 * @see getInstalledExtensions
	 * @see getDownloadedExtensions
	 * @return array downloaded or installed extensions
	 */
	public function getInstalledAndDownloadedExtensions() {
		$installedAndDownloadedExtensions = array();
		foreach ($this->getInstalledExtensions() as $installedExtension) {
			$extension = new WordPressExtension();
			$extension->setName($installedExtension);
			$extension->setInstalled();
			$installedAndDownloadedExtensions[] = $extension;
		}

		foreach ($this->getDownloadedExtensions() as $downloadedExtension) {
			$extension = new WordPressExtension();
			$extension->setName($downloadedExtension);
			$extension->setInstalled($this->isInstalled($downloadedExtension));
			$extension->setDownloaded();
			$installedAndDownloadedExtensions[] = $extension;
		}

		return $installedAndDownloadedExtensions;
	}
	/*private function isExtensionNamePresent(array $extensions, $name) {
		$name = strtolower($name);
		foreach ($extensions as $extension) {
			if ($name == strtolower($extension->getName())) return true;
		}
		return false;
	}*/

	/**
	 * Returns the installed extensions, i.e. all files from the installation directory.
	 * @return array installed extensions
	 */
	private function getInstalledExtensions() {
		return $this->fileHelper->getFilesInDirectory($this->extensionInstallDir);
	}

	/**
	 * Returns the downloaded extensions, i.e. all files from the download directory.
	 * @return array downloaded extensions
	 */
	private function getDownloadedExtensions() {
		$files = $this->downloadManager->getDownloadedFiles();
		$excludeFiles = array(basename($this->extensionFile), basename($this->extensionUpdateFile));
		if ($this->extensionLocationIsGzip) $excludeFiles[] = basename($this->extensionFile.'.gz');
		return array_diff($files, $excludeFiles);
	}

	/**
	 * Checks whether the given extension is installed.
	 * @param string $extension the extensions filename
	 */
	private function isInstalled($extension) {
		$downloadLocationOfExtension = $this->downloadManager->getDownloadedFile($extension);
		$extensionFiles = ZipFileHelper::getContents($downloadLocationOfExtension);
		foreach ($extensionFiles as $extensionFile) {
			if ($this->fileHelper->fileOrDirExists($this->extensionInstallDir.$extensionFile)) return true;
		}
		return false;
	}

}


/**
 * This class models an extension for WordPress, i.e. a plugin or theme.
 * @package model
 */
class WordPressExtension {

	private $type;
	private $name;
	private $version;
	private $download;
	private $href;
	private $installed;
	private $downloaded;

	/**
	 * Constructor
	 * @param string $type type
	 * @param string $name name
	 * @param string $version version
	 * @param string $download
	 * @param string $href href
	 */
	#public function __construct($type, $name, $version, $download, $href) {
	public function __construct() {
		$this->installed = false;
		$this->downloaded = false;
	}

	/**
	 * Make sure that the type is set correctly, i.e. either to 'plugin' or
	 * 'theme'.
	 * @param string $type the type
	 */
	private function setType($type) {
		if ($type != 'plugin' and $type != 'theme') throw new Exception("Type must be 'plugin' or 'theme'");
		$this->type = $type;
	}
	public function getType() { return $this->type; }
	public function getName() { return $this->name; }
	public function setName($name) { $this->name = $name; }
	public function getVersion() { return $this->version; }
	public function setVersion($version) { $this->version = $version; }
	public function getDownload() { return $this->download; }
	public function setDownload($download) { $this->download = $download; }
	public function getHref() { return $this->href; }
	public function setHref($href) { $this->href = $href; }
	public function isInstalled() { return $this->installed; }
	public function setInstalled($installed = true) { $this->installed = $installed; }
	public function isDownloaded() { return $this->downloaded; }
	public function setDownloaded($downloaded = true) { $this->downloaded = $downloaded; }

	/**
	 * Since we can't overload the constructor, we'll have to supply this
	 * method to conveniently create new objects.
	 */
	public static function get($type, $name, $version, $download, $href) {
		$extension = new WordPressExtension();
		$extension->setType($type);
		$extension->setName($name);
		$extension->setVersion($version);
		$extension->setDownload($download);
		$extension->setHref($href);
		return $extension;
	}

}

?>
