<?php
/**
 * PluginManager
 *
 * This file contains the PluginManager class.
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

$PLUGIN_ROOT_DIR = dirname(__FILE__);
define('PLUGINS_DOWNLOAD_DIR', $PLUGIN_ROOT_DIR.'/plugins');
define('PLUGINS_FILE', PLUGINS_DOWNLOAD_DIR.'/plugins.xml');
define('PLUGINS_UPDATE_FILE', PLUGINS_DOWNLOAD_DIR.'/plugins-update.txt');
define('PLUGINS_LOCATION', 'http://data.christianschenk.org/wordpress-extensions-manager/data/plugins.xml.gz');
define('PLUGINS_LOCATION_IS_GZIP', true);
define('PLUGINS_UPDATE_LOCATION', 'http://data.christianschenk.org/wordpress-extensions-manager/data/plugins-update.txt');
define('PLUGINS_INSTALL_DIR', $PLUGIN_ROOT_DIR.'/../');

/**
 * This class manages plugins.
 */
class PluginManager extends ExtensionManager {

	public function __construct() {
		parent::__construct(PLUGINS_DOWNLOAD_DIR, PLUGINS_FILE, PLUGINS_UPDATE_FILE, PLUGINS_LOCATION, PLUGINS_UPDATE_LOCATION, PLUGINS_LOCATION_IS_GZIP, PLUGINS_INSTALL_DIR);
	}

	public function getExtensions() {
		return parent::getExtensions('plugin');
	}

}

?>
