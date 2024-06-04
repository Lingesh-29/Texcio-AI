<?php

namespace App\Classes;

use OpenAI;
use App\Models\Config;
use Illuminate\Support\Facades\File;

class AiSpeechText
{
    public function generate($request)
    {
        // Image validatation
        $validator = $request->validate([
            'audio' => 'required|mimes:mp3,mp4,mpeg,mpga,m4a,wav,webm|max:' . env("SIZE_LIMIT") . ''
        ]);

        // Upload audio
        $audiotmp = time();
        $audio = $request->file('audio');
        $input['audio'] = $audiotmp . '.' . $audio->getClientOriginalExtension();
        $destinationPath = public_path('/audio');
        $audio->move($destinationPath, $input['audio']);

        // Query
        $config = Config::get();

        // Parameters
        $open_ai_key = $config[35]->config_value;
        $client = OpenAI::client($open_ai_key);

        $response = $client->audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen(public_path('audio/' . $input['audio']), 'r'),
            'response_format' => 'text'
        ]);

        // Check error
        if (isset($response->error)) {
            $this->result = null;
        } else {
            $this->result = $response->text;
        }

        // Delete audio
        File::delete(public_path('audio/' . $input['audio']));

        return $this->result;
    }
}
