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
 * Opencast library functions.
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2017 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function filter_opencast_load_player() {
    global $CFG;
    global $PAGE;
    $PAGE->requires->js_call_amd('filter_opencast/functions','init');
    $PAGE->requires->js_call_amd('filter_opencast/functions','injectcss', array($CFG->wwwroot.'/filter/opencast/player/css/bootstrap/css/bootstrap.css'));
    $PAGE->requires->js_call_amd('filter_opencast/functions','injectcss', array($CFG->wwwroot.'/filter/opencast/player/css/core_global_style.css'));


    //  $PAGE->requires->js(new moodle_url('/filter/opencast/player/js/engage_init.js'));
    return '
<noscript>
    <div class="noJavaScript">
        <img src="'.$CFG->wwwroot.'/filter/opencast/player/img/opencast.svg" class="loadingImg"/>
        <h1>Error</h1>
        <p id="noJavaScript-container">
            JavaScript is not enabled in your browser.
            <br/>
            Please <a href="http://www.enable-javascript.com" class="noJavaScriptLink" target="_blank">enable
            JavaScript</a> to display the content of this site correctly.
        </p>
    </div>
</noscript>
<div id="browserWarning" class="alert alert-danger" role="alert">
    <h1>Browser not supported</h1>
    <p>
        Your browser is not supported by Opencast. Please download a recent version of one of the following browsers to
        get all of the provided features:
    </p>
    <ul>
        <li>
            <a href="https://www.mozilla.org/firefox" class="alert-link" target="_blank">Mozilla Firefox
                <div id="min-firefox-version" class="browser-version">24</div>
                +</a>
        </li>
        <li>
            <a href="https://www.google.com/chrome" class="alert-link" target="_blank">Google Chrome
                <div id="min-chrome-version" class="browser-version">30</div>
                +</a>
        </li>
        <li>
            <a href="http://www.opera.com/download" class="alert-link" target="_blank">Opera
                <div id="min-opera-version" class="browser-version">20</div>
                +</a>
        </li>
        <li>
            <a href="https://www.apple.com/safari" class="alert-link" target="_blank">Apple Safari
                <div id="min-safari-version" class="browser-version">7</div>
                +</a>
        </li>
        <li>
            <a href="http://windows.microsoft.com/internet-explorer" class="alert-link" target="_blank">Microsoft
                Internet Explorer
                <div id="min-msie-version" class="browser-version">11</div>
                +</a>
        </li>
        <li>
            <a href="https://www.microsoft.com/microsoft-edge" class="alert-link" target="_blank">Microsoft Edge
                <div id="min-msedge-version" class="browser-version">13</div>
                +</a>
        </li>
    </ul>
    <p id="customError_btn-container">
        <button id="btn_tryAnyway" type="button" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-eye-open"></span>&nbsp;Try it anyway
        </button>
    </p>
</div>
<div id="customError" class="alert alert-danger" role="alert">
    <img src="'.$CFG->wwwroot.'/filter/opencast/player/img/opencast.svg" class="loadingImg"/>
    <h1 id="str_error">Error</h1>
    <p>
        <span id="customError_str">An error occurred. Please reload the page.</span>
    </p>
    <p id="customError_btn-container">
        <button id="btn_reloadPage" type="button" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-repeat"></span>&nbsp;<span id="str_reloadPage">Reload page</span>
        </button>
        <button id="btn_login" type="button" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-log-in"></span>&nbsp;<span id="str_login">Log in</span>
        </button>
    </p>
</div>
<!-- loading animation container -->
<div class="loading">
    <img src="'.$CFG->wwwroot.'/filter/opencast/player/img/opencast.svg" class="loadingImg"/>
    <div id="loading1">
        <div class="progress">
            <div class="progress-bar progress-bar-striped active" id="loadingProgressbar1" role="progressbar"
                 aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div>
        </div>
    </div>
    <div id="loading2">
        <div class="progress">
            <div class="progress-bar active" id="loadingProgressbar2" role="progressbar" aria-valuenow="100"
                 aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>
        </div>
    </div>
</div>
<div id="page-cover">
    <button id="btn_fullscreenCancel" type="button" class="btn btn-default"><span
            class="glyphicon glyphicon-remove-circle"></span></button>
</div>
<!-- global main view -->
<div id="engage_view"></div>

    ';


}