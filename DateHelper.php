<?php
/**
 * DateHelper
 *
 * This file contains the DateHelper class.
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
 * This class works with times and dates.
 */
class DateHelper {

	/**
	 * Checks whether the first parameter is newer than the second.
	 * @param string $date1 the first date
	 * @param string $date2 the second date
	 * @return bool true if the first date is newer than the second, otherwise false
	 */
	public static function isNewer($date1, $date2) {
		if (($time1 = strtotime($date1)) === -1) throw new Exception("Couldn't parse date '".$date1."'");
		if (($time2 = strtotime($date2)) === -1) throw new Exception("Couldn't parse date '".$date2."'");
		if (!is_int($time1) or !is_int($time2)) throw new Exception("Can't compare '".$date1."' (".$time1.") and '".$date2."' (".$time2.")");
		return ($time1 > $time2);
	}

}

?>
