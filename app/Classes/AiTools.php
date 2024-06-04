<?php

namespace App\Classes;

use App\Models\Config;
use Orhanerday\OpenAi\OpenAi;
use App\Models\CustomTemplate;

class AiTools
{
    public function generate($request)
    {
        // Query
        $config = Config::get();

        // Parameters
        $open_ai_key = $config[35]->config_value;
        $open_ai = new OpenAi($open_ai_key);
        $level = (float)$request->level;
        $maxTokens = 600;
        $topP = 1.0;
        $frequencyPenalty = 0.0;
        $presencePenalty = 0.0;

        // Template
        $template_details = CustomTemplate::join('custom_template_fields', 'custom_templates.id', '=', 'custom_template_fields.template_id')->select('custom_templates.*', 'custom_template_fields.*')->where('custom_templates.unique_slug', $request->type)->where('custom_templates.status', 1)->get();

        // set default prompt and Max Token
        $prompt = "";
        $maxTokens = (int)$request->max_length;

        // Prompt
        $prompt = $template_details[0]->prompt;
        $prompt = str_replace("##results##", $request->results, $prompt);
        $prompt = str_replace("##tone##", $request->tone, $prompt);
        $prompt = str_replace("##lang##", $request->lang, $prompt);

        for ($i=0; $i < count($template_details); $i++) { 
            $placeholder = "##input" . ($i + 1) . "##";
            $replacement = $request->input("input".($i + 1));
            $prompt = str_replace($placeholder, $replacement, $prompt);
        }

        // Parameters
        if ($config[34]->config_value == "gpt-3.5-turbo" || $config[34]->config_value == "gpt-3.5-turbo-16k" || $config[34]->config_value == "gpt-3.5-turbo-1106" || $config[34]->config_value == "gpt-4" || $config[34]->config_value == "gpt-4-32k" || $config[34]->config_value == "gpt-4-vision-preview" || $config[34]->config_value == "gpt-4-1106-preview") {
            $data = [
                'model' => $config[34]->config_value,
                'messages' => array(["role" => "user", "content" => $prompt]),
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    // Set Here Your Requesred Headers
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $open_ai_key,
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                // Result
                $result = json_decode($response);

                // Check error
                if (isset($result->error)) {
                    $this->result = null;
                } else {
                    $this->result = $result->choices[0]->message->content;
                }
            }
        } else {
            $complete = $open_ai->completion([
                'model' => $config[34]->config_value,
                'prompt' => $prompt,
                'temperature' => $level,
                'max_tokens' => $maxTokens,
                'top_p' => $topP,
                "frequency_penalty" => $frequencyPenalty,
                "presence_penalty" => $presencePenalty
            ]);

            // Result
            $result = json_decode($complete);

            // Check error
            if (isset($result->error)) {
                $this->result = null;
            } else {
                $this->result = $result->choices[0]->text;
            }
        }

        return $this->result;
    }
}
