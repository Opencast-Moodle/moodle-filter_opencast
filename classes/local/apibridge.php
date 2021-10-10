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
 * API-bridge for filter_opencast. Contains all the functions which use the external API.
 *
 * @package    filter_opencast
 * @copyright  2020 Justus Dieckmann WWU, 2021 Tamara Gunkel WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_opencast\local;

use tool_opencast\local\api;

defined('MOODLE_INTERNAL') || die();

/**
 * API-bridge for filter_opencast. Contains all the functions which use the external API.
 *
 * @package    filter_opencast
 * @copyright  2020 Justus Dieckmann WWU, 2021 Tamara Gunkel  WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class apibridge {

    private $ocinstanceid;

    /**
     * apibridge constructor.
     */
    private function __construct($ocinstanceid) {
        $this->ocinstanceid = $ocinstanceid;
    }

    /**
     * Get an instance of an object of this class. Create as a singleton.
     *
     * @param boolean $forcenewinstance true, when a new instance should be created.
     *
     * @return apibridge
     */
    public static function get_instance($ocinstanceid, $forcenewinstance = false) {
        static $apibridge;

        if (isset($apibridge) && !$forcenewinstance) {
            $apibridge->ocinstanceid = $ocinstanceid;
            return $apibridge;
        }

        $apibridge = new apibridge($ocinstanceid);

        return $apibridge;
    }

    /**
     * Get all events in the specified series.
     * @param string $seriesid
     * @return false|mixed
     */
    public function get_episodes_in_series($seriesid) {
        $api = new api($this->ocinstanceid);
        $resource = "/api/events?filter=is_part_of:$seriesid&withpublications=true&sort=start_date:DESC,title:ASC";
        $response = $api->oc_get($resource);

        if ($api->get_http_code() != 200) {
            return false;
        }

        $response = json_decode($response);
        if ($response === null) {
            return false;
        }

        return $response;
    }

    /**
     * Gets the information about the given series.
     * @param string $seriesid
     * @return false|mixed
     */
    public function get_series($seriesid) {
        $api = new api($this->ocinstanceid);
        $resource = "/api/series/$seriesid";
        $response = $api->oc_get($resource);

        if ($api->get_http_code() != 200) {
            return false;
        }

        $response = json_decode($response);
        if ($response === null) {
            return false;
        }
        return $response;
    }

    /**
     * Gets the information about the given Episode.
     * @param string $episodeid
     * @param null|string $ensureseries If not null, will return false if the episode is not part of the series.
     * @return false|mixed
     */
    public function get_episode($episodeid, $ensureseries = null) {
        $api = new api($this->ocinstanceid);
        $resource = "/api/events/$episodeid?sign=true&withpublications=true";
        $response = $api->oc_get($resource);

        if ($api->get_http_code() != 200) {
            return false;
        }

        $response = json_decode($response);
        if ($response === null) {
            return false;
        }

        if ($ensureseries) {
            if ($response->is_part_of !== $ensureseries) {
                return false;
            }
        }
        return $response;
    }

    /**
     * Finds out, if a opencastid specifies an episode, a series, or nothing.
     * @param string $id opencastid
     * @return int the type {@see opencasttype}
     */
    public function find_opencast_type_for_id($id) {
        $api = new api($this->ocinstanceid);
        $api->oc_get("/api/events/$id");
        if ($api->get_http_code() == 200) {
            return opencasttype::EPISODE;
        }

        $api->oc_get("/api/series/$id");
        if ($api->get_http_code() == 200) {
            return opencasttype::SERIES;
        }

        return opencasttype::UNDEFINED;
    }
}
