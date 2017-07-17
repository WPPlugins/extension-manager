<?php
/**
 * extension-manager-helper
 *
 * Contains some convenience methods for the PluginManager and ThemeManager.
 *
 * @author Christian Schenk
 * @version 1.0
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

/**
 * Returns an instance of the PluginManager.
 * @exception throws an exception if an error occurred
 */
function getPluginManager() {
	require_once('PluginManager.php');
	try {
		return new PluginManager();
	} catch(Exception $ex) {
		$errorMsg = '<p>Correct these errors first:</p><p>'.$ex->getMessage().'</p>';
		throw new Exception($errorMsg);
	}
}

/**
 * Returns an instance of the ThemeManager.
 * @exception throws an exception if an error occurred
 */
function getThemeManager() {
	require_once('ThemeManager.php');
	try {
		return new ThemeManager();
	} catch(Exception $ex) {
		$errorMsg = '<p>Correct these errors first:</p><p>'.$ex->getMessage().'</p>';
		throw new Exception($errorMsg);
	}
}
?>
