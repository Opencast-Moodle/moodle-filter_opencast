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
 * @package    filter_opencast
 * @copyright  2024 Justus Dieckmann and Tamara Gunkel, University of Münster
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_opencast\local\paella_transform;

/**
 * Automatic opencast videos filter class.
 *
 * @package    filter_opencast
 * @copyright  2024 Justus Dieckmann and Tamara Gunkel, University of Münster
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_opencast extends moodle_text_filter {

    /**
     * Get the content of the attribute $attributename from $tag.
     * @param string $tag HTML-Tag.
     * @param string $attributename Name of the attribute.
     * @param string $attributecontentregex Regex of what the content of the attribute might be.
     * @return string|null The content of the attribute or null, if it doesn't exist.
     */
    private static function get_attribute(string $tag, string $attributename, string $attributecontentregex = '.*') {
        $pattern = "/$attributename=(['\"])($attributecontentregex)\\1/";
        preg_match($pattern, $tag, $matches);
        return $matches[2] ?? null;
    }

    /**
     * Test whether $url matches one of the episodeurls.
     * @param string $url The url to test.
     * @param array $episodeurls array of [ocinstanceid, episoderegex, baseurl].
     * @return array|null [ocinstanceid, episodeid] or null if there are no matches.
     */
    private static function test_url(string $url, array $episodeurls) {
        foreach ($episodeurls as [$ocinstanceid, $episoderegex, $baseurl]) {
            if (preg_match_all($episoderegex, $url, $matches)) {
                return [$ocinstanceid, $matches[1][0]];
            }
        }
        return null;
    }

    /**
     * Replaces Opencast videos embedded in <video> tags by the paella player.
     *
     * @param string $text
     * @param array $options
     * @return array|mixed|string|string[]|null
     */
    public function filter($text, array $options = []) {

        if (preg_match('</(a|video)>', $text) !== 1) {
            // Performance shortcut - if there are no </video> or </a> tags, nothing can match.
            return $text;
        }

        // First section: (Relatively) quick check if there are episode urls in the text, and only look for these later.
        // Improvable by combining all episode urls into one big regex if needed.
        $ocinstances = \tool_opencast\local\settings_api::get_ocinstances();
        $occurrences = [];
        foreach ($ocinstances as $ocinstance) {
            $episodeurls = get_config('filter_opencast', 'episodeurl_' . $ocinstance->id);

            if (!$episodeurls) {
                continue;
            }

            foreach (explode("\n", $episodeurls) as $episodeurl) {
                $episodeurl = trim($episodeurl);

                $urlparts = parse_url($episodeurl);
                if (!isset($urlparts['scheme']) || !isset($urlparts['host'])) {
                    continue;
                }
                $baseurl = $urlparts['scheme'] . '://' . $urlparts['host'];
                if (isset($urlparts['port'])) {
                    $baseurl .= ':' . $urlparts['port'];
                }

                if (self::str_contains($text, $baseurl)) {
                    $episoderegex = "/" . preg_quote($episodeurl, "/") . "/";
                    $episoderegex = preg_replace('/\\\\\[EPISODEID\\\]/', '([0-9a-zA-Z\-]+)', $episoderegex);
                    $occurrences[] = [$ocinstance->id, $episoderegex, $baseurl];
                }
            }
        }

        if (empty($occurrences)) {
            return $text;
        }

        // Second section: splitting the text into tags (and stuff between tags), and search for relevant urls in <a> and <video>.
        $matches = preg_split('/(<[^>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if (!$matches) {
            return $text;
        }

        $i = 0;
        $newtext = '';

        $episode = null;
        $currenttag = null;
        $texttoreplace = '';
        $width = null;
        $height = null;

        // We go through the complete text and transfer it match by match to $newtext.
        // While we are going through interesting tags, $currenttag is set to 'video' or 'a' respectively.
        // During that time, the matches are transferred to $texttoreplace instead. When we find the matching closing tag,
        // ... we add either $texttoreplace to $newtext, or the html for the video player, if we found a matching opencast url.
        foreach ($matches as $match) {
            if ($currenttag) {
                $texttoreplace .= $match;
                if (self::str_starts_with($match, "</$currenttag")) {
                    $replacement = null;
                    if ($episode) {
                        $replacement = $this->render_player($episode[0], $episode[1], $i++, $width, $height);
                    }
                    if ($replacement) {
                        $newtext .= $replacement;
                    } else {
                        $newtext .= $texttoreplace;
                    }
                    $episode = null;
                    $width = null;
                    $height = null;
                    $texttoreplace = null;
                    $currenttag = null;
                } else if (!$episode && $currenttag === 'video' && preg_match('/^<source\s/', $match)) {
                    $src = self::get_attribute($match, 'src');
                    if ($src) {
                        $episode = self::test_url($src, $occurrences);
                    }
                }
            } else {
                if (preg_match('/^<video[>\s]/', $match)) {
                    $currenttag = 'video';
                    $width = self::get_attribute($match, 'width', '[0-9]+');
                    $height = self::get_attribute($match, 'height', '[0-9]+');
                    $src = self::get_attribute($match, 'src');
                    if ($src) {
                        $episode = self::test_url($src, $occurrences);
                    }
                } else if (preg_match('/^<a\s/', $match)) {
                    $src = self::get_attribute($match, 'href');
                    if ($src) {
                        $episode = self::test_url($src, $occurrences);
                        // Only set currenttag if there is a recognized url,
                        // ... so that nested <a> or <video> tags can be matched otherwise.
                        if ($episode) {
                            $currenttag = 'a';
                        }
                    }
                }
                if ($currenttag) {
                    $texttoreplace .= $match;
                } else {
                    $newtext .= $match;
                }
            }
        }

        return $newtext;
    }

    /**
     * Render HTML for embedding video player.
     * @param int $ocinstanceid Id of ocinstance.
     * @param string $episodeid Id opencast episode.
     * @param int $playerid Unique id to assign to player element.
     * @param int|null $width Optionally width for player.
     * @param int|null $height Optionally height for player.
     * @return string|null
     */
    protected function render_player(int $ocinstanceid, string $episodeid, int $playerid,
            $width = null, $height = null): string|null {
        global $OUTPUT, $PAGE;

        $data = paella_transform::get_paella_data_json($ocinstanceid, $episodeid);

        if (!$data) {
            return null;
        }

        // Collect the needed data being submitted to the template.
        $mustachedata = new stdClass();
        $mustachedata->playerid = 'ocplayer_' . $playerid;
        $mustachedata->configurl =
                (new moodle_url(get_config('filter_opencast', 'configurl_' . $ocinstanceid)))->out(false);
        $mustachedata->themeurl =
                (new moodle_url(get_config('mod_opencast', 'themeurl_' . $ocinstanceid)))->out(false);

        $mustachedata->data = json_encode($data);
        $mustachedata->width = $width;
        $mustachedata->height = $height;
        $mustachedata->modplayerpath = (new moodle_url('/mod/opencast/player.html'))->out(false);

        if (isset($data['streams'])) {
            if (count($data['streams']) === 1) {
                $sources = $data['streams'][0]['sources'];
                $res = $sources[array_key_first($sources)][0]['res'];
                $resolution = $res['w'] . '/' . $res['h'];
                $mustachedata->resolution = $resolution;

                if ($width xor $height) {
                    if ($width) {
                        $mustachedata->height = $width * ($res['h'] / $res['w']);
                    } else if ($height) {
                        $mustachedata->width = $height * ($res['w'] / $res['h']);
                    }
                }
            }
            $renderer = $PAGE->get_renderer('filter_opencast');
            return $renderer->render_player($mustachedata);
        } else {
            return $OUTPUT->render(new \core\output\notification(
                    get_string('erroremptystreamsources', 'mod_opencast'),
                    \core\output\notification::NOTIFY_ERROR
            ));
        }
    }

    /**
     * Polyfill for str_contains for PHP 7.
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private static function str_contains(string $haystack, string $needle): bool {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * Polyfill for str_starts_with for PHP 7.
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private static function str_starts_with(string $haystack, string $needle): bool {
        return strpos($haystack, $needle) === 0;
    }
}
