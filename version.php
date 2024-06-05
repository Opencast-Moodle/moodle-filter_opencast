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
 * @copyright  2018 Tamara Gunkel, 2020 Nina Herrmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'filter_opencast';
$plugin->release = 'v4.4-r1';
$plugin->version = 2024060500;
$plugin->requires = 2022112800; // Requires Moodle 4.1+.
$plugin->maturity = MATURITY_STABLE;
$plugin->dependencies = [
    'tool_opencast' => 2024060400,
    'mod_opencast' => 2024060400,
];
