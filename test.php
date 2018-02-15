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
 * Opencast filter settings
 *
 * @package    filter_opencastfilter
 * @copyright  2017 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');

// TODO get issuerid
$issuerid = get_config('filter_opencast', 'issuerid');
$issuer = \core\oauth2\api::get_issuer($issuerid);
// Get an OAuth client from the issuer.
// TODO return url
$returnurl = new moodle_url('/filter/opencastfilter/adminsettings.php');
$client = \core\oauth2\api::get_user_oauth_client($issuer, $returnurl);

$service = new \filter_opencast\rest($client);
$me = $service->call('me', []);
file_put_contents('info/me.json', $me);

$id = '607e3bc5-f134-4ca2-87fd-b87a76ea3a65';
$params = ['id' => $id];
$episode = $service->call('episode', $params);
file_put_contents('info/episode.json', $episode);

$params = ['id' => '6103ef9b-d699-435d-9e69-4676afd30c87'];
$footprint = $service->call('footprint', $params);
file_put_contents('info/footprint.json', $footprint);
