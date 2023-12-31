<?php

session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
} else {
    header('Location: ../index.php');
    exit();
}
?>
<?php
$hitCounterFile = 'mainhitcounter.txt';
if (!file_exists($hitCounterFile)) {
    file_put_contents($hitCounterFile, '0');
}
$hitCount = (int)file_get_contents($hitCounterFile);
$hitCount++;
file_put_contents($hitCounterFile, $hitCount);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>librebook</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../css/mainsite.css">
</head>
<body>

    <section id="head">
        <img src="/images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <br>
    <div id="helloworld">
        <?php
        echo 'Welcome back ' . htmlspecialchars($username) . '!';
        ?>
        <p></p>
        <a href="deleteyou.php">Delete your account</a>
        <p></p>
        <a href="../logout.php">Logout</a><a href="../profiles/sprofile.php" style="float: right;">See my profile</a>
    </div>
    <section id="searchbar">
        <form action="../profiles/profiles.php" id="searchform" method="get">
            <input id="searchbut" type="text" placeholder="Search profiles.." name="search">
            <button type="submit">Search!<i class="fa fa-search"></i></button>
        </form>
    </section>

        <section id="messages">
            <div id="success"></div>
            <div id="error"></div>
            <div id="messageList"></div>
        </section>
        <br></br>
        <section id="messages">
        <form id="messageForm">
            <textarea id="message" name="message" rows="4" cols="50" required placeholder="let the world know what you're thinking"></textarea><p></p><button id="bootun" type="submit">Post</button>
        </form>
        </section>
    </section>
    <script>
        var userID = "<?php echo $username; ?>";
        console.log('hi',userID)
        $(document).ready(function() {
            $("#messageForm").submit(function(event) {
                event.preventDefault();
                var name = userID;
                var message_text = $("#message").val();
                $.ajax({
                    type: "POST",
                    url: "../messages/submit.php",
                    data: { name: name, message_text: message_text },
                    success: function(response) {
                        $("#name").val("");
                        $("#message").val("");
                        $("#success").text(response).fadeIn().delay(3000).fadeOut();
                    },
                    error: function(xhr, status, error) {
                        $("#error").text("An error occurred: " + error).fadeIn().delay(3000).fadeOut();
                    }
                });
            });

            setInterval(function() {
                $.ajax({
                    type: "GET",
                    url: "../messages/messages.php",
                    success: function(response) {
                        $("#messageList").html(response);
                    },
                    error: function(xhr, status, error) {
                        $("#error").text("An error occurred: " + error).fadeIn().delay(3000).fadeOut();
                    }
                });
            }, 1000);
        });
    </script>
<script src="script.js"></script>
</body>
</html>
