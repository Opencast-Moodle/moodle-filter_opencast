/**
 * Licensed to The Apereo Foundation under one or more contributor license
 * agreements. See the NOTICE file distributed with this work for additional
 * information regarding copyright ownership.
 *
 *
 * The Apereo Foundation licenses this file to you under the Educational
 * Community License, Version 2.0 (the "License"); you may not use this file
 * except in compliance with the License. You may obtain a copy of the License
 * at:
 *
 *   http://opensource.org/licenses/ecl2.txt
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 */
/*jslint browser: true, nomen: true*/
/*global define, CustomEvent*/
define(["backbone", "engage/core"], function(Backbone, Engage) {
    "use strict";

    var events = {
        mediaPackageModelInternalError: new Engage.Event("MhConnection:mediaPackageModelInternalError", "A mediapackage model error occured", "trigger"),
        mediaPackageLoaded: new Engage.Event("MhConnection:mediaPackageLoaded", "A mediapackage has been loaded", "trigger")
    };

    var mediaPackageID = Engage.model.get("urlParameters").id;
    if (!mediaPackageID) {
        mediaPackageID = "";
    }

    var MediaPackageModel = Backbone.Model.extend({
        urlRoot: "",
        initialize: function() {
            Engage.log("MhConnection: Init MediaPackage model");
            this.attributes.tracks = new Array();
            this.attributes.tracks.push({
                audio: {
                    bitrate: 91741,
                    channels: 2,
                    device: "",
                    encoder: {type: "AAC (Advanced Audio Coding)"},
                    framecount : 22312,
                    id: "audio-1",
                    samplingrate: 44100
                },
                checksum: {type: "md5", $: "97a5e89ed544a21d56f5fb571a085c0a"},
                duration: 518120,
                id: "5e0893dc-b856-46dd-8b34-bbfc19f9399a",
                mimetype: "video/mp4",
                ref: "track:0e714fe5-f15f-4f85-9df1-fa30972c46da",
                tags: {tag: ["archive", "engage-download", "high-quality"]},
                type: "presenter/delivery",
                url: "http://localhost/moodle/filter/opencastfilter/test/test.mp4",
                video: {
                    bitrate: 2999377,
                    device: "",
                    encoder: {type: "H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10"},
                    framecount: 12953,
                    framerate: 25,
                    id: "video-1",
                    resolution: "1920x1080"
                }
                });
            this.attributes.tracks.push({
                audio: {
                    bitrate: 128000,
                    channels: 2,
                    device: "",
                    encoder: {type: "MP3 (MPEG audio layer 3)"},
                    id: "audio-1",
                    samplingrate: 44100
                },
                checksum: {type: "md5", $: "def6efe40f542b62b1de38aeab1904c2"},
                duration: 518086,
                id: "f2aac88d-5afb-498c-adaa-3add869564d5",
                mimetype: "audio/mpeg",
                ref: "track:0e714fe5-f15f-4f85-9df1-fa30972c46da",
                tags: {tag: ["archive", "engage-download"]},
                type: "presenter/delivery",
                url: "http://localhost/moodle/filter/opencastfilter/test/test2.mp3"
            });
            this.attributes.tracks.push({
                audio: {
                    bitrate: 91741,
                    channels: 2,
                    device: "",
                    encoder: {type: "AAC (Advanced Audio Coding)"},
                    framecount : 22312,
                    id: "audio-1",
                    samplingrate: 44100
                },
                checksum: {type: "md5", $: "22182c860aea59f2e48c6a0968d6ec75"},
                duration: 518120,
                id: "21f788e2-ac94-43f5-a51a-b6e031d5dd04",
                mimetype: "video/mp4",
                ref: "track:0e714fe5-f15f-4f85-9df1-fa30972c46da",
                tags: {tag: ["archive", "engage-download", "low-quality"]},
                type: "presenter/delivery",
                url: "http://localhost/moodle/filter/opencastfilter/test/test3.mp4",
                video: {
                    bitrate: 755664,
                    device: "",
                    encoder: {type: "H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10"},
                    framecount: 12953,
                    framerate: 25,
                    id: "video-1",
                    resolution: "854x480"
                }
            });
            this.attributes.tracks.push({
                audio: {
                    bitrate: 91741,
                    channels: 2,
                    device: "",
                    encoder: {type: "AAC (Advanced Audio Coding)"},
                    framecount : 22312,
                    id: "audio-1",
                    samplingrate: 44100
                },
                checksum: {type: "md5", $: "6423f0897025e1efb6c34e1d14f2ddae"},
                duration: 518120,
                id: "bbdc3191-1b3e-4b55-9d57-b5307670639d",
                mimetype: "video/mp4",
                ref: "track:0e714fe5-f15f-4f85-9df1-fa30972c46da",
                tags: {tag: ["archive", "engage-download", "medium-quality"]},
                type: "presenter/delivery",
                url: "http://localhost/moodle/filter/opencastfilter/test/test4.mp4",
                video: {
                    bitrate: 1478940,
                    device: "",
                    encoder: {type: "H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10"},
                    framecount: 12953,
                    framerate: 25,
                    id: "video-1",
                    resolution: "1280x720"
                }
            });

            this.attributes.tracks.push({
                duration: 100,
                id: "testid123",
                mimetype: "video/mp4",
                tags: {tag: ["high-quality"]},
                type: "presenter/delivery",
                url: "http://localhost/moodle/filter/opencastfilter/test.mp4",
                video: {resolution: "854x480"}
            });



            this.attributes.attachments = new Array();
            this.attributes.attachments.push({
                id: "d0ffbfa2-ad55-41d0-8f94-88aea3ea1883",
                mimetype: "image/jpeg",
                ref: "track:0e714fe5-f15f-4f85-9df1-fa30972c46da",
                size: 0,
                tags: {tag: "engage-download"},
                type: "presenter/player+preview",
                url: "http://localhost/moodle/filter/opencastfilter/test/bild1.jpg"
            });
            this.attributes.attachments.push({
                additionalProperties: {property: [
                    {key: "resolutionY", $: "-1"},
                    {key: "imageCount", $: "100"},
                    {key: "resolutionX", $: "160"},
                    {key: "imageSizeY", $: "10"},
                    {key: "imageSizeX", $: "10"}
                ]},
                id: "cb02839b-d96d-4138-9224-5a8b05f71231",
                mimetype: "image/png",
                ref: "track:0e714fe5-f15f-4f85-9df1-fa30972c46da",
                size: 0,
                tags: {tag: "engage-download"},
                type: "presenter/timeline+preview",
                url: "http://localhost/moodle/filter/opencastfilter/test/bild2.png"
            });
            this.attributes.attachments.push({
                id: "4fd71cad-dc3b-4bda-b560-e5652d640aa7",
                mimetype: "text/xml",
                size: 0,
                tags: {tag: "archive"},
                type: "security/xacml+series",
                url: "http://localhost/moodle/filter/opencastfilter/test/xacml.xml"
            });
            this.attributes.attachments.push({
                id: "db1825c0-8855-43f0-abef-f5a6173130cb",
                mimetype: "image/jpeg",
                ref: "track:0e714fe5-f15f-4f85-9df1-fa30972c46da",
                size: 0,
                tags: {tag: ["archive", "engage-download"]},
                type: "presenter/search+preview",
                url: "http://localhost/moodle/filter/opencastfilter/test/bild3.jpg"
            });
            this.attributes.attachments.push({
                id: "76695cab-c6f6-4fc4-9471-528c05766728",
                mimetype: "text/xml",
                size: 0,
                tags: {tag: "archive"},
                type: "security/xacml+episode",
                url: "http://localhost/moodle/filter/opencastfilter/test/xacml2.xml"
            });

            // Check types
            this.attributes.series = "";
            this.attributes.seriesid = "";
            this.attributes.title = "";
            this.attributes.creator = "";
            this.attributes.date = "";
            this.attributes.description = "";
            this.attributes.subject = "";
            this.attributes.contributor = "";
            this.attributes.segments = "";
            this.attributes.eventid = "";
            this.attributes.ready = true;

            this.trigger("change");
            Engage.trigger(events.mediaPackageLoaded.getName());
            Engage.log("MhConnection: Mediapackage Data change event thrown");

            // Check if successful
            // Engage.log("MhConnection: Mediapackage data not loaded successfully");
            // Engage.trigger(events.mediaPackageModelInternalError.getName());
        },
        defaults: {
            "title": "",
            "creator": "",
            "date": "",
            "description": "",
            "subject": "",
            "tracks": {},
            "attachments": {},
            "ready" : false
        }
    });

    return MediaPackageModel;
});
