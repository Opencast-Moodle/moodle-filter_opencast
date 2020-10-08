<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Tests for filter_opencast.
 *
 * @package    filter_opencast
 * @category   test
 * @copyright  2020 Nina Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/opencast/filter.php');

/**
 * Unit tests for Opencast filter.
 *
 * The Filter displays the data given from the repository opencast to an iFrame
 *
 * @package    filter_opencast
 * @category   test
 * @copyright  2020 Nina Herrmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_opencast_testcase extends advanced_testcase {

    /** @var object The filter plugin object to perform the tests on */
    protected $filter;

    /**
     * Setup the test framework
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->filter = new filter_opencast(context_system::instance(), array());
    }

    /**
     * Perform the actual tests, once the unit test is set up.
     *
     * @return void
     */
    public function test_filter_opencast() {
        global $CFG;

        $filter = new filter_opencast;
        $string = $filter->filter('www.some.de/url/');
        $this->assertEquals('', '');
    }
}
