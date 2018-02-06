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
 * Settings.
 *
 * @package    block_opencast
 * @copyright  2017 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $issuers = \core\oauth2\api::get_all_issuers();
    $choices = array();
    foreach($issuers as $issuer){
        $choices[$issuer->get('id')] = $issuer->get('name');
    }

    $settings->add(new admin_setting_configselect('filter_opencast/issuerid', get_string('setting_issuer', 'filter_opencast'),
        get_string('setting_issuer_desc', 'filter_opencast'), 0, $choices));
    $settings->add(new admin_setting_configtext('filter_opencast/baseurlapi', get_string('setting_baseurlapi', 'filter_opencast'),
        get_string('setting_baseurlapi_desc', 'filter_opencast'), ''));
}
