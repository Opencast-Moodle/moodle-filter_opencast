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

require_once($CFG->dirroot.'/mod/lti/locallib.php');

/**
 * Opencast library functions.
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2017 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function filter_opencast_load_meInfo() {
    global $CFG, $PAGE;

    $urlparts = parse_url($CFG->wwwroot);
    $orgid = $urlparts['host'];

    $endpoint = 'http://localhost:8080/lti';

    $instance = new stdClass();
    $instance->course = 2;
    $instance->typeid = 2;
    $instance->launchcontainer = 1;
    $instance->id = 101;
    $instance->name = 'Opencast';
    $instance->servicesalt = '5a685957417926.81276553';

    $typeid = 2;
    $tool = new stdClass();
    $tool->baseurl = 'http://localhost:8080/lti';
    $tool->tooldomain = 'localhost:8080';
    $tool->state = 1;
    $tool->course = 2;
    $tool->coursevisible = 1;

    $typeconfig = array();
    $typeconfig['resourcekey'] = 'myconsumerkey';
    $typeconfig['password'] = 'myconsumersecret';
    $typeconfig['sendname'] = 1;
    $typeconfig['sendemailaddr'] = 1;
    $typeconfig['acceptgrades'] = 1;

    $urlparts = parse_url($CFG->wwwroot);
    $typeconfig['organizationid'] = $urlparts['host'];

    $key = $typeconfig['resourcekey'];
    $secret = $typeconfig['password'];
    $endpoint = $tool->baseurl;
    $orgid = $typeconfig['organizationid'];
    $course = $PAGE->course;
    $islti2 = isset($tool->toolproxyid);

    $requestparams = lti_build_request($instance, $typeconfig, $course);
    $requestparams = array_merge($requestparams, lti_build_standard_request($instance, $orgid, $islti2));
    $launchcontainer = lti_get_launch_container($instance, $typeconfig);

    $target = '';
    switch($launchcontainer) {
        case LTI_LAUNCH_CONTAINER_EMBED:
        case LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS:
            $target = 'iframe';
            break;
        case LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW:
            $target = 'frame';
            break;
        case LTI_LAUNCH_CONTAINER_WINDOW:
            $target = 'window';
            break;
    }
    if (!empty($target)) {
        $requestparams['launch_presentation_document_target'] = $target;
    }

    $requestparams['launch_presentation_return_url'] = $PAGE->url->out();
    $parms = lti_sign_parameters($requestparams, $endpoint, "POST", $key, $secret);

    $content = "<form action=\"" . $endpoint .
        "\" name=\"ltiLaunchForm\" id=\"ltiLaunchForm\" method=\"post\" encType=\"application/x-www-form-urlencoded\">\n";

    // Contruct html for the launch parameters.
    foreach ($parms as $key => $value) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        $content .= "<input type=\"hidden\" name=\"{$key}\"";
        $content .= " value=\"";
        $content .= $value;
        $content .= "\"/>\n";
    }
    $content .= "</form>\n";

    echo $content;
    $PAGE->requires->js_call_amd('filter_opencast/form','init');
}

function filter_opencast_load_episode($service, $id) {
    global $CFG;
    // Get episode.json
    $params = ['id' => $id];
    $episode = $service->call('episode', $params);
    file_put_contents($CFG->dirroot . '\filter\opencast\info\episode.json', $episode);
}