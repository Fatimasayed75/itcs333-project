<html>

<head>
    <title>Hello World</title>
</head>

<body>
    <?php
    date_default_timezone_set('Asia/Bahrain');
    echo "Hello, World!<br>";

    include 'db-connection.php';
    include 'database/book-model.php';
    include 'database/room-model.php';
    include 'database/user-model.php';

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["sign-up-btn"])) {
            $fname = $_POST["firstName"];
            $lname = $_POST["lastName"];
            $email = $_POST["email"];
            $password = $_POST["password"];

            $user = new UserModel($pdo, $fname, $email, $password, $fname, $lname, 'student', 'default.jpg');
            $user->save();
            echo "User created successfully!";
        } elseif (isset($_POST["sign-in-btn"])) {
            $email = $_POST["email"];
            $password = $_POST["password"];

            // Use a prepared statement
            $sql = "SELECT * FROM users WHERE email = :email AND password = :password";
            $stmt = $pdo->prepare($sql);

            // Bind the parameters to avoid SQL injection
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);

            // Execute the query
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                header("Location: ../frontend/templates/layout/base.php");
                exit();
            } else {
                $_SESSION['error'] = "Invalid email or password!";
                header("Location: ../frontend/templates/layout/signbase.php");
                exit();
            }
        }

    }

    /*
    $footerPath = 'frontend/templates/components/footer.html';
    if (file_exists($footerPath)) {
        include $footerPath;
    } else {
        echo "Footer file not found.";
    }
    */
    ?>
</body>

</html>