<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include '../config.php';
session_start();

function extractVideoId($url) {
    $parsedUrl = parse_url($url);
    parse_str($parsedUrl['query'], $query);
    return isset($query['v']) ? $query['v'] : null;
}

function extractID($string) {
    $symbolPosition = strpos($string, '[#@');
    if ($symbolPosition !== false) {
        $substringAfterSymbol = substr($string, $symbolPosition);
        $semicolonPosition = strpos($substringAfterSymbol, ';');
        if ($semicolonPosition !== false) {
            $numbers = substr($substringAfterSymbol, 3, $semicolonPosition - 3);
            $numbers = preg_replace("/[^0-9]/", "", $numbers);
            $replacement = "<a href='../messages/spmessages.php/?id=$numbers'>Reply to</a>";
            $string = substr_replace($string, $replacement, $symbolPosition, $semicolonPosition + 1);
        }
    }

    return $string;
}

function convertHashtagsToLinks($message) {
    $pattern = '/#(\w+)/';
    $messageWithLinks = preg_replace($pattern, '<a href="../messages/hashtag.php?tag=$1">#$1</a>', $message);
    return $messageWithLinks;
}

try {
    $messageId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($messageId) {
        $stmt = $pdo->prepare("SELECT `id`, `name`, `message`, `timestamp`
                            FROM messages
                            WHERE id = :id
                            ORDER BY `timestamp` DESC");

        $stmt->bindParam(':id', $messageId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->query("SELECT `id`, `name`, `message`, `timestamp`
                            FROM messages
                            ORDER BY `timestamp` DESC");

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($result) {
        foreach ($result as $row) {
            $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
            $message = htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8');
            $timestamp = $row["timestamp"];
            $id = $row["id"];

            // Extract IDs and convert hashtags to links before displaying the message
            $message = extractID($message);
            $message = convertHashtagsToLinks($message);

            if (filter_var($message, FILTER_VALIDATE_URL) && 
                (strpos($message, '.jpg') !== false || 
                strpos($message, '.jpeg') !== false || 
                strpos($message, '.png') !== false || 
                strpos($message, '.webp') !== false)) {
                echo "<div><b>{$name}:</b> <br> <img src='{$message}' alt='Image' style='max-width: 600px; height: 100%; max-height: 600px;'> <br> (Sent on: {$timestamp})</div>";
                echo "<hr>";
            } elseif (strpos($message, 'https://ltbeta.epicsite.xyz/videodata/non-hls.php?id=https://ltbeta.epicsite.xyz/videodata/non-hls.php?id=') !== false) {
                $videoId = extractVideoId($message);
                if ($videoId) {
                    $videoUrl = "https://ltbeta.epicsite.xyz/videodata/non-hls.php?id={$videoId}&dl=dl&itag=18";
                    echo "<div><b>{$name}:</b> <br> <video controls><source src='{$videoUrl}' type='video/mp4'></video> <br> (Sent on: {$timestamp})</div>";
                    echo "<hr>";
                }
            } elseif (strpos($message, 'https://www.youtube.com/watch?v=') !== false) {
                $videoId = extractVideoId($message);
                if ($videoId) {
                    $videoUrl = "https://lt.epicsite.xyz/videodata/non-hls.php?id={$videoId}&dl=dl&itag=18";
                    echo "<div><b>{$name}:</b> <br> <video controls><source src='{$videoUrl}' type='video/mp4'></video> <br> (Sent on: {$timestamp})</div>";
                    echo "<hr>";
                }
            } elseif (strpos($message, 'https://lt.epicsite.xyz/watch/?v=') !== false) {
                $videoId = extractVideoId($message);
                if ($videoId) {
                    $videoUrl = "https://lt.epicsite.xyz/videodata/non-hls.php?id={$videoId}&dl=dl&itag=18";
                    echo "<div><b>{$name}:</b> <br> <video controls><source src='{$videoUrl}' type='video/mp4'></video> <br> (Sent on: {$timestamp})</div>";
                    echo "<hr>";
                }
            } else {
                echo "<div><b>{$name}:</b> {$message} (Sent on: {$timestamp})</div>";
                echo "<a href='../messages/reply.php?id=" . urlencode($id) . "'>Reply</a>";
                echo "<hr>";
            }
        }
    } else {
        echo "No messages.";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
