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

use filter_opencast\local\paella_transform;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/filter/opencast/lib.php');

/**
 * Automatic opencast videos filter class.
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2018 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_opencast extends moodle_text_filter {

    public function filter($text, array $options = array()) {
        global $PAGE;
        $i = 0;

        foreach(\tool_opencast\local\settings_api::get_ocinstances() as $ocinstance) {
            // Get baseurl either from opencast tool.
            // TODO also use engage url or repository url.
            $baseurl = \tool_opencast\local\settings_api::get_apiurl($ocinstance->id); // TODO is this always the same url as the playerurl used in the repository?!

            if($ocinstance->id == 2) {
                $baseurl = 'https://electures.uni-muenster.de'; // TODO delete
            }


            if (stripos($text, $baseurl) === false) {
                // Performance shortcut - if there are no </video> tags, nothing can match.
                continue;
            }

            // Looking for tags.
            $matches = preg_split('/(<[^>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            if ($matches) {
                $renderer = $PAGE->get_renderer('filter_opencast');
                $video = false;
                $width = false;
                $height = false;

                foreach ($matches as $match) {
                    // Check if the match is a video tag.
                    if (substr($match, 0, 6) === "<video") {
                        $video = true;
                        preg_match('/width="([0-9]+)"/', $match, $width);
                        preg_match('/height="([0-9]+)"/', $match, $height);
                        $width = $width ? $width[1]: $width;
                        $height = $height ? $height[1] : $height;
                    } else if ($video) {
                        $video = false;
                        if (substr($match, 0, 7) === "<source") {

                            // Check if video is from opencast.
                            if (strpos($match, $baseurl) === false) {
                                continue;
                            }

                            // TODO use api to check if video is available.

                            // Extract url.
                            preg_match_all('/<source[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', $match, $result);

                            // Change url for loading the (Paella) Player.
                            $link = $result['src'][0];

                            // Get episode id from link
                            // TODO matthias fragen, ob die ids immer so aufgebaut sind
                            preg_match_all('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/', $link, $episodeid);

                            # todo handle id not not found

                            $data = paella_transform::get_paella_data_json($ocinstance->id, $episodeid[0][0]);

                            if (strpos($link, 'http') !== 0) {
                                $link = 'http://' . $link;
                            }

                            // Create source with embedded mode.
                            $src = $link;

                            // Collect the needed data being submitted to the template.
                            $mustachedata = new stdClass();
                            $mustachedata->src = $src;
                            $mustachedata->link = $link;
                            $mustachedata->playerid = 'ocplayer_' . $i++;
                            $mustachedata->configurl = '/mod/opencast/config.json';
                            $mustachedata->data = json_encode($data);
                            $mustachedata->width = $width;
                            $mustachedata->height = $height;

                            if (count($data['streams']) === 1) {
                                $sources = $data['streams'][0]['sources'];
                                $res = $sources[array_key_first($sources)][0]['res'];
                                $resolution = $res['w'] . '/' . $res['h'];
                                $mustachedata->resolution = $resolution;

                                if($width xor $height) {
                                    if($width) {
                                        $mustachedata->height = $width * ($res['h'] / $res['w']);
                                    }
                                    else if($height) {
                                        $mustachedata->width = $height * ($res['w'] / $res['h']);
                                    }
                                }
                            }
                            else {
                                if($width && $height) {
                                    $mustachedata->width = $width;
                                    $mustachedata->height = $height;
                                }
                            }

                            $newtext =  $renderer->render_player($mustachedata);
                            // TODO xss possible?!

                            // Replace video tag.
                            $text = preg_replace('/<video(?:(?!<\/video>).)*?' . preg_quote($match, '/') . '.*?<\/video>/', $newtext, $text, 1);
                        }
                        $width = $height = false;
                    }
                }
            }
        }

        // Return the same string except processed by the above.
        return $text;
    }
}
