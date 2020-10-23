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
 * Opencast filter
 *
 * @package    filter_opencast
 * @subpackage opencastfilter
 * @copyright  2018 Tamara Gunkel, 2020 Nina Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->maturity = MATURITY_RC;
$plugin->version = 2018031902;        // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires = 2017050500;        // Requires this Moodle version.
$plugin->component = 'filter_opencast'; // Full name of the plugin.
$plugin->dependencies = array(
    'block_opencast' => 2019052900, // Requires Block Opencast and Tool Opencast.
    'tool_opencast' => 2018102900,
);