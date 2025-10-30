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
 * Main renderer
 *
 * @package     local_aicontentquestiongenerator
 * @copyright   2025 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_aicontentquestiongenerator\output;

use renderable;
use templatable;
use renderer_base;

class html implements renderable, templatable {
    protected $contextid;
    protected $courseid;
    protected $cmid;
    protected $module;

    public function __construct($contextid, $courseid, $cmid, $module) {
        $this->contextid = $contextid;
        $this->courseid = $courseid;
        $this->cmid = $cmid;
        $this->module = $module;
    }

    public function export_for_template(renderer_base $output) {
        global $DB;

        $data = $DB->get_record('local_aicontentquestiongenerator', ['cmid' => $this->cmid]);

        if (!$data) {
            return ['hascontent' => false];
        }

        $data = json_decode($data->questions, true);

        foreach ($data['questoes'] as $key => $questao) {
            $data['questoes'][$key]['key'] = $key;
        }

        $data['hascontent'] = true;

        return $data;
    }
}