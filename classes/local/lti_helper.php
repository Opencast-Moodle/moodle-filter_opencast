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
 * LTI helper class for filter opencast.
 * @package    filter_opencast
 * @copyright  2024 Farbod Zamani Boroujeni, ELAN e.V.
 * @author     Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_opencast\local;

use oauth_helper;
use tool_opencast\exception\opencast_api_response_exception;
use tool_opencast\local\settings_api;
use tool_opencast\local\api;
use moodle_exception;

/**
 * LTI helper class for filter opencast.
 * @package    filter_opencast
 * @copyright  2024 Farbod Zamani Boroujeni, ELAN e.V.
 * @author     Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lti_helper {

    /**  */
    const LTI_LAUNCH_PATH = '/filter/opencast/ltilaunch.php';

    /**
     * Create necessary lti parameters.
     * @param string $consumerkey LTI consumer key.
     * @param string $consumersecret LTI consumer secret.
     * @param string $endpoint of the opencast instance.
     * @param string $customtool the custom tool
     * @param int $courseid the course id to add into the parameters
     *
     * @return array lti parameters
     */
    public static function create_lti_parameters($consumerkey, $consumersecret, $endpoint, $customtool, $courseid) {
        global $CFG, $USER;

        $course = get_course($courseid);

        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        require_once($CFG->dirroot . '/lib/oauthlib.php');

        $helper = new oauth_helper(['oauth_consumer_key' => $consumerkey,
            'oauth_consumer_secret' => $consumersecret, ]);

        // Set all necessary parameters.
        $params = [];
        $params['oauth_version'] = '1.0';
        $params['oauth_nonce'] = $helper->get_nonce();
        $params['oauth_timestamp'] = $helper->get_timestamp();
        $params['oauth_consumer_key'] = $consumerkey;

        $params['context_id'] = $course->id;
        $params['context_label'] = trim($course->shortname);
        $params['context_title'] = trim($course->fullname);
        $params['resource_link_id'] = 'o' . random_int(1000, 9999) . '-' . random_int(1000, 9999);
        $params['resource_link_title'] = 'Opencast';
        $params['context_type'] = ($course->format == 'site') ? 'Group' : 'CourseSection';
        $params['launch_presentation_locale'] = current_language();
        $params['ext_lms'] = 'moodle-2';
        $params['tool_consumer_info_product_family_code'] = 'moodle';
        $params['tool_consumer_info_version'] = strval($CFG->version);
        $params['oauth_callback'] = 'about:blank';
        $params['lti_version'] = 'LTI-1p0';
        $params['lti_message_type'] = 'basic-lti-launch-request';
        $urlparts = parse_url($CFG->wwwroot);
        $params['tool_consumer_instance_guid'] = $urlparts['host'];
        $params['custom_tool'] = urlencode($customtool);

        // User data.
        $params['user_id'] = $USER->id;
        $params['lis_person_name_given'] = $USER->firstname;
        $params['lis_person_name_family'] = $USER->lastname;
        $params['lis_person_name_full'] = $USER->firstname . ' ' . $USER->lastname;
        $params['ext_user_username'] = $USER->username;
        $params['lis_person_contact_email_primary'] = $USER->email;
        $params['roles'] = lti_get_ims_role($USER, null, $course->id, false);

        if (!empty($CFG->mod_lti_institution_name)) {
            $params['tool_consumer_instance_name'] = trim(html_to_text($CFG->mod_lti_institution_name, 0));
        } else {
            $params['tool_consumer_instance_name'] = get_site()->shortname;
        }

        $params['launch_presentation_document_target'] = 'iframe';
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_signature'] = $helper->sign("POST", $endpoint, $params, $consumersecret . '&');
        return $params;
    }

    /**
     * Retrieves the LTI consumer key and consumer secret for a given Opencast instance ID.
     *
     * @param int $ocinstanceid The ID of the Opencast instance.
     *
     * @return array An associative array containing the 'consumerkey' and 'consumersecret' for the given Opencast instance.
     *               If the credentials are not found, an empty array is returned.
     */
    public static function get_lti_credentials(int $ocinstanceid) {
        $lticonsumerkey = settings_api::get_lticonsumerkey($ocinstanceid);
        $lticonsumersecret = settings_api::get_lticonsumersecret($ocinstanceid);
        return ['consumerkey' => $lticonsumerkey, 'consumersecret' => $lticonsumersecret];
    }

    /**
     * Checks if LTI credentials are configured for a given Opencast instance.
     *
     * This function verifies whether both the LTI consumer key and consumer secret
     * are set for the specified Opencast instance.
     *
     * @param int $ocinstanceid The ID of the Opencast instance to check.
     *
     * @return bool Returns true if both LTI consumer key and secret are configured,
     *              false otherwise.
     */
    public static function is_lti_credentials_configured(int $ocinstanceid) {
        $lticredentials = self::get_lti_credentials($ocinstanceid);
        return !empty($lticredentials['consumerkey']) && !empty($lticredentials['consumersecret']);
    }

    /**
     * Retrieves an object containing LTI settings for a given Opencast instance.
     *
     * This function gathers the LTI credentials and API URL for the specified Opencast instance
     * and returns them as a structured object.
     *
     * @param int $ocinstanceid The ID of the Opencast instance for which to retrieve LTI settings.
     *
     * @return object An object containing the following properties:
     *                - ocinstanceid: The ID of the Opencast instance.
     *                - consumerkey: The LTI consumer key for the instance.
     *                - consumersecret: The LTI consumer secret for the instance.
     *                - baseurl: The API URL for the presentation node of Opencast instance.
     */
    public static function get_lti_set_object(int $ocinstanceid) {
        $lticredentials = self::get_lti_credentials($ocinstanceid);
        // Get url of the engage.ui.
        $baseurl = self::get_engage_url($ocinstanceid);

        return (object) [
            'ocinstanceid' => $ocinstanceid,
            'consumerkey' => $lticredentials['consumerkey'],
            'consumersecret' => $lticredentials['consumersecret'],
            'baseurl' => $baseurl,
        ];
    }

    /**
     * Generates the LTI launch URL for the Opencast filter.
     *
     * This function creates a URL for launching LTI content specific to the Opencast filter,
     * incorporating necessary parameters such as course ID, Opencast instance ID, and episode ID.
     *
     * @param int $ocinstanceid The ID of the Opencast instance.
     * @param int $courseid The ID of the course.
     * @param string $episodeid The ID of the Opencast episode.
     * @param bool $output Optional. If true, returns the URL as a string. If false, returns a moodle_url object. Default is true.
     *
     * @return string|moodle_url If $output is true, returns the LTI launch URL as a string.
     *                           If $output is false, returns a moodle_url object representing the LTI launch URL.
     */
    public static function get_filter_lti_launch_url(int $ocinstanceid, int $courseid, string $episodeid, bool $output = true) {
        $params = [
            'courseid' => $courseid,
            'ocinstanceid' => $ocinstanceid,
            'episodeid' => $episodeid,
            'sesskey' => sesskey(),
        ];
        $ltilaunchurl = new \moodle_url(self::LTI_LAUNCH_PATH, $params);
        if ($output) {
            return $ltilaunchurl->out(false);
        }
        return $ltilaunchurl;
    }


    /**
     * Retrieves the engage URL for a given Opencast instance.
     *
     * This function attempts to get the engage URL for the specified Opencast instance.
     * It first tries to fetch the URL from the Opencast API. If that fails, it falls back
     * to using the API URL as the engage URL.
     *
     * @param int $ocinstanceid The ID of the Opencast instance.
     *
     * @return string The engage URL for the Opencast instance.
     *
     * @throws opencast_api_response_exception If the API request fails.
     */
    public static function get_engage_url(int $ocinstanceid) {
        $api = api::get_instance($ocinstanceid);

        // As a default fallback, we assume that the engage node url is the same as the api url.
        $engageurl = settings_api::get_apiurl($ocinstanceid);

        // Try to get the engage url from engage ui url once more, as secondary fallback method.
        $response = $api->opencastapi->baseApi->getOrgEngageUIUrl();
        $code = $response['code'];
        // If something went wrong, we throw opencast_api_response_exception exception.
        if ($code != 200) {
            throw new opencast_api_response_exception($response);
        }

        // Get the engage ui object from the get call.
        $engageuiobj = (array) $response['body'];

        // Check if we have a valid engage ui url.
        if (isset($engageuiobj['org.opencastproject.engage.ui.url'])) {
            $engageuiurl = $engageuiobj['org.opencastproject.engage.ui.url'];

            // Check if the engage ui url is not empty and not a localhost url.
            if (!empty($engageuiurl) &&
                strpos($engageuiurl, 'http://') === false &&
                strpos($engageuiurl, 'localhost') === false ) {
                $engageurl = $engageuiurl;
            }
        }

        // Finally, we return it.
        return $engageurl;
    }
}
