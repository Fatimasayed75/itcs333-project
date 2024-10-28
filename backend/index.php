<html> 
<head>
    <title>Hello World</title>
</head>

<body>
    <?php
        date_default_timezone_set('Asia/Bahrain');
        echo "Hello, World!<br>";
        
        // Include the main.php file to establish the database connection
        include 'main.php';
        include 'database/book-model.php';
        include 'database/room-model.php';
        include 'database/user-model.php';

        
        if (isset($pdo)) {

            // $room = new RoomModel($pdo, "S40-1003", null, 10, 1, 1);
            // $result = $room->save();
            // echo $result;

            $Book = new BookModel($pdo, null, 14, "S40-1003", null, '2024-10-30 11:00:00', '2024-10-30 11:30:00');
            $result = $Book->save();
            echo $result;

            // $result = $Book->expire();
            
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
