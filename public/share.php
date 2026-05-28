<?php
// share.php - Dynamically serve Open Graph tags for NACOS Nominees to social media crawlers.

$id = isset($_GET['id']) ? $_GET['id'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// 1. Fetch nominee details from the voting backend API
$apiUrl = "https://nacos-website-production.up.railway.app/api/nominees/" . urlencode($id);

$nomineeName = "Vote Nominee";
$nomineeBio = "Support this candidate in the NACOS Awards!";
$photoUrl = "https://nacoslasustech.org.ng/og-image.png"; // Fallback
$categoryName = "NACOS Awards Category";

if (!empty($id)) {
    // Perform curl request to Railway API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Crucial: Disable SSL checks on cPanel curl
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5s timeout
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

// 2. Output the HTML with Open Graph headers only
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vote for <?php echo htmlspecialchars($nomineeName); ?> | NACOS Awards</title>
    
    <!-- Open Graph tags for social crawlers -->
    <meta property="og:title" content="Vote for <?php echo htmlspecialchars($nomineeName); ?> | NACOS Awards">
    <meta property="og:description" content="<?php echo htmlspecialchars($nomineeBio); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($photoUrl); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://nacoslasustech.org.ng/voting/<?php echo htmlspecialchars($category); ?>/<?php echo htmlspecialchars($id); ?>">
    
    <!-- Twitter tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Vote for <?php echo htmlspecialchars($nomineeName); ?> | NACOS Awards">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($nomineeBio); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($photoUrl); ?>">

    <!-- Redirect humans in case they land here directly -->
    <script type="text/javascript">
        window.location.href = "https://nacoslasustech.org.ng/voting/<?php echo htmlspecialchars($category); ?>/<?php echo htmlspecialchars($id); ?>";
    </script>
</head>
<body>
    <p>Redirecting to voting profile...</p>
</body>
</html>
