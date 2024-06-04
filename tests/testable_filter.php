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
 * Testable opencast filter.
 *
 * @package    filter_opencast
 * @copyright  2024 Justus Dieckmann, University of Münster.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_opencast;
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/filter/opencast/filter.php');

/**
 * Testable opencast filter.
 *
 * @package    filter_opencast
 * @copyright  2024 Justus Dieckmann, University of Münster.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_filter extends \filter_opencast {

    /**
     * Render a simple
     * @param int $ocinstanceid Id of ocinstance.
     * @param string $episodeid Id opencast episode.
     * @param int $playerid Unique id to assign to player element.
     * @param int|null $width Optionally width for player.
     * @param int|null $height Optionally height for player.
     * @return string
     */
    protected function render_player(int $ocinstanceid, string $episodeid, int $playerid, $width = null,
            $height = null): string {
        return '<oc-video episode="'. $episodeid . '"/>';
    }

}
