<?php
session_start();
session_destroy(); // Sterge toate datele din sesiune
header("Location: login.php"); // Te trimite inapoi la login
exit;