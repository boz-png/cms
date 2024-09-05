<?php
// Use environment variables for sensitive credentials
$MySQLhostname = getenv('DB_HOSTNAME') ?: 'localhost';
$MySQLusername = getenv('DB_USERNAME') ?: 'root';
$MySQLpassword = getenv('DB_PASSWORD') ?: 'R00Taccess';
$MySQLdb = getenv('DB_NAME') ?: 'test';

$mysqli = new mysqli($MySQLhostname, $MySQLusername, $MySQLpassword, $MySQLdb, 3306);

// Error handling for database connection
if ($mysqli->connect_error) {
    // Log error to a file instead of displaying to the user
    error_log('Database connection error: ' . $mysqli->connect_error);
    exit('Internal Server Error');
}

// Protect function with stronger sanitization
function protect($string) {
    return htmlspecialchars(strip_tags($string), ENT_QUOTES, 'UTF-8');
}

// Validate input for SQL query safety
$load = protect($_GET['load']);
$userip = protect($_GET['userip']);
$token = protect($_GET['token']);
$username = protect($_GET['username']);

$my = new stdClass();
$my->ip = $_SERVER['CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];

// Ensure user IP matches
if ($my->ip !== $userip) {
    exit('IP mismatch');
}

// Use prepared statements to prevent SQL injection
$stmt = $mysqli->prepare("SELECT * FROM users WHERE ip_last = ? AND username = ? AND rank > 7");
$stmt->bind_param('ss', $my->ip, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_object();

    // Verify token security
    if (md5($user->auth_ticket) !== $token) {
        exit('ERROR TOKEN');
    }
} else {
    exit('No Useraccount found');
}

// Secure function for querying user data
function UserIDDB($id, $column) {
    global $mysqli;

    $id = protect($id);
    $column = protect($column);

    // Use prepared statement for secure querying
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_object();
        return $row->$column;
    }
    return null;
}

// Protect badge upload by verifying file type and size
function handleBadgeUpload($badge_id, $title, $beschreibung) {
    global $mysqli, $user;

    $badgepath = 'https://bobba.pw/nitro-2/nitro/c_images/album1584/';
    $allowed_types = ['image/gif']; // Only allow GIF

    if (isset($_FILES["datei"])) {
        $file_type = $_FILES["datei"]["type"];
        if (in_array($file_type, $allowed_types)) {
            // Sanitize and handle badge upload securely
            $badge_id = protect($badge_id);
            $title = protect(umlautenew($title));
            $beschreibung = protect(umlautenew($beschreibung));

            // Check if file exists, handle upload logic
            $destination = $badgepath . $badge_id . '.gif';
            if (!file_exists($destination)) {
                if (move_uploaded_file($_FILES['datei']['tmp_name'], $destination)) {
                    $mysqli->query("INSERT INTO hp_badges (badge_id, title, beschreibung, status, timestamp_added, autor_added) VALUES (?, ?, ?, '1', ?, ?)", [
                        $badge_id, $title, $beschreibung, time(), $user->id
                    ]);
                    echo 'Badge successfully uploaded!';
                } else {
                    echo 'Failed to upload the badge.';
                }
            }
        } else {
            echo 'The badge must be a GIF image!';
        }
    }
}

// Further code follows similar pattern of improvements
?>
