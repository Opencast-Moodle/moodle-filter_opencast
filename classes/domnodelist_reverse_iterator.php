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
 * Version information
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2018 RWTH Aachen University
 * @author     Tim Schroeder <t.schroeder@itc.rwth-aachen.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_opencast;
defined('MOODLE_INTERNAL') || die;

/**
 * Wrapper that enables iteration over DOMNodeLists with foreach loops
 * while allowing the current DOMNode to be removed or replaced from
 * within the loop.
 *
 * @package    filter
 * @subpackage opencast
 * @copyright  2018 RWTH Aachen University
 * @author     Tim Schroeder <t.schroeder@itc.rwth-aachen.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class domnodelist_reverse_iterator implements \Iterator, \Countable {

    private $nodes = null;
    private $position = -1;

    public function __construct(\DOMNodeList $nodes) {
        $this->nodes = $nodes;
        $this->position = $nodes->length - 1;
    }

    public function rewind() {
        $this->position = $this->nodes->length - 1;
    }

    public function current() {
        return $this->nodes->item($this->position);
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        --$this->position;
    }

    public function valid() {
        return $this->position > -1;
    }

    public function count() {
        return $this->nodes->length;
    }

}