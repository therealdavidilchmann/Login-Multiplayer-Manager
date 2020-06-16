<?php
    $servername = "localhost";
    $dbname = "id11715633_game";
    $dbname_multiplayer = "id11715633_multiplayer";
    $user = "id11715633_root";
    $user_multiplayer = "id11715633_root2";
    $password = "Aa12[]bcf$%osaj";

    $conn = mysqli_connect($servername, $user, $password, $dbname);
    $conn_multiplayer = mysqli_connect($servername, $user_multiplayer, $password, $dbname_multiplayer);

    if (!$conn || !$conn_multiplayer) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    function echo_result($code, $msg) {
        echo json_encode(
            array(
                'code'      => $code,
                'message'   => $msg
            )
        );
    }

    function checkToken($connection, $token) {
        $sql = "SELECT * FROM tokens WHERE token=?";
        $stmt = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo echo_result(mysqli_stmt_error($stmt));
            return FALSE;
        } else {
            mysqli_stmt_bind_param($stmt, "s", $token);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            $num_rows = mysqli_stmt_num_rows($stmt);
            if ($num_rows > 0) { return TRUE; } else { return FALSE; }
        }
    }

    function check_if_exists($connection, $what, $whatValue, $where) {
        $sql = "SELECT $what FROM $where WHERE $what=?";
        $stmt = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return FALSE;
        } else {
            mysqli_stmt_bind_param($stmt, "s", $whatValue);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            $num_rows = mysqli_stmt_num_rows($stmt);
            if ($num_rows > 0) { return TRUE; } else { return FALSE; }
        }
    }

    function setToken($connection, $email) {
        $token = password_hash($email, PASSWORD_DEFAULT);

        $sql = "INSERT INTO tokens (token, email) VALUES (?, ?)";
        $stmt = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return FALSE;
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $token, $email);
            mysqli_stmt_execute($stmt);
            return $token;
        }
    }

    function checkTokenExtended($connection, $token) {
        if (checkToken($connection, $token)) { echo echo_result(50, 'Token is valid'); exit(); } else { echo echo_result(0, 'Token is invalid'); }
    }

    function getEmailFromToken($connection, $token) {
        $sql = "SELECT email FROM tokens WHERE token=?";
        $stmt = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return FALSE;
        } else {
            mysqli_stmt_bind_param($stmt, "s", $token);
            mysqli_stmt_execute($stmt);
            
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                return $row["email"];
            } else {
                return "";
            }
        }
    }

    function deleteToken($connection, $token) {
        $sql = "DELETE FROM tokens WHERE token=?";
        $stmt = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return FALSE;
        } else {
            mysqli_stmt_bind_param($stmt, "s", $token);
            mysqli_stmt_execute($stmt);
            return TRUE;
        }
    }

    function get_player_data($connection, $token) {
        if (checkToken($connection, $token)) {
            $email = getEmailFromToken($connection, $token);

            if ($email != "") {
                $data = array("email"=>$email);
            
                $sql = "SELECT * FROM users WHERE email=?";
                $stmt = mysqli_stmt_init($connection);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);

                    $res = mysqli_stmt_get_result($stmt);
                    while ($row = mysqli_fetch_assoc($res)) {
                        $data["level"] = $row["level"];
                        $data["id"] = $row["uid"];
                        $data["username"] = $row["username"];
                    }
                    return $data;
                
                } else {
                    echo echo_result(100, 'Couldn\'t fetch user data');
                }
            } else {
                echo echo_result(107, 'No E-Mail for this token');
            }
        } else {
            echo echo_result(106, 'User must be logged in');
        }
    }

    function get_player_data_extended($connection, $token) {
        $data = get_player_data($connection, $token);
        echo echo_result(50, array("uid"=>$data["id"], "username"=>$data["username"]));
    }