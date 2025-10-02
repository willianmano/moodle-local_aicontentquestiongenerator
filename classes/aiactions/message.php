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

namespace mod_aiagents\aiactions;

use GuzzleHttp\Exception\GuzzleException;

define('API_ENDPOINT_BASEURL', 'https://api.openai.com');

/**
 * Message client class.
 *
 * @package     mod_aiagents
 * @copyright   2025 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message extends base {
    /**
     * @var string|int
     */
    protected string $resourceid;

    /**
     * @var string
     */
    protected string $assistantid;

    /**
     * @var string
     */
    protected $prompttext;

    /**
     * @var string|null
     */
    protected $threadid;

    /**
     * @var null
     */
    protected $attachments;

    /**
     * Create a new instance of the generate_text action.
     *
     * It’s responsible for performing any setup tasks,
     * such as getting additional data from the database etc.
     *
     * @param int $resourceid
     * @param string $prompttext The prompt text used to generate the image.
     * @param string|null $threadid
     * @param array|null $attachments
     * @throws \dml_exception
     */
    public function __construct(int $resourceid, string $prompttext, string|null $threadid = null, array|null $attachments = null) {
        $this->resourceid = $resourceid;

        $this->prompttext = $prompttext;

        $this->attachments = $attachments;

        parent::__construct();

        $this->set_assistant_id($resourceid);

        $this->generate_or_set_thread_id($threadid);
    }

    /**
     * Send the message to the AI agent and return the response.
     *
     * @return array
     * @throws GuzzleException
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function send() {
        $messageid = $this->createmessage();

        $this->createrun();

        $response = $this->client->get("/v1/threads/{$this->threadid}/messages", [
            'query' => [
                'order' => 'asc',
                'after' => $messageid,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents());

        $messages = [];
        foreach ($data->data as $message) {
            $messages[] = [
                'id' => $message->id,
                'body' => format_text($message->content[0]->text->value, FORMAT_MARKDOWN),
            ];
        }

        return [
            'threadid' => $this->threadid,
            'messages' => $messages,
        ];
    }

    /**
     * Create a new run for the thread.
     *
     * @return void
     * @throws GuzzleException
     */
    public function createrun() {
        $this->client->post("/v1/threads/{$this->threadid}/runs", [
            'json' => [
                'assistant_id' => $this->assistantid,
                'stream' => true,
            ],
        ]);
    }

    /**
     * Create a new thread.
     *
     * @return void
     * @throws GuzzleException
     * @throws \dml_exception
     */
    public function createthread() {
        $response = $this->client->post('/v1/threads');

        $thread = json_decode($response->getBody()->getContents());

        if (!empty($thread->id)) {
            $this->threadid = $thread->id;

            $this->log_action('thread');
        }
    }

    /**
     * Create a message in the thread.
     *
     * @return mixed
     * @throws GuzzleException
     * @throws \dml_exception
     */
    public function createmessage() {
        $data = [
            'role' => 'user',
            'content' => $this->prompttext,
        ];

        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $data['attachments'][] = [
                    'file_id' => $attachment,
                    'tools' => [
                        [
                            'type' => 'file_search',
                        ],
                    ],
                ];
            }
        }

        $response = $this->client->post("/v1/threads/{$this->threadid}/messages", [
            'json' => $data,
        ]);

        $message = json_decode($response->getBody()->getContents());

        $this->log_action('message');

        return $message->id;
    }

    /**
     * Set the assistant ID based on the resource ID.
     *
     * @param $resourceid
     * @return void
     * @throws \dml_exception
     */
    private function set_assistant_id($resourceid): void {
        global $DB;

        $resource = $DB->get_record('aiagents_resources', ['id' => $resourceid], 'id, identifier', MUST_EXIST);

        $this->assistantid = $resource->identifier;
    }

    /**
     * Generate or set the thread ID.
     *
     * @param $threadid
     * @return void
     */
    private function generate_or_set_thread_id($threadid = null): void {
        $this->threadid = $threadid;

        if (empty($threadid)) {
            $this->createthread();
        }
    }

    /**
     * Log the action performed by the user.
     *
     * @param $type
     * @return void
     * @throws \dml_exception
     */
    private function log_action($type) {
        global $DB, $USER;

        $data = [
            'resourceid' => $this->resourceid,
            'userid' => $USER->id,
            'threadid' => $this->threadid,
            'type' => $type,
            'timecreated' => time(),
        ];

        $DB->insert_record('aiagents_logs', $data);
    }
}
