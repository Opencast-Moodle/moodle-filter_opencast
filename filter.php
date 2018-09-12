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
 *  This filter will replace any links to opencast videos with the selected player from opencast.
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2018 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/filter/opencast/lib.php');

use filter_opencast\domnodelist_reverse_iterator;

/**
 * Automatic opencast videos filter class.
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2018 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_opencast extends moodle_text_filter {

    private static $loginrendered = false;

    public function filter($text, array $options = array()) {
        global $PAGE;

        if (stripos($text, '</video>') === false) {
            // Performance shortcut - if there are no </video> tags, nothing can match.
            return $text;
        }

        $renderer = $PAGE->get_renderer('filter_opencast');

        // Login if user is not logged in yet.
        $loggedin = true;
        if (!isset($_COOKIE['JSESSIONID']) && !self::$loginrendered) {
            // Login and set cookie.
            filter_opencast_login();
            $loggedin = false;
            self::$loginrendered = true;
        }

        $dom = new DOMDocument;
        @$dom->loadHTML($text);

        $videos = $dom->getElementsByTagName('video');
        foreach (new domnodelist_reverse_iterator($videos) as $video) {
            $sources = $video->getElementsByTagName('source');
            foreach (new domnodelist_reverse_iterator($sources) as $source) {
                $sourceurl = $source->getAttribute('src');

                // Get baseurl either from engageurl setting or from opencast tool.
                $baseurl = get_config('filter_opencast', 'engageurl');
                if (empty($baseurl)) {
                    $baseurl = get_config('tool_opencast', 'apiurl');
                }

                // Check if video is from opencast.
                if (strpos($sourceurl, $baseurl) === false) {
                    break;
                }

                if (strpos($baseurl, 'http') !== 0) {
                    $baseurl = 'http://' . $baseurl;
                }

                // Extract id.
                $id = substr($sourceurl, strpos($sourceurl, 'api/') + 4, 36);

                // Create link to video.
                $playerurl = get_config('filter_opencast', 'playerurl');

                // Change url for loading the (Paella) Player.
                $link = $baseurl . $playerurl .'?id=' . $id;

                // Create source with embedded mode.
                $src = $link;

                // Collect the needed data being submitted to the template.
                $mustachedata = new stdClass();
                $mustachedata->loggedin = $loggedin;
                $mustachedata->src = $src;
                $mustachedata->link = $link;

                $newtext =  $renderer->render_player($mustachedata);
                $fragment = $dom->createDocumentFragment();
                $fragment->appendXML($newtext);

                // Replace video tag.
                $video->parentNode->replaceChild($fragment, $video);
                break;
            }
        }

        // Return the same string except processed by the above.
        return $dom->saveHTML();
    }
}
