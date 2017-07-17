<?php
/*
Plugin Name: Extension Manager
Plugin URI: http://www.christianschenk.org/projects/wordpress-extension-manager/
Description: Install, upgrade, delete and search for plugins and themes.
Version: 0.6.6
Author: Christian Schenk
Author URI: http://www.christianschenk.org/
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

/** Convenience methods */
require_once('extension-manager-helper.php');

#
# Adds a menu to the 'Options' page
#
function wpextmgr_add_options_page() {
	# version okay?
	if (version_compare(phpversion(), '5.0.0', '>')) {
		$showOptionsPage = 'wpextmgr_show_options_page';
	} else {
		$showOptionsPage = 'wpextmgr_show_options_page_php4';
	}
	if(function_exists('add_options_page'))
		add_options_page('Extension Manager', 'Extension Manager', 5, basename(__FILE__), $showOptionsPage);
}
if (function_exists('add_action')) add_action("admin_menu", "wpextmgr_add_options_page");


#
# This functions displays an error message that this plugin doesn't work with PHP4, yet.
#
function wpextmgr_show_options_page_php4() { ?>
	<div class="wrap">
	<h2>Extension Manager</h2>
	<p>You're running PHP <?php echo phpversion() ?>. Please update to at least PHP 5.2 or be patient until this plugin works with PHP 4.</p>
	</div>
<?php
}


#
# The logic and layout of the options page
#
function wpextmgr_show_options_page() {
	# i18n
	load_plugin_textdomain('wpextmgr', 'wp-content/plugins/extension-manager/languages');

	try {
		if(isset($_POST['show_plugins']) or isset($_POST['install_plugin'])) {
			$pm = getPluginManager();

			# download and install the plugin and prepare a message
			if (isset($_POST['install_plugin'])) {
				$pm->downloadAndInstall($_POST['download']);
				$msg = '<p>'.__('Installed plugin', 'wpextmgr').' "'.$_POST['name'].'". ';
				$msg .= sprintf(__('Go activate it on the <a href="%s">plugins screen</a>.', 'wpextmgr'), '/wp-admin/plugins.php').'</p>';
			}

			showBody('show_plugins', $pm, $msg);
		} else if(isset($_POST["show_themes"])) {
			$tm = getThemeManager();
			# TODO implement me
			showBody('show_themes', $tm, $msg);
		} else if(isset($_POST["maintenance"]) or isset($_POST['delete_installed_plugin']) or isset($_POST['delete_downloaded_plugin'])) {
			$pm = getPluginManager();

			# remove installed or downloaded plugin and prepare a message
			if (isset($_POST['delete_installed_plugin'])) {
				$pm->deleteInstalledExtension($_POST['name']);
				$msg = '<p>'.__('Removed installed plugin', 'wpextmgr').' "'.$_POST['name'].'". ';
			} else if (isset($_POST['delete_downloaded_plugin'])) {
				$pm->deleteDownloadedExtension($_POST['name']);
				$msg = '<p>'.__('Removed downloaded plugin', 'wpextmgr').' "'.$_POST['name'].'". ';
			}

			showBody('maintenance', $pm->getInstalledAndDownloadedExtensions(), $msg);
		} else {
			showBody();
		}
	} catch (Exception $ex) {
		# catches every error that might occur
		showBody(NULL, NULL, $ex->getMessage());
	}
}


