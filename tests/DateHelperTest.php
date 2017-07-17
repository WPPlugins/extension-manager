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
require_once('../DateHelper.php');
 
class DateHelperTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function isNewer() {
		$this->assertTrue(DateHelper::isNewer('2000-01-02', '2000-01-01'));
		$this->assertFalse(DateHelper::isNewer('2000-01-01', '2000-01-01'));
		$this->assertFalse(DateHelper::isNewer('2000-01-01', '2000-01-02'));
	}

	/**
	 * @test
	 * @dataProvider isNewerExceptionProvider
	 * @expectedException Exception
	 */
	public function isNewerException($date1, $date2) {
		DateHelper::isNewer($date1, $date2);
	}

	public static function isNewerExceptionProvider() {
		return array(
			array('hurz', '2000-01-01'),
			array('2000-01-01', 'hurz'),
			array('hurz', 'hurz')
		);
	}
 
}

?>
