/* global require.js config */
requirejs.config({
    baseUrl: "js/lib",
    waitSeconds: 30,
    paths: {
        engage: "../engage",
        plugins: "../plugin"
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

var PLUGIN_PATH = "../../plugin/";

// Get opencast url
var query = window.location.search.substring(1);
var vars = query.split("&");
var opencastlink = "";
for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    if (pair[0] == "ocurl") {
        opencastlink =  decodeURIComponent(pair[1]);
        break;
    }
}

// start core logic
require(["engage/core"]);
