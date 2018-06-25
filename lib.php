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
 * Opencast library functions.
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2018 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->dirroot . '/lib/oauthlib.php');

/**
 * Use lti to login and retrieve cookie from opencast.
 */
function filter_opencast_login() {
	global $PAGE;

	// Get url of opencast engage server.
	$endpoint = get_config('filter_opencast', 'engageurl');
	if (strpos($endpoint, 'http') !== 0) {
		$endpoint = 'http://' . $endpoint;
	}
	$endpoint .= '/lti';

	// Create parameters.
	$params = filter_opencast_create_parameters($endpoint);

	// Render form.
	$renderer = $PAGE->get_renderer('filter_opencast');
	echo $renderer->render_lti_form($endpoint, $params);

	// Submit form.
	$PAGE->requires->js_call_amd('filter_opencast/form', 'init');
}

/**
 * Create necessary lti parameters.
 * @param $endpoint of the opencast instance.
 *
 * @return array lti parameters
 */
function filter_opencast_create_parameters($endpoint) {
	global $CFG, $COURSE, $USER;

	// Get consumerkey and consumersecret.
	$consumerkey = get_config('filter_opencast', 'consumerkey');
	$consumersecret = get_config('filter_opencast', 'consumersecret');

	$helper = new oauth_helper(array('oauth_consumer_key'    => $consumerkey,
	                                 'oauth_consumer_secret' => $consumersecret));

	// Set all necessary parameters.
	$params = array();
	$params['oauth_version'] = '1.0';
	$params['oauth_nonce'] = $helper->get_nonce();
	$params['oauth_timestamp'] = $helper->get_timestamp();
	$params['oauth_consumer_key'] = $consumerkey;
	$params['oauth_callback'] = 'about:blank';

	$params['context_id'] = $COURSE->id;
	$params['resource_link_id'] = 'o' . random_int(1000, 9999) . '-' . random_int(1000, 9999);
	$params['context_type'] = ($COURSE->format == 'site') ? 'Group' : 'CourseSection';
	$params['lti_version'] = 'LTI-1p0';
	$params['lti_message_type'] = 'basic-lti-launch-request';
	$urlparts = parse_url($CFG->wwwroot);
	$params['tool_consumer_instance_guid'] = $urlparts['host'];

	// User data.
	$params['user_id'] = $USER->id;
	$params['ext_user_username'] = $USER->username;
	$params['roles'] = lti_get_ims_role($USER, null, $COURSE->id, false);

	$params['launch_presentation_document_target'] = 'iframe';
	$params['oauth_signature_method'] = 'HMAC-SHA1';
	$params['oauth_signature'] = $helper->sign("POST", $endpoint, $params, $consumersecret . '&');

	return $params;
}