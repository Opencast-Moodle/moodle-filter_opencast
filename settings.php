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
 * Settings for opencast filter.
 *
 * @package    filter_opencast
 * @copyright  2018 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $ocinstances = \tool_opencast\local\settings_api::get_ocinstances();

    foreach ($ocinstances as $instance) {
        $settings->add(new admin_setting_configtextarea('filter_opencast/episodeurl_' . $instance->id,
            get_string('setting_episodeurl', 'filter_opencast'),
            get_string('setting_episodeurl_desc', 'filter_opencast'), '', PARAM_RAW_TRIMMED, '30', '4'));

        $settings->add(new admin_setting_configtext('filter_opencast/configurl_' . $instance->id,
            new lang_string('setting_configurl', 'filter_opencast'),
            new lang_string('setting_configurl_desc', 'filter_opencast'), '/filter/opencast/config.json'));
    }
}
