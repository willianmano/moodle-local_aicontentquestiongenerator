<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_aicontentquestiongenerator\hooks;

use core\hook\output\before_footer_html_generation;

/**
 * Hook callbacks for aicontentquestiongenerator.
 *
 * @package    local_aicontentquestiongenerator
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Bootstrap the aicontentquestiongenerator library.
     *
     * @param before_footer_html_generation $hook
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        global $PAGE;

        if (isguestuser() || !isloggedin() || !$PAGE->cm) {
            return;
        }

        $renderer = $PAGE->get_renderer('local_aicontentquestiongenerator');

        $contentrenderable = new \local_aicontentquestiongenerator\output\html(
            $PAGE->context->id,
            $PAGE->course->id,
            $PAGE->cm->id,
            $PAGE->cm->modname
        );

        $hook->add_html($renderer->render($contentrenderable));
    }
}
