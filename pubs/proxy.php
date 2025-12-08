<?php
// URL of the FSA XML
$url = "https://ratings.food.gov.uk/api/open-data-files/FHRS306en-GB.xml";


// Fetch the remote XML
$xml = file_get_contents($url);

if ($xml === false) {
    http_response_code(500);
    echo "Error fetching data";
    exit;
}

// Optional: fix unescaped & characters
$xml = preg_replace('/&(?!amp;|lt;|gt;|quot;|apos;)/', '&amp;', $xml);

// Set header and output
header("Content-Type: application/xml");
echo $xml;
