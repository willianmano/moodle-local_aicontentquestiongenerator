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

namespace local_aicontentquestiongenerator\aiactions;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Message client class.
 *
 * @package     mod_aiagents
 * @copyright   2025 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text extends base {
    /**
     * Send the message to the AI agent and return the response.
     *
     * @return array
     * @throws GuzzleException
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function send($prompt) {
        $response = $this->client->post("/v1/responses", [
            'json' => [
                'model' => 'gpt-4o',
                'input' => $prompt
            ],
        ]);

        $data = json_decode($response->getBody()->getContents());

        if (!isset($data->output[0]->content[0]->text)) {
            return null;
        }

        $json = json_encode($data->output[0]->content[0]->text, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $json = str_replace('```json', '', $json);
        $json = str_replace('```', '', $json);
        $json = str_replace('\n', '', $json);
        $json = str_replace('\\', '', $json);

        // Remove aspas duplas do início e do fim.
        $json = ltrim($json, '"');
        $json = rtrim($json, '"');

        return $json;
    }
}
