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
 * Strings for component 'filter_opencastfilter', language 'en'
 *
 * @package   filter_opencast
 * @copyright 2017 Tamara Gunkel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$string['filtername'] = 'Opencast';
$string['pluginname'] = 'Opencast Filter';
$string['privacy:metadata'] = 'The Opencast filter plugin does not store any personal data.';
$string['setting_configurl'] = 'URL to Paella config.json';
$string['setting_configurl_desc'] = 'URL of the config.json used by Paella Player. Can either be a absolute URL or a URL relative to the wwwroot.';
$string['setting_episodeurl'] = 'URL templates for filtering';
$string['setting_episodeurl_desc'] = 'URLs matching this template are replaced with the Opencast player. You must use the placeholder [EPISODEID] to indicate where the episode ID is contained in the URL e.g. http://stable.opencast.de/play/[EPISODEID]. If you want to filter for multiple URLs, enter each URL in a new line.';
