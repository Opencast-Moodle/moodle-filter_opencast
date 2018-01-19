/* global require.js config */
define(["core/config"], function(mdlcfg) {

    window.requirejs.config({
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

    var PLUGIN_PATH = mdlcfg.wwwroot + "/filter/opencast/player/plugin/";
// start core logic
    require(["engage/core"]);
});
