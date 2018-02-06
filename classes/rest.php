<?php
namespace filter_opencast;

defined('MOODLE_INTERNAL') || die();

class rest extends \core\oauth2\rest {
    /**
     * Define the functions of the rest API.
     *
     * @return array Example:
     *  [ 'listFiles' => [ 'method' => 'get', 'endpoint' => 'http://...', 'args' => [ 'folder' => PARAM_STRING ] ] ]
     */
    public function get_api_functions() {
        $baseurl = get_config('filter_opencast', 'baseurlapi');

        return [
            'me' => [
                'endpoint' => $baseurl . '/info/me.json',
                'method' => 'get',
                'args' => [],
                'response' => 'raw'
            ],
            'episode' => [
                'endpoint' => $baseurl . '/search/episode.json',
                'method' => 'get',
                'args' => [
                    'id' => PARAM_RAW
                ],
                'response' => 'raw'
            ],
            'footprint' => [
                'endpoint' => $baseurl . '/usertracking/footprint.json',
                'method' => 'get',
                'args' => [
                    'id' => PARAM_RAW
                ],
                'response' => 'raw'
            ],
        ];
    }
}