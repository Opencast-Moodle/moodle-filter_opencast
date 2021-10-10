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
 * Contains Functions to transform the opencast /api/events/{id} response into a data.json as accepted by paella player.
 * @package    filter_opencast
 * @copyright  2021 Justus Dieckmann WWU, Tamara Gunkel WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_opencast\local;


defined('MOODLE_INTERNAL') || die();

/**
 * Helper for preparing the data from the Opencast API for the paella player.
 * @package filter_opencast
 * @copyright  2021 Justus Dieckmann WWU, Tamara Gunkel WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class paella_transform
{

    /**
     * Returns the publication with the correct release channel for a given episode.
     * @param string $episode Episode id
     * @return false|mixed Publication or false if no publication for the configured channel exists.
     * @throws \dml_exception
     */
    private static function get_api_publication($ocinstanceid, $episode) {
        $channel = get_config('mod_opencast', 'channel_' . $ocinstanceid);
        foreach ($episode->publications as $publication) {
            if ($publication->channel == $channel) {
                return $publication;
            }
        }
        return false;
    }

    /**
     * Returns the preview image for a publication.
     * @param string $publication Publication id
     * @return mixed|null Url to preview image or null if not existing
     */
    private static function get_preview_image($publication) {
        $presenterpreview = null;
        $presentationpreview = null;
        $otherpreview = null;

        foreach ($publication->attachments as $attachment) {
            if ($attachment->flavor === 'presenter/player+preview') {
                $presenterpreview = $attachment->url;
            } else if ($attachment->flavor === 'presentation/player+preview') {
                $presentationpreview = $attachment->url;
            } else if (substr($attachment->flavor, -15) === '/player+preview') {
                $otherpreview = $attachment->url;
            }
        }

        return $presentationpreview ?? $presenterpreview ?? $otherpreview;
    }

    /**
     * Returns the duration of a publication.
     * @param string $publication Publication id
     * @return float|int duration in seconds
     */
    private static function get_duration($publication) {
        $duration = 0;

        foreach ($publication->media as $media) {
            if ($media->duration > $duration) {
                $duration = $media->duration;
            }
        }
        return $duration / 1000;
    }

    /**
     * Returns the frames of a publication.
     * @param string $publication Publication id
     * @return array of frames
     */
    private static function get_frame_list($publication) {
        $framelist = [];

        foreach ($publication->attachments as $attachment) {
            if ($attachment->flavor === 'presentation/segment+preview' ||
                $attachment->flavor === 'presentation/segment+preview+hires') {
                if (preg_match('/time=T(\d+):(\d+):(\d+)/', $attachment->ref, $matches)) {
                    $time = intval($matches[1]) * 60 * 60 + intval($matches[2]) * 60 + intval($matches[3]);
                    if (!array_key_exists($time, $framelist)) {
                        $framelist[$time] = [
                            'id' => 'frame_' . $time,
                            'mimetype' => $attachment->mediatype,
                            'time' => $time,
                            'url' => $attachment->url,
                            'thumb' => $attachment->url
                        ];
                    } else {
                        if (substr($attachment->flavor, -5) === 'hires') {
                            $framelist[$time]->url = $attachment->url;
                        } else {
                            $framelist[$time]->thumb = $attachment->url;
                        }

                    }
                }
            }
        }
        return $framelist;
    }

    private static function get_source_type_from_track($track) {
        $protocol = parse_url($track->url);
        $sourceType = null;

        if ($protocol && $protocol['scheme']) {
            switch ($protocol['scheme']) {
                case 'rtmp':
                case 'rtmps':
                    if (in_array($track, ['video/mp4', 'video/ogg', 'video/webm', 'video/x-flv'])) {
                        $sourceType = 'rtmp';
                    }
                    break;
                case 'http':
                case 'https':
                    switch ($track->mediatype) {
                        case 'video/mp4':
                        case 'video/ogg':
                        case 'video/webm':
                            list($type, $sourceType) = explode('/', $track->mediatype, 2);
                            break;
                        case 'video/x-flv':
                            $sourceType = 'flv';
                            break;
                        case 'application/x-mpegURL':
                            $sourceType = 'hls';
                            break;
                        case 'application/dash+xml':
                            $sourceType = 'mpd';
                            break;
                        case 'audio/m4a':
                            $sourceType = 'audio';
                            break;
                    }
                    break;
            }
        }

        return $sourceType;
    }

    /**
     * Creates the streams for a publication.
     * @param string $publication Publication id
     * @return array of streams
     */
    private static function get_streams($publication) {
        $streams = [];

        foreach ($publication->media as $media) {
            $sourceType = self::get_source_type_from_track($media);
            $content = explode('/', $media->flavor, 2)[0];
            if (!array_key_exists($content, $streams)) {
                $streams[$content] = [
                    'sources' => [],
                    'content' => $content,
                    'type' => 'video'
                ];
                $hasAdaptiveMasterTrack[$content] = false;
            }

            $ismaster = false;
            if(isset($media->is_master_playlist) && $media->is_master_playlist) {
                $hasAdaptiveMasterTrack[$content] = true;
                $ismaster = true;
            }

            if( $sourceType == 'hls' && !$ismaster) {
                continue;
            }

            if (!array_key_exists($sourceType, $streams[$content]['sources'])) {
                $streams[$content]['sources'][$sourceType] = [];
            }

            $streams[$content]['sources'][$sourceType][] = [
                'src' => $media->url,
                'mimetype' => $media->mediatype,
                'res' => [
                    'w' => isset($media->width) ? $media->width : 0,
                    'h' => isset($media->height) ? $media->height : 0
                ],
                'master' => $ismaster,
                'isLiveStream' => isset($media->is_live) && $media->is_live
            ];
        }

        return array_values($streams);
    }

    /**
     * Returns the captions of a publication.
     * @param string $publication Publication id
     * @return array of captions
     */
    private static function get_captions($publication) {
        $captions = [];
        foreach ($publication->attachments as $attachment) {
            list($type1, $type2) = explode('/', $attachment->flavor, 2);
            if ($type1 === 'captions') {
                list($format, $lang) = explode('+', $type2, 2);
                $captions[] = [
                    'lang' => $lang,
                    'text' => $lang,
                    'format' => $format,
                    'url' => $attachment->url
                ];
            }
        }
        return $captions;
    }

    /**
     * Returns the video data from Opencast in the format for the paella player.
     * @param string $episodeid Opencast episode id
     * @param string|null $seriesid Opencast series id
     * @return array|false Video data or false if data could not be retrieved
     * @throws \dml_exception
     */
    public static function get_paella_data_json($ocinstanceid, $episodeid, $seriesid = null) {
        $api = apibridge::get_instance($ocinstanceid);
        if (($episode = $api->get_episode($episodeid, $seriesid)) === false) {
            return false;
        }

        if (($publication = self::get_api_publication($ocinstanceid, $episode)) === false) {
            return false;
        }

        return [
            'metadata' => [
                'title' => $episode->title,
                'duration' => self::get_duration($publication),
                'preview' => self::get_preview_image($publication)
            ],
            'streams' => self::get_streams($publication),
            'frameList' => self::get_frame_list($publication),
            'captions' => self::get_captions($publication)
        ];
    }
}