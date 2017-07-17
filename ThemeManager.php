<?php
/**
 * ThemeManager
 *
 * This file contains the ThemeManager class.
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

/** ExtensionManager */
require_once('ExtensionManager.php');

$THEME_ROOT_DIR = dirname(__FILE__);
define('THEMES_DOWNLOAD_DIR', $THEME_ROOT_DIR.'/themes');
define('THEMES_FILE', THEMES_DOWNLOAD_DIR.'/themes.xml');
define('THEMES_UPDATE_FILE', THEMES_DOWNLOAD_DIR.'/themes-update.txt');
define('THEMES_LOCATION', 'http://data.christianschenk.org/wordpress-extensions-manager/data/themes.xml.gz');
define('THEMES_LOCATION_IS_GZIP', true);
define('THEMES_UPDATE_LOCATION', 'http://data.christianschenk.org/wordpress-extensions-manager/data/themes-update.txt');
define('THEMES_INSTALL_DIR', $THEME_ROOT_DIR.'/../');

/**
 * This class manages themes.
 */
class ThemeManager extends ExtensionManager {

	public function __construct() {
		throw new Exception('Not implemented yet');
		parent::__construct(THEMES_DOWNLOAD_DIR, THEMES_FILE, THEMES_UPDATE_FILE, THEMES_LOCATION, THEMES_UPDATE_LOCATION, THEMES_LOCATION_IS_GZIP, THEMES_INSTALL_DIR);
	}

	public function getExtensions() {
		return parent::getExtensions('theme');
	}

}

?>
