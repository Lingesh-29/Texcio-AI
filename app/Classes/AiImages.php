<?php

namespace App\Classes;

use App\Models\Config;
use Illuminate\Support\Facades\Log;
use Orhanerday\OpenAi\OpenAi;
use Illuminate\Support\Facades\Storage;

class AiImages
{
    public function generate($request)
    {
        // Query
        $config = Config::get();

        // Parameters
        $open_ai_key = $config[35]->config_value;
        $open_ai = new OpenAi($open_ai_key);
        $size = $request->size;
        $results = (int)$request->results;

        // Parameters 
        $complete = $open_ai->image([
            "model" => $config[46]->config_value,
            "prompt" => "Create " . $request->name . " image using " . $request->style,
            "n" => $results,
            "size" => $size,
            "response_format" => "url",
        ]);

        // Result
        $result = json_decode($complete);

        // Save the generated image to the public storage
        if ($result && isset($result->data)) {
            // Array to store paths of saved images
            $savedImagePaths = array();

            foreach ($result->data as $imageUrl) {
                // Get the image content
                $imageContent = file_get_contents($imageUrl->url);

                // Generate a unique filename for the image
                $filename = uniqid() . '-' . time() . '.png'; // Adjust the extension based on the actual format

                // Save the image to the public storage using public_path
                $filePath = public_path("images/generate-images/{$filename}");
                file_put_contents($filePath, $imageContent);

                // The full path to the saved image
                $path = "images/generate-images/{$filename}";

                // Store the path in the array
                $savedImagePaths[] = $path;
            }
        } else {
            // Handle the case where the image URL is not available in the API response
            $path = null;
        }

        // Check error
        if (isset($result->error)) {
            $this->result = $result->error;
        } else {
            $this->result = $savedImagePaths;
        }

        return $this->result;
    }
}
