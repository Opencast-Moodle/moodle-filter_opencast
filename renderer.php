<?php
// This file is part of a plugin for Moodle - http://moodle.org/
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
 * Renderer definition.
 *
 * @package   filter_opencast
 * @copyright 2018 Tamara Gunkel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir . '/weblib.php');

/**
 * Class for rendering opencast videos.
 *
 * @package   filter_opencast
 * @copyright 2018 Tamara Gunkel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_opencast_renderer extends plugin_renderer_base {

    /**
     * Display the player.
     *
     * @param object $data The prepared variables.
     * @return string
     */
    public function render_player($data) {
        return $this->render_from_template('filter_opencast/player', $data);
    }

    /**
     * Display the lti form.
     *
     * @param string $endpoint the opencast endpoint.
     * @param array $params parameter to be passed.
     * @return string
     */
    public function render_lti_form($endpoint, $params) {
        $content = "<form action=\"" . urlencode($endpoint) .
            "\" name=\"ltiLaunchForm\" id=\"ltiLaunchForm\" method=\"post\" encType=\"application/x-www-form-urlencoded\">\n";

        // Construct html form for the launch parameters.
        foreach ($params as $key => $value) {
            $key = htmlspecialchars($key);
            $value = htmlspecialchars($value);
            $content .= "<input type=\"hidden\" name=\"{$key}\"";
            $content .= " value=\"";
            $content .= $value;
            $content .= "\"/>\n";
        }
        $content .= "</form>\n";

        return $content;
    }
}