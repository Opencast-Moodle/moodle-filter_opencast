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
 * LTI Launch page.
 * Designed to be called as a link in an iframe to prepare the lti launch data and perform the launch.
 *
 * @package    filter_opencast
 * @copyright  2024 Farbod Zamani Boroujeni, ELAN e.V.
 * @author     Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use filter_opencast\local\lti_helper;
use html_writer;

require(__DIR__ . '/../../config.php');

global $PAGE;

$courseid = required_param('courseid', PARAM_INT);
$ocinstanceid = required_param('ocinstanceid', PARAM_INT);
$episodeid = required_param('episodeid', PARAM_ALPHANUMEXT);

$baseurl = lti_helper::get_filter_lti_launch_url($ocinstanceid, $courseid, $episodeid, false);

$PAGE->set_pagelayout('embedded');
$PAGE->set_url($baseurl);

require_login($courseid, false);

if (confirm_sesskey()) {
    $ltisetobject = lti_helper::get_lti_set_object($ocinstanceid);
    $customtool = "/play/{$episodeid}";
    $endpoint = rtrim($ltisetobject->baseurl, '/') . '/lti';
    $ltiparams = lti_helper::create_lti_parameters(
        $ltisetobject->consumerkey,
        $ltisetobject->consumersecret,
        $endpoint,
        $customtool,
        $courseid
    );
    $formid = "ltiLaunchForm-{$episodeid}";
    $formattributed = [
        'action' => $endpoint,
        'method' => 'post',
        'id' => $formid,
        'name' => $formid,
        'encType' => 'application/x-www-form-urlencoded'
    ];
    echo html_writer::start_tag('form', $formattributed);

    foreach ($ltiparams as $name => $value) {
        $attributes = ['type' => 'hidden', 'name' => htmlspecialchars($name), 'value' => htmlspecialchars($value)];
        echo html_writer::empty_tag('input', $attributes) . "\n";
    }

    echo html_writer::end_tag('form');

    echo html_writer::script(
        "window.onload = function() {
            document.getElementById('{$formid}').submit();
        };"
    );

    exit();
}

throw new \moodle_exception('ltilaunch_failed', 'filter_opencast', $baseurl);
