<?php

    class Register {
        private $isValid = TRUE;
        private $connection;

        function __construct($connection) {

            $this->connection = $connection;

            if (isset($_GET["email"])) { $this->email = $_GET["email"]; }
            if (isset($_GET["password"])) { $this->password = $_GET["password"]; }
            if (isset($_GET["username"])) { $this->username = $_GET["username"]; }
            
        }

        function register() {
            if ($this->isValid) {
                if (isset($this->email) && isset($this->password) && isset($this->username)) {
                    $inserting = TRUE;
                    $count = 0;
                    
                    $uid_found = FALSE;
                    while ($uid_found == FALSE) {
                        $uid = rand(1000000000, 9999999999);
                        if (check_if_exists($this->connection, "uid", $uid, "users")) { $uid_found = FALSE; } else { $uid_found = TRUE; }
                    }

                    $time_count = 0;

                    while ($inserting) {

                        if ($time_count >= 50) {
                            $username_exists = check_if_exists($this->connection, "username", $this->username, "users");
                            if ($username_exists && $count > 0) { echo echo_result(250, "Successfully created User"); $inserting=FALSE; exit(); }
                            if ($username_exists) { echo echo_result(202, "Username already exists"); $inserting=FALSE; exit(); }
                            if (check_if_exists($this->connection, "email", $this->email, "users")) { echo echo_result(203, "E-Mail already exists"); $inserting=FALSE; exit(); }
                    
                            $hashedpw = password_hash($this->password, PASSWORD_DEFAULT);
                            $level = 0;
                            $ready = FALSE;
                            $sql = "INSERT INTO users (uid, email, password, username, level, ready) VALUES (?, ?, ?, ?, ?, ?)";
                            $stmt = mysqli_stmt_init($this->connection);
                            if (mysqli_stmt_prepare($stmt, $sql)) {
                                mysqli_stmt_bind_param($stmt, "ssssss", $uid, $this->email, $hashedpw, $this->username, $level, $ready);
                                mysqli_stmt_execute($stmt);

                                $time_count = 0;
                                
                                $count+=1;
                            } else {
                                echo echo_result(200, "SQL Statement Failed");
                            }
                        } else {
                            $time_count += 1;
                        }
                    }
                } else {
                    echo echo_result(201, "Email, Username and Password are required");
                }
            } else {
                echo echo_result(204, 'An error occured, your data is invalid');
            }
        }

        function register_once() {
            $uid = 1234567890;
            $hashedpw = password_hash("david", PASSWORD_DEFAULT);
            echo $hashedpw;
            $level = 0;
            $ready = FALSE;
            $username_exists = check_if_exists($this->connection, "username", $this->username, "users");
            $a = [$uid, $this->email, $hashedpw, $this->username, $level, $ready];
            if ($username_exists) { echo echo_result(202, "Username already exists"); exit(); }

            $sql = "INSERT INTO `users` (`uid`, `email`, `password`, `username`, `level`, `ready`) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($this->connection);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssss", $a[0], $a[1], $a[2], $a[3], $a[4], $a[5]);
                mysqli_stmt_execute($stmt);

                echo "DID";
            } else {
                echo echo_result(200, "SQL Statement Failed");
            }
        }
    }

?>
