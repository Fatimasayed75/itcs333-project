<html>
    <head>
        <title>Hello World</title>
    </head>

    <body>
        <?php
            echo "Hello, World!<br>";

            

            $footerPath = 'frontend/templates/components/footer.html';
            if (file_exists($footerPath)) {
                include $footerPath;
            } else {
                echo "Footer file not found.";
            }
        ?>
    </body>
</html>