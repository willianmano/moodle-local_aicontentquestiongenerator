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

namespace local_aicontentquestiongenerator\observers;

/**
 * Event observer class.
 *
 * @package     local_aicontentquestiongenerator
 * @category    event
 * @copyright   2025 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module {

    /**
     * Triggered via $event.
     *
     * @param \core\event\course_module_created $event The event.
     * @return bool True on success.
     */
    public static function created(\core\event\course_module_created $event) {
        global $DB;

        if ($event->other['modulename'] === 'page') {
            $questions = new \local_aicontentquestiongenerator\support\questions();
            $record = new \stdClass();
            $record->cmid = $event->objectid;
            $record->questions = $questions->generate($event->other['modulename'], $event->objectid);
            $record->timecreated = time();
            $record->timemodified = time();

            $DB->insert_record('local_aicontentquestiongenerator', $record);
        }

        return true;
    }

    /**
     * Triggered via $event.
     *
     * @param \core\event\course_module_updated $event The event.
     * @return bool True on success.
     */
    public static function updated($event) {

        // For more information about the Events API please visit {@link https://docs.moodle.org/dev/Events_API}.

        return true;
    }

    /**
     * Triggered via $event.
     *
     * @param \core\event\course_module_deleted $event The event.
     * @return bool True on success.
     */
    public static function deleted($event) {

        // For more information about the Events API please visit {@link https://docs.moodle.org/dev/Events_API}.

        return true;
    }
}
