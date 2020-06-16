<?php

    class Login {
        private $isValid = TRUE;
        private $connection;

        function __construct($connection, $token="null") {
            if ($token != "null") {
                if (checkToken($connection, $token)) { //check if user is authenticated
                    echo echo_result(151, "User is already logged in");
                    $this->isValid = FALSE;
                } else {
                    echo echo_result(104, 'Login token still exists, but it is not valid. Try logging out of the game and login again');
                    $this->isValid = FALSE;
                }
            } else {
                $this->connection = $connection;

                if (isset($_GET["password"])) { $this->password = $_GET["password"]; }
                if (isset($_GET["email"])) { $this->email = $_GET["email"]; }
            }
        }

        function login() {
            if ($this->isValid) {
                if (isset($this->email) && isset($this->password)) {
                    $sql = "SELECT * FROM users WHERE email=?";
                    $stmt = mysqli_stmt_init($this->connection);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo echo_result(100, "SQL Statement Failed");
                    } else {
                        mysqli_stmt_bind_param($stmt, "s", $this->email);
                        mysqli_stmt_execute($stmt);
                        
                        $res = mysqli_stmt_get_result($stmt);
                        if ($row = mysqli_fetch_assoc($res)) {
                            $pwdCheck = password_verify($this->password, $row["password"]);
                            if ($pwdCheck == FALSE) {
                                echo echo_result(103, "The password is wrong");
            
                            } else if ($pwdCheck == TRUE) {
                                $token = setToken($this->connection, $this->email);

                                echo echo_result(150, "User is logged in. Token: " . $token);
                                
                            } else {
                                echo echo_result(103, "The password is wrong");
                            }
                        } else {
                            echo echo_result(102, "No user with this email or username");
                        }
                    }
                } else {
                    echo echo_result(101, "Email and Password are required");
                }
            } else {
                echo echo_result(100, 'An error occured, your data is invalid.');
            }
        }
    }