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

use GuzzleHttp\Client;

define('API_ENDPOINT_BASEURL', 'https://api.openai.com');

/**
 * Client consumer base class.
 *
 * @package     local_aicontentquestiongenerator
 * @copyright   2025 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base {
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * Class constructor.
     *
     * @throws \dml_exception
     */
    public function __construct() {
        $config = get_config('local_aicontentquestiongenerator');
        $authtoken = 'Bearer ' . $config->authtoken;

        $this->client = new Client([
            'base_uri' => API_ENDPOINT_BASEURL,
            'headers' => [
                'Authorization' => $authtoken,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2',
            ],
        ]);
    }
}
