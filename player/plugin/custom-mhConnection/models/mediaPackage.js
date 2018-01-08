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
                id: "testid123",
                url: "http://localhost/moodle/filter/opencastfilter/test.mp4",
                mimetype: "video/mp4"});

            this.attributes.attachments = new Array();
            this.attributes.attachments.push();

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
