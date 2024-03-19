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
 * Testcases for the opencast filter.
 *
 * @package    filter_opencast
 * @copyright  2024 Justus Dieckmann, University of Münster.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_opencast;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/opencast/tests/testable_filter.php');

/**
 * Testcases for the opencast filter.
 *
 * @package    filter_opencast
 * @copyright  2024 Justus Dieckmann, University of Münster.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group      filter_opencast
 */
class replacement_test extends \advanced_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
        set_config('episodeurl_1', "http://localhost:8080/play/[EPISODEID]\nhttps://stable.opencast.de/play/[EPISODEID]",
                'filter_opencast');
    }

    /**
     * Actual test function.
     *
     * @dataProvider replacement_provider
     * @covers       \filter_opencast
     * @param string $input input for filter
     * @param string $output expected filter output.
     */
    public function test_replacement($input, $output) {
        $filter = new testable_filter(\context_system::instance(), []);
        $this->assertEquals($output, $filter->filter($input));
    }

    /**
     * Provides test cases.
     */
    public function replacement_provider() {
        return [
            [
                ' <p> hello </p> <video src="http://localhost:8080/play/f78ac136-8252-4b8e-bfea-4786c6993f03"> hello </video>',
                ' <p> hello </p> <oc-video episode="f78ac136-8252-4b8e-bfea-4786c6993f03"/>'
            ],
            [
                '<video src="https://somethingother.com"></video><video>
<source
src="https://stable.opencast.de/play/370e5bef-1d59-4440-858a-4df62e767dfc">
</video>',
                '<video src="https://somethingother.com"></video><oc-video episode="370e5bef-1d59-4440-858a-4df62e767dfc"/>'
            ],
            [
                '<video
autoplay loopdiloop
src="http://localhost:8080/play/f9e7b289-c8be-462f-80bf-d1f493c6ed55"></video>',
                '<oc-video episode="f9e7b289-c8be-462f-80bf-d1f493c6ed55"/>'
            ],
            [
                'begin <video>
<source src="https://somethingother.de/play/4380f73a-47a6-41c6-b854-ec0fa9d0261b">
<source
src="https://stable.opencast.de/play/2e0ca3bb-df8e-4913-9380-c925efaf5ac2">
</video> end',
                'begin <oc-video episode="2e0ca3bb-df8e-4913-9380-c925efaf5ac2"/> end'
            ],
            [
                'and a link <a href="https://www.google.com">link</a>
<a href="http://localhost:8080/play/09b9d154-c849-429d-adea-3df4f76429b6">look, a video!</a>',
                'and a link <a href="https://www.google.com">link</a>
<oc-video episode="09b9d154-c849-429d-adea-3df4f76429b6"/>'
            ],
            [
                'and now two <a
href="http://localhost:8080/play/64b085e9-0142-4a10-a08e-3dbce055e740">look, a video!</a>
<video src="http://localhost:8080/play/329885fe-d18e-4c6b-a896-dbc66463a6b2"></video>.',
                'and now two <oc-video episode="64b085e9-0142-4a10-a08e-3dbce055e740"/>
<oc-video episode="329885fe-d18e-4c6b-a896-dbc66463a6b2"/>.'
            ],
        ];
    }

}
