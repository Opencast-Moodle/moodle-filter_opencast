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
require_once($CFG->dirroot.'/filter/opencast/lib.php');

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
        global $CFG;
        //Checks momentarily only for videos embedded in <video> tag

        if (stripos($text, '</video>') === false) {
            // Performance shortcut - if there are no </video> tags, nothing can match.
            return $text;
        }

        // Looking for tags.
        $matches = preg_split('/(<[^>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        if ($matches) {
            $issuerid = get_config('filter_opencast', 'issuerid');
            $issuer = \core\oauth2\api::get_issuer($issuerid);
            // Get an OAuth client from the issuer.
            // TODO return url
            $returnurl = new moodle_url('/filter/opencastfilter/adminsettings.php');
            $client = \core\oauth2\api::get_user_oauth_client($issuer, $returnurl);
            $service = new \filter_opencast\rest($client);

            // Get me.json
            $me = $service->call('me', []);
            file_put_contents($CFG->dirroot . '\filter\opencast\info\me.json', $me);

            foreach ($matches as $match) {
                if (substr($match, 0, 6) === "<video") {
                    // Replace it
                    // TODO
                    $id = substr($match, strpos($match, 'id=') + 4, 36);
                    // Get episode.json
                    $id = '0cc5a97c-82fd-4358-9266-351dc1b1b046';
                    $params = ['id' => $id];
                    $episode = $service->call('episode', $params);
                    file_put_contents($CFG->dirroot . '\filter\opencast\info\episode.json', $episode);

                    $player = '<iframe src="'.$CFG->wwwroot.'/filter/opencast/player/core.html" width="100%" height="400px"></iframe>';
		            $text = preg_replace('/<video.*<\/video>/', $player, $text, 1);
                }
            }
        }

        $newtext = $text;

        // Return the same string except processed by the above.
        return $newtext;
    }


}
