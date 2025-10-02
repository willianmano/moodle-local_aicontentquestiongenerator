<?php

namespace local_aicontentquestiongenerator\support;

use core_ai\aiactions\responses\response_base;

class questions {
    public function generate($module, $instance) {
        global $DB;

        $module = get_coursemodule_from_id($module, $instance);

        $page = $DB->get_record('page', ['id' => $module->instance], '*', MUST_EXIST);

        $config = get_config('local_aicontentquestiongenerator');
        $prompt = $config->prompt . format_text($page->content);

        $text = new \local_aicontentquestiongenerator\aiactions\text();

        return $text->send($prompt);
    }
}