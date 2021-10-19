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


use mod_opencast\local\paella_transform;

defined('MOODLE_INTERNAL') || die();

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

        if (stripos($text, '</video>') === false) {
            // Performance shortcut - if there is no </video> tag, nothing can match.
            return $text;
        }

        foreach(\tool_opencast\local\settings_api::get_ocinstances() as $ocinstance) {
            $episodeurl = get_config('filter_opencast','episodeurl_' . $ocinstance->id);
            $urlparts = parse_url($episodeurl);
            $baseurl = $urlparts['scheme'] . '://' . $urlparts['host'];
            if($urlparts['port']) {
                $baseurl .= $urlparts['port'];
            }

            if (empty($episodeurl) || stripos($text, $baseurl) === false) {
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
                                $width = $height = false;
                                continue;
                            }

                            // Extract url.
                            preg_match_all('/<source[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', $match, $result);

                            // Change url for loading the (Paella) Player.
                            $link = $result['src'][0];

                            // Get episode id from link
                            $episoderegex = "/" . preg_quote($episodeurl, "/") . "/";
                            $episoderegex = preg_replace('/\\\\\[EPISODEID\\\]/','([0-9a-zA-Z\-]+)', $episoderegex);
                            $nummatches = preg_match_all($episoderegex, $link, $episodeid);

                            if(!$nummatches) {
                                $width = $height = false;
                                continue;
                            }

                            $data = paella_transform::get_paella_data_json($ocinstance->id, $episodeid[1][0]);

                            // Collect the needed data being submitted to the template.
                            $mustachedata = new stdClass();
                            $mustachedata->playerid = 'ocplayer_' . $i++;
                            $mustachedata->configurl = get_config('filter_opencast','configurl_' . $ocinstance->id);
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