function showBody($content = 'default', $dataObj = NULL, $specialMsg = NULL) {
	if ($specialMsg != NULL) echo '<div class="updated">'.$specialMsg.'</div>';
?>
	<div class="wrap">
	<h2>Extension Manager</h2>
		<style type="text/css"> p.submit { text-align: left; } </style>
		<form action="" method="post">
			<fieldset class="options">
			<legend><?php _e('What do you want to do?', 'wpextmgr'); ?></legend>
			<p class="submit">
				<input type="submit" name="show_plugins" value="<?php _e('Install Plugins', 'wpextmgr'); ?>" />
				<input type="submit" name="show_themes" value="<?php _e('Install Themes', 'wpextmgr'); ?>" />
				<input type="submit" name="maintenance" value="<?php _e('Maintenance', 'wpextmgr'); ?>" />
			</p>
			</fieldset>
		</form>
		<?php
			switch ($content) {
			case 'default':
				break;
			case 'show_plugins':
			case 'install_plugin': ?>
				<fieldset class="options">
            	<legend><?php _e('Plugins', 'wpextmgr'); ?></legend>
				</fieldset>
			<?php
				getExtensionTable($dataObj->getExtensions());
				break;
			case 'show_themes': ?>
				<fieldset class="options">
            	<legend><?php _e('Themes', 'wpextmgr'); ?></legend>
				</fieldset>
			<?php
				getExtensionTable($dataObj->getExtensions());
				break;
			case 'maintenance': ?>
				<fieldset class="options">
                <legend><?php _e('Maintenance' ,'wpextmgr'); ?></legend>
                </fieldset>
				<table class="widefat">
					<thead>
						<tr>
							<th scope="col"><?php _e('Entity', 'wpextmgr'); ?></th>
							<th scope="col" style="text-align: center"><?php _e('Status', 'wpextmgr'); ?></th>
						</tr>
					</thead>
					<tr><td>allow_url_fopen</td><td style="text-align: center"><?php echo (ini_get('allow_url_fopen') ? 'true' : 'false'); ?></td></tr>
					<tr><td>cURL</td><td style="text-align: center"><?php $cv = curl_version(); echo (!empty($cv) ? 'true' : 'false'); ?></td></tr>
				</table>

				<br/><br/>
				<fieldset class="options">
                <legend><?php _e('Installed and downloaded plugins' ,'wpextmgr'); ?></legend>
                </fieldset>
			<?php
				if (count($dataObj) == 0) {
					echo '<p>'.__("You haven't downloaded any plugins yet.", 'wpextmgr').'</p>';
				} else {
					echo '<p>'.sprintf(__('Before deleting any installed plugins make sure that you\'ve deactivated them on the <a href="%s">plugins screen</a>.', 'wpextmgr'), '/wp-admin/plugins.php').'</p>';
					echo '<p>'.__("You've installed or downloaded these plugins:", 'wpextmgr').'</p>'; ?>
					<table class="widefat">
						<thead>
							<tr>
								<th scope="col"><?php _e('Name', 'wpextmgr'); ?></th>
								<th scope="col"><?php _e('Is installed', 'wpextmgr'); ?></th>
								<th scope="col" style="text-align: center"><?php _e('Remove installed plugin', 'wpextmgr'); ?></th>
								<th scope="col" style="text-align: center"><?php _e('Remove downloaded file', 'wpextmgr'); ?></th>
							</tr>
						</thead>
						<?php foreach ($dataObj as $plugin) { ?>
							<tr>
								<td><?php echo $plugin->getName(); ?></td>
								<td><?php echo ($plugin->isInstalled()?'<strong>'.__('Yes', 'wpextmgr').'</strong>':__('No', 'wpextmgr')); ?></td>
								<td style="text-align: center">
									<?php if ($plugin->isInstalled()) { ?>
									<form action="" method="post">
										<input class="submit" type="submit" name="delete_installed_plugin" value="<?php _e('Remove installation', 'wpextmgr'); ?>" />
										<input type="hidden" name="name" value="<?php echo $plugin->getName(); ?>" />
									</form>
									<?php } ?>
								</td>
								<td style="text-align: center">
									<?php if ($plugin->isDownloaded()) { ?>
									<form action="" method="post">
										<input class="submit" type="submit" name="delete_downloaded_plugin" value="<?php _e('Remove download', 'wpextmgr'); ?>" />
										<input type="hidden" name="name" value="<?php echo $plugin->getName(); ?>" />
									</form>
									<?php } ?>
								</td>
							<tr>
						<?php } ?>
					</table>
			<?php }
				break;
			}
		?>
	</div>
<?php
}


function getExtensionTable($extensions) {
	if (count($extensions) < 1) {
		echo '<em>'.__('Nothing found', 'wpextmgr').'</em>';
		return;
	}

	getFilterTable();

?>
<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e('Name', 'wpextmgr'); ?></th>
			<th scope="col"><?php _e('Version', 'wpextmgr'); ?></th>
			<th scope="col" style="text-align: center"><?php _e('Install', 'wpextmgr'); ?></th>
		</tr>
	</thead>
	<?php
		/* holds the number of the current element, i.e. 0 for the first, 1 for the second, etc.
		   -> this is used to alternate the color of the rows */
		$nrOfCurrentElement = 0;
		$startElement = getStartElement();
		$maxElementsInTable = getNrOfElements();
		$searchString = getSearchString();
		foreach ($extensions as $extension) {
			/*
			 * We'll skip the rest of the loop if this comes to be true:
			 * - the maximum number of elements isn't set to 'All'
			 * - the current element is smaller than the start element or
			 *   greater than the start element plus the number of maximum elements
			 * - in case of a search we'd like to show all elements, so only if the
			 *   search string is NULL we allow to skip anyway
			 */
			if ($maxElementsInTable != 'All' and $searchString == NULL) {
				if ($nrOfCurrentElement < $startElement or $nrOfCurrentElement > $startElement + $maxElementsInTable) {
					$nrOfCurrentElement++;
					continue;
				}
			}
			# here's where the search takes place
			if (! matchesSearch($searchString, $extension)) continue;
		?>
			<tr valign="top"<?php echo (($nrOfCurrentElement % 2 == 0)?' class="alternate"':''); ?>>
				<td>
					<a href="<?php echo $extension->getHref(); ?>"><?php echo $extension->getName(); ?></a>
				</td>
				<td>
					<?php echo $extension->getVersion(); ?>
				</td>
				<td style="text-align: center">
					<form action="" method="post">
						<input class="submit" type="submit" name="install_plugin" value="<?php _e('Install Plugin', 'wpextmgr'); ?>" />
						<input type="hidden" name="name" value="<?php echo $extension->getName(); ?>" />
						<input type="hidden" name="download" value="<?php echo $extension->getDownload(); ?>" />
					</form>
				</td>
			</tr>
			<?php
			# don't forget to increment the counter
			$nrOfCurrentElement++;
		} ?>
</table>
<?php
}


