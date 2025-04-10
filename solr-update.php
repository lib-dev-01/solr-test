<?php
function sendToSolr($core, $data) {
    $solr_url = "http://localhost:8983/solr/$core/update?commit=true";
    
    $json_data = json_encode($data, JSON_UNESCAPED_UNICODE);

    $ch = curl_init($solr_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

$mysqli = new mysqli("localhost", "root", "", "library");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$languages = ["en" => "library_en", "zh" => "library_zh", "ja" => "library_ja", "ko" => "library_ko"];

foreach ($languages as $lang => $core) {
    $query = "SELECT id, title, author, year, category, summary FROM books_$lang";
    $result = $mysqli->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => $row["id"],
            "title" => $row["title"],
            "author" => $row["author"],
            "year" => $row["year"],
            "category" => $row["category"],
            "summary" => $row["summary"]
        ];
    }

    if (!empty($data)) {
        echo "Sending data to Solr core: $core\n";
        $response = sendToSolr($core, $data);
        echo "Response: $response\n";
    }
}

$mysqli->close();
?>
