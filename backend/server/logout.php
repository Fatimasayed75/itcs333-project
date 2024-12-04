<?php

session_start();
session_destroy();
header('Location: ../../frontend/templates/layout/signbase.php');