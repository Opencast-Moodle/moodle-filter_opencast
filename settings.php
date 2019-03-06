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
	// Lti settings.
    $settings->add(new admin_setting_configtext('filter_opencast/consumerkey',
        get_string('setting_consumerkey', 'filter_opencast'),
        get_string('setting_consumerkey_desc', 'filter_opencast'), ''));
    
    $settings->add(new admin_setting_configpasswordunmask('filter_opencast/consumersecret',
        get_string('setting_consumersecret', 'filter_opencast'),
        get_string('setting_consumersecret_desc', 'filter_opencast'), ''));
    
    // Opencast settings.
    $settings->add(new admin_setting_configtext('filter_opencast/engageurl',
        get_string('setting_engageurl', 'filter_opencast'),
        get_string('setting_engageurl_desc', 'filter_opencast'), ''));
    
    $settings->add(new admin_setting_configtext('filter_opencast/playerurl',
        get_string('setting_playerurl', 'filter_opencast'),
        get_string('setting_playerurl_desc', 'filter_opencast'), ''));
    
    $options = array('' => get_string('no'), 'allowfullscreen' => get_string('yes'));
    $settings->add(new admin_setting_configselect('filter_opencast/allowfullscreen',
    	get_string('setting_allowfullscreen', 'filter_opencast'),
    	get_string('setting_allowfullscreen_desc', 'filter_opencast'),
    	'allowfullscreen', $options));
    
    $settings->add(new admin_setting_configtext('filter_opencast/defaultwidth',
    	get_string('setting_defaultwidth', 'filter_opencast'),
    	get_string('setting_defaultwidth_desc', 'filter_opencast'), '95%'));
    
    $settings->add(new admin_setting_configtext('filter_opencast/defaultheight',
    	get_string('setting_defaultheight', 'filter_opencast'),
    	get_string('setting_defaultheight_desc', 'filter_opencast'), '455px'));
}
