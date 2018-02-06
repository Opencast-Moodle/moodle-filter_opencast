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
        //Checks momentarily only for videos embedded in <video> tag

        if (stripos($text, '</video>') === false) {
            // Performance shortcut - if there are no </video> tags, nothing can match.
            return $text;
        }

        // Looking for tags.
        $matches = preg_split('/(<[^>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        if ($matches) {
            // Get me.json
            filter_opencast_load_meInfo();

            $video = false;

            foreach ($matches as $match) {
                if (substr($match, 0, 6) === "<video") {
                    $video = true;
                } else if ($video) {
                    $video = false;
                    if (substr($match, 0, 7) === "<source") {
                        // Extract id
                        $id = substr($match, strpos($match, 'api/') + 4, 36);

                        $player = '<iframe src="" data-frameSrc="' . $CFG->wwwroot . '/filter/opencast/player/core.html?id='.$id.'" width="80%" height="450px" class="ocplayer"></iframe>';
                        $link = get_config('filter_opencast', 'baseurlapi') . '/engage/theodul/ui/core.html?id=' . $id;
                        $newtext = $player . '<a style="display:block;" href="' . $link . '">Zum Video</a>';
                        $text = preg_replace('/<video.*<\/video>/', $newtext, $text, 1);

                    }
                }
            }
        }

        // Return the same string except processed by the above.
        return $text;
    }


}
