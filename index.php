<?php
$stopWords = ["the", "and", "in", "on", "at", "to", "is", "are", "was", "were", "it", "this", "that", "of", "for", "with", "as", "by", "an", "a", "or", "be", "from", "but", "not", "if", "than", "then", "so", "will", "has", "have", "had"];

function analyzeText($text, $stopWords) {
    $text = strtolower($text);
    $text = preg_replace("/[^a-z0-9\s]/", "", $text);
    $words = explode(" ", $text);
    
    $filteredWords = array_filter($words, function ($word) use ($stopWords) {
        return $word !== "" && !in_array($word, $stopWords);
    });
    
    return array_count_values($filteredWords);
}

function orderWords($wordData, $order) {
    $order === "desc" ? arsort($wordData) : asort($wordData);
    return $wordData;
}

$result = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text'] ?? "";
    $sortOrder = $_POST['sort'] ?? "desc";
    $limit = (int) ($_POST['limit'] ?? 10);

    if (!empty($text)) {
        $wordData = analyzeText($text, $stopWords);
        $sortedWords = orderWords($wordData, $sortOrder);
        $result = array_slice($sortedWords, 0, $limit, true);
    } else {
        $error = "Oops! You forgot to enter text.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Word Frequency Counter</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Word Frequency Counter</h1>
    
    <form method="post">
        <label for="text">Paste your text here:</label><br>
        <textarea id="text" name="text" rows="10" cols="50" required><?php echo htmlspecialchars($_POST['text'] ?? ""); ?></textarea><br><br>
        
        <label for="sort">Sort by frequency:</label>
        <select id="sort" name="sort">
            <option value="desc" <?php echo (($_POST['sort'] ?? "") === "desc") ? "selected" : ""; ?>>Descending</option>
            <option value="asc" <?php echo (($_POST['sort'] ?? "") === "asc") ? "selected" : ""; ?>>Ascending</option>
        </select><br><br>
        
        <label for="limit">Number of words to display:</label>
        <input type="number" id="limit" name="limit" value="<?php echo htmlspecialchars($_POST['limit'] ?? 10); ?>" min="1"><br><br>
        
        <input type="submit" value="Calculate Word Frequency">
    </form>
    
    <?php if (!empty($error)) : ?>
        <p style="color: red;"> <?php echo $error; ?> </p>
    <?php endif; ?>
    
    <?php if (!empty($result)) : ?>
        <h3>Word Frequency Results:</h3>
        <ul>
            <?php foreach ($result as $word => $count) : ?>
                <li><?php echo htmlspecialchars($word) . " - " . $count; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
