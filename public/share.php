<?php
// share.php - Dynamically serve Open Graph tags for NACOS Nominees by injecting them into index.html

$id = isset($_GET['id']) ? $_GET['id'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// 1. Default fallback values
$nomineeName = "Vote Nominee";
$nomineeBio = "Support this candidate in the NACOS Awards!";
$photoUrl = "https://nacoslasustech.org.ng/og-image.png"; // Fallback
$categoryName = "NACOS Awards Category";

// 2. Fetch nominee details from the voting backend API if we have an ID
if (!empty($id)) {
    $apiUrl = "https://nacos-website-production.up.railway.app/api/nominees/" . urlencode($id);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 seconds max timeout
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['nominee'])) {
            $nominee = $data['nominee'];
            $nomineeName = isset($nominee['name']) ? $nominee['name'] : $nomineeName;
            $categoryName = isset($nominee['categoryName']) ? $nominee['categoryName'] : (isset($nominee['category']) ? $nominee['category'] : $categoryName);
            $nomineeBio = "Vote for " . $nomineeName . " contesting for \"" . $categoryName . "\".";
            
            // Resolve photo URL (cPanel persistent uploads)
            $photo = isset($nominee['photo']) ? $nominee['photo'] : '';
            if (!empty($photo)) {
                if (strpos($photo, 'http://') === 0 || strpos($photo, 'https://') === 0) {
                    $photoUrl = $photo;
                } else {
                    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
                    $photoUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . ltrim($photo, '/');
                }
            }
        }
    }
}

// 3. Read index.html and dynamically inject the tags
$htmlFile = __DIR__ . '/index.html';
if (file_exists($htmlFile)) {
    $html = file_get_contents($htmlFile);
    
    // Define dynamic values
    $title = "Vote for " . htmlspecialchars($nomineeName) . " | NACOS Awards";
    $description = htmlspecialchars($nomineeBio);
    $image = htmlspecialchars($photoUrl);
    
    // Replace <title>
    $html = preg_replace('/<title>(.*?)<\/title>/is', '<title>' . $title . '</title>', $html);
    
    // Replace Meta descriptions
    $html = preg_replace('/<meta name="description" content="(.*?)"/is', '<meta name="description" content="' . $description . '"', $html);
    $html = preg_replace('/<meta name="description" content=\'(.*?)\'/is', '<meta name="description" content="' . $description . '"', $html);
    
    // Replace Open Graph Tags
    $html = preg_replace('/<meta property="og:title" content="(.*?)"/is', '<meta property="og:title" content="' . $title . '"', $html);
    $html = preg_replace('/<meta property="og:description" content="(.*?)"/is', '<meta property="og:description" content="' . $description . '"', $html);
    $html = preg_replace('/<meta property="og:image" content="(.*?)"/is', '<meta property="og:image" content="' . $image . '"', $html);
    
    // Replace Twitter Tags
    $html = preg_replace('/<meta name="twitter:title" content="(.*?)"/is', '<meta name="twitter:title" content="' . $title . '"', $html);
    $html = preg_replace('/<meta name="twitter:description" content="(.*?)"/is', '<meta name="twitter:description" content="' . $description . '"', $html);
    $html = preg_replace('/<meta name="twitter:image" content="(.*?)"/is', '<meta name="twitter:image" content="' . $image . '"', $html);

    // Also update dynamic url tag
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $html = preg_replace('/<meta property="og:url" content="(.*?)"/is', '<meta property="og:url" content="' . htmlspecialchars($url) . '"', $html);

    echo $html;
} else {
    // Ultimate fallback if index.html is missing
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Vote for <?php echo htmlspecialchars($nomineeName); ?> | NACOS Awards</title>
        <meta property="og:title" content="Vote for <?php echo htmlspecialchars($nomineeName); ?> | NACOS Awards">
        <meta property="og:description" content="<?php echo htmlspecialchars($nomineeBio); ?>">
        <meta property="og:image" content="<?php echo htmlspecialchars($photoUrl); ?>">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Vote for <?php echo htmlspecialchars($nomineeName); ?> | NACOS Awards">
        <meta name="twitter:description" content="<?php echo htmlspecialchars($nomineeBio); ?>">
        <meta name="twitter:image" content="<?php echo htmlspecialchars($photoUrl); ?>">
        <script type="text/javascript">
            window.location.href = "/voting/<?php echo htmlspecialchars($category); ?>/<?php echo htmlspecialchars($id); ?>";
        </script>
    </head>
    <body>
        <p>Redirecting to voting profile...</p>
    </body>
    </html>
    <?php
}
?>
