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

/**
 * Plugin administration pages are defined here.
 *
 * @package     local_aicontentquestiongenerator
 * @category    admin
 * @copyright   2025 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_aicontentquestiongenerator_settings', new lang_string('pluginname', 'local_aicontentquestiongenerator'));

    $settings->add(new admin_setting_configtext("local_aicontentquestiongenerator/authtoken", "Token da API do OpenAI", '', '', PARAM_TEXT, 30));

    $settings->add(
        new admin_setting_configtextarea(
            'local_aicontentquestiongenerator/prompt',
            get_string('prompt', 'local_aicontentquestiongenerator'),
            '',
            get_string('prompttext', 'local_aicontentquestiongenerator'),
            PARAM_TEXT,
        )
    );

    $ADMIN->add('localplugins', $settings);
}