function getFilterTable() { ?>
	<form name="filter" action="" method="post">
	<input type="hidden" name="show_plugins"/>
	<table class="widefat">
		<tr class="alternate">
			<td><label for="filter_nr_of_elements"><?php _e('Plugins per page', 'wpextmgr'); ?></label>:
				<select name="filter_nr_of_elements" id="filter_nr_of_elements">
					<?php
						$nrOfElemSelected = getNrOfElements();
						$nrOfElements = array('All', 10, 20, 50, 100);
						foreach ($nrOfElements as $nrOfElement) {
							echo '<option'.(($nrOfElemSelected == $nrOfElement)?' selected="selected"':'').' value="'.$nrOfElement.'">'.$nrOfElement.'</option>';
						}
					?>
				</select>
				<?php //<input type="hidden" name="filter_start_element" id="filter_start_element" value="<?php echo (($nrOfElemSelected != 'All')?getStartElement() + $nrOfElemSelected : 0);" /?>
				<input type="hidden" name="filter_start_element" id="filter_start_element" value="<?php echo getStartElement(); ?>" />
				<input type="submit" name="prev" id="prev" value="<?php _e('Previous', 'wpextmgr'); ?>" />
				<input type="submit" name="next" id="next" value="<?php _e('Next', 'wpextmgr'); ?>" />
			</td>
			<td><label for="filter_search_string"><?php _e('Search terms', 'wpextmgr'); ?></label>:
				<input type="text" name="filter_search_string" id="filter_search_string" value="<?php echo $_POST['filter_search_string']; ?>" />
			</td>
			<td><input type="submit" name="filter" value="<?php _e('Filter', 'wpextmgr'); ?>"/></td>
		</tr>
	</table>
	</form>
<?php
}

function matchesSearch($searchStrings, $extension) {
	if ($searchStrings == NULL) return true;
	$searchStrings = explode(' ', $searchStrings);
	$fields = array($extension->getName(), $extension->getVersion(), $extension->getDownload(), $extension->getHref());
	#$found = false;
	foreach ($searchStrings as $searchString) {
		foreach ($fields as $field) {
			if (! strpos($field, $searchString) === false) { return true; }
	#		if (! strpos($field, $searchString) === false) {
	#			$found = true;
	#			break;
	#		}
		}
	#	if ($found === true) break;
	}
	#return $found;
	return false;
}

function getNrOfElements() {
	if (!isset($_POST['filter_nr_of_elements'])) return 10;
	else return $_POST['filter_nr_of_elements'];
}

# TODO if the start element is greater than the nr fo elements available, we
#      should start from zero again
function getStartElement() {
	if (!isset($_POST['filter_start_element'])) {
		return 0;
	} else {
		$nrOfElementsFactor = ((getNrOfElements() != 'All')?getNrOfElements() : 10);
		if (isset($_POST['next'])) {
			# TODO see to-do of this method; insert a $next here similar to the one of $prev
			return $_POST['filter_start_element'] + $nrOfElementsFactor;
		} else if (isset($_POST['prev'])) {
			$prev = $_POST['filter_start_element'] - $nrOfElementsFactor;
			if ($prev < 0) $prev = 0;
			return $prev;
		}
		#echo 'Returning without next';
		return $_POST['filter_start_element'];
	}
}

function getSearchString() {
	if (!isset($_POST['filter_search_string'])) return NULL;
	else return strtolower($_POST['filter_search_string']);
}

?>
