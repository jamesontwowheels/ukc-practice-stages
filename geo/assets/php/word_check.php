<?PHP

function isValidEnglishWord($word) {
    $apiKey = '36dd0919-81f8-43f0-9661-70c4d811a482'; // Replace with your actual API key
    
    // The API URL for checking the word
    $url = "https://www.dictionaryapi.com/api/v3/references/collegiate/json/" . urlencode($word) . "?key=" . $apiKey;
    
    // Initialize cURL
    $ch = curl_init($url);
    
    // Set options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
        return false;
    }
    
    // Get the HTTP status code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close the cURL session
    curl_close($ch);
    
    // If the status code is 200 and the response contains valid entries, the word is valid
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        // Check if there are valid entries
        if (is_array($data) && !empty($data) && isset($data[0]['meta'])) {
            return true;
        }
    }
    
    return false;
}

// Example usage
/*$word = "ECI";
if (isValidEnglishWord($word)) {
    echo "'$word' is a valid English word.";
} else {
    echo "'$word' is not a valid English word.";
}*/