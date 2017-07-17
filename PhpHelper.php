<?php
/**
 * PhpHelper
 *
 * This file contains the PhpHelper class.
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
 * This class contains some hacks for PHP.
 */
class PhpHelper {

	/*
	 * Copied from here: http://php.net/manual/function.gzfile.php#38967
	 */
	public static function gzfile_get_contents($filename, $use_include_path = 0) {
		$file = @gzopen($filename, 'rb', $use_include_path);
		if ($file) {
			$data = '';
			while (!gzeof($file)) {
				$data .= gzread($file, 1024);
			}
			gzclose($file);
		}
		return $data;
	} 

	/*
	 * Copied from here: http://php.net/manual/function.rmdir.php#80338
	 */
	public static function deltree($f){
		if( is_dir($f) ) {
			foreach( scandir($f) as $item ) {
				if( !strcmp($item, '.') || !strcmp($item, '..') ) continue;
				PhpHelper::deltree( $f . "/" . $item );
			}
			rmdir( $f );
		} else {
			unlink( $f );
		}
	}

}

?>
