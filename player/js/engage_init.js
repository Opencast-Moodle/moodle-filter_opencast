/* global require.js config */
requirejs.config({
    baseUrl: "js/lib",
    waitSeconds: 50,
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
// start core logic
require(["engage/core"]);
