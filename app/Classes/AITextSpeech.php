<?php

namespace App\Classes;

use OpenAI;
use App\Models\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AiTextSpeech
{
    public function generate($request)
    {
        // Image validatation
        $validator = $request->validate([
            'name' => 'required',
            'voices' => 'required',
            'speed' => 'required',
            'audio_format' => 'required',
            'content' => 'required'
        ]);

        // Query
        $config = Config::get();

        // Parameters
        $open_ai_key = $config[35]->config_value;
        $client = OpenAI::client($open_ai_key);

        $response = $client->audio()->speech([
            'model' => $config[47]->config_value,
            'input' => $request->content,
            'voice' => $request->voices,
            'response_format' => $request->audio_format,
            'speed' => $request->speed
        ]);

        // Upload audio
        $audioName = strtolower(\str_replace(' ', '-', $request->name)) . '-' . uniqid() . '' . time();
        file_put_contents('audio/' . $audioName . "." . $request->audio_format, $response);

        // Check error
        if (isset($response->error)) {
            $this->result = null;
        } else {
            $this->result = 'audio/' . $audioName . "." . $request->audio_format;
        }

        return $this->result;
    }
}
