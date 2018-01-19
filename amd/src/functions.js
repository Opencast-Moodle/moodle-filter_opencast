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
 * Ajax functions for moodleoverflow
 *
 * @module     mod/moodleoverflow
 * @package    mod_moodleoverflow
 * @copyright  2017 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(["core/config"], function (mdlcfg) {

    var t = {
        injectcss: function (csslink) {
            var link = document.createElement("link");
            link.href = csslink;
            link.type = "text/css";
            link.rel = "stylesheet";
            document.getElementsByTagName("head")[0].appendChild(link);
        },

        init: function () {
            window.requirejs.config({
                enforceDefine: false,
                baseUrl: mdlcfg.wwwroot + "/filter/opencast/player/js/lib",
                waitSeconds: 50,
                paths: {
                    engage: mdlcfg.wwwroot + "/filter/opencast/player/js/engage",
                    plugins: mdlcfg.wwwroot + "/filter/opencast/player/plugin"
                },
                shim: {
                    "bootstrap": {
                        deps: ["jquery"],
                        exports: "Bootstrap"
                    },
                    "backbone": {
                        deps: ["underscore", "jquery"],
                        exports: "Backbone"
                    },
                    "underscore": {
                        exports: "_"
                    },
                    "mousetrap": {
                        exports: "Mousetrap"
                    },
                    "moment": {
                        exports: "Moment"
                    },
                    "basil": {
                        exports: "Basil"
                    },
                    "bowser": {
                        exports: "Bowser"
                    },
                    "bootbox": {
                        deps: ["bootstrap"],
                        exports: "Bootbox"
                    }
                }
            });


// start core logic
            require(["engage/core"]);
        }
    };

    return t;
});
