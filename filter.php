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
 *  Opencast filtering
 *
 *  This filter will replace any links to opencast videos with the opencast theodul pass player.
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2017 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/filter/opencast/lib.php');
require_once($CFG->libdir . '/oauthlib.php');

/**
 * Automatic opencast videos filter class.
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2017 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_opencast extends moodle_text_filter {

    public function filter($text, array $options = array()) {
        global $CFG, $PAGE;

        if (stripos($text, '</video>') === false) {
            // Performance shortcut - if there are no </video> tags, nothing can match.
            return $text;
        }

        // Looking for tags.
        $matches = preg_split('/(<[^>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        if ($matches) {

            // Login if user is not logged in yet.
            $loggedin = true;
            if (!isset($_COOKIE['JSESSIONID'])) {
                // Login and set cookie.
                filter_opencast_login();
                $loggedin = false;
            }

            $video = false;

            foreach ($matches as $match) {
                if (substr($match, 0, 6) === "<video") {
                    $video = true;
                } else if ($video) {
                    $video = false;
                    if (substr($match, 0, 7) === "<source") {
                        // Get apiurl from opencast tool.
                        $apiurl = get_config('tool_opencast', 'apiurl');

                        // Check if video is from opencast.
                        if (strpos($match, $apiurl) === false) {
                            continue;
                        }

                        if (strpos($apiurl, 'http') !== 0) {
                            $apiurl = 'http://' . $apiurl;
                        }

                        // Extract id.
                        $id = substr($match, strpos($match, 'api/') + 4, 36);
                        $src = $CFG->wwwroot . '/filter/opencast/player/core.html?id=' . $id . '&ocurl=' . urlencode($apiurl);

                        if ($loggedin) {
                            // Set the source attribute directly.
                            $player = '<iframe src="' . $src . '" width="95%" height="455px" class="ocplayer"></iframe>';
                        } else {
                            // Set the source attribute after login.
                            $player = '<iframe data-frameSrc="' . $src . '" width="95%" height="455px" class="ocplayer"></iframe>';

                        }

                        $link = $apiurl . '/engage/theodul/ui/core.html?id=' . $id;
                        // Add link to video.
                        $newtext = $player . '<a style="display:block;" target="_blank" href="' . $link . '">Zum Video</a>';
                        // Replace video tag.
                        $text = preg_replace('/<video.*<\/video>/', $newtext, $text, 1);
                    }
                }
            }
        }

        // Return the same string except processed by the above.
        return $text;
    }


}
