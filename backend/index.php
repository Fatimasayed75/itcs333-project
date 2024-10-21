<html> 
<head>
    <title>Hello World</title>
</head>

<body>
    <?php
        echo "Hello, World!<br>";
        
        // Include the main.php file to establish the database connection
        include 'main.php';
        include 'database/book-model.php';
        include 'database/room-model.php';
        include 'database/user-model.php';

        
        if (isset($pdo)) {

            $Book = new BookModel($pdo, null, 14, "S40-1112", null, '2021-10-01 10:00:00', '2021-10-01 12:00:00');
            // $result = $Book->save();
            $result = $Book->expire();
            echo "result= {$result}";
        } else {
            echo "Database connection is not established.";
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
