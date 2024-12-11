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

use filter_opencast\local\lti_helper;

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


        $hasconfiguredlti = lti_helper::is_lti_credentials_configured($instance->id);
        // Providing use lti option, when when the consumer key and secret are configured in tool_opencast.
        if ($hasconfiguredlti) {
            $settings->add(new admin_setting_configcheckbox('filter_opencast/uselti_' . $instance->id,
                new lang_string('setting_uselti', 'filter_opencast'),
                new lang_string('setting_uselti_desc', 'filter_opencast'), 0));
        } else {
            // Otherwise, we will inform the admin about this setting with extra info to configure this if needed.
            $path = '/admin/settings.php?section=tool_opencast_configuration';
            if (count($ocinstances) > 1) {
                $path .= '_' . $instance->id;
            }
            $toolopencasturl = new moodle_url($path);
            $ocinstancename = $instance->name ?? $instance->id;
            $link = html_writer::link($toolopencasturl,
                get_string('setting_uselti_ocinstance_name', 'filter_opencast', $ocinstancename), ['target' => '_blank']);
            $description = get_string('setting_uselti_nolti_desc', 'filter_opencast', $link);
            $settings->add(
                new admin_setting_configempty('block_opencast/uselti_' . $instance->id,
                    get_string('setting_uselti', 'filter_opencast'),
                    $description));
        }
    }
}
