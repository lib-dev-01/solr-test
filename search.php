<?php
$query = isset($_GET['q']) ? urlencode($_GET['q']) : '*:*';
// $solr_url = "http://localhost:8983/solr/library/select?q=$query&wt=json&rows=10";
// $solr_url = "http://localhost:8983/solr/library/select?q=$query&defType=edismax&qf=title^2.0+summary^1.5+author^1.0&wt=json&rows=10";
$solr_url = "http://localhost:8983/solr/library/select?q=$query&defType=edismax&qf=title^3.0+author^2.0+category^1.8+summary^1.5+keywords^1.2+year^0.5&wt=json&rows=10";


/*
http://localhost:8983/solr/library_en/select?q=Data%20Science&shards=localhost:8983/solr/library_en,localhost:8983/solr/library_zh,localhost:8983/solr/library_ja,localhost:8983/solr/library_ko&defType=edismax&qf=title^3.0+author^2.0+category^2.0+summary^1.5+year^0.5&fl=id,title,category,author,score,year,summary&wt=json

solr start -Dsolr.disable.allowUrls=true

*/

$ch = curl_init($solr_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$results = json_decode($response, true);

var_dump($results);

echo "<h2>Search Results:</h2>";

if (isset($results['response']['docs'])) {
    foreach ($results['response']['docs'] as $doc) {
        echo "<h3>" . htmlspecialchars($doc['title'][0]) . "</h3>";
        echo "<p><strong>Author:</strong> " . htmlspecialchars($doc['author'][0]) . "</p>";
        echo "<p><strong>Year:</strong> " . htmlspecialchars($doc['year']) . "</p>";
        echo "<hr>";
    }
} else {
    echo "<p>No results found.</p>";
}
?>
