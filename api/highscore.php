<?php

    class Highscore {
        private $isValid = TRUE;
        private $connection;

        function __construct($connection, $token) {
            if (checkToken($connection, $token)) { //check if user is authenticated

                $this->connection = $connection;
                $this->token = $token;
                $this->player = get_player_data($this->connection, $this->token);

                if (isset($_GET["gamename"])) { $this->gamename = $_GET["gamename"]; }
                if (isset($_GET["highscore"])) { $this->highscore = $_GET["highscore"]; }
            } else {
                echo echo_result(106, 'User must be logged in');
                $this->isValid = FALSE;
            }
        }

        function get_highscores() {
            if ($this->isValid) {
                if (isset($this->gamename)) {
                    $sql = "SELECT * FROM highscores WHERE game_name = ?";
                    $stmt = mysqli_stmt_init($this->connection);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo_result(400, 'Game doesn\'t exist');
                        return FALSE;
                    } else {
                        mysqli_stmt_bind_param($stmt, "s", $this->gamename);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        $return_arr = array();
                        $counter = 0;
                        while ($row = mysqli_fetch_assoc($res)) {
                            $return_arr[$counter] = $row;
                            $counter++;
                        }
                        echo echo_result(450, $return_arr);
                    }
                } else {
                    echo echo_result(401, 'Gamename is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function update_highscore() {
            if ($this->isValid) {
                if (isset($this->gamename) && isset($this->highscore)) {
                    $sql = "SELECT * FROM highscores WHERE game_name = ?";
                    $stmt = mysqli_stmt_init($this->connection);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo_result(400, 'Game doesn\'t exist');
                        return FALSE;
                    } else {
                        mysqli_stmt_bind_param($stmt, "s", $this->gamename);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        $updated_score = FALSE;

                        while ($row = mysqli_fetch_assoc($res)) {
                            if ($this->highscore > $row["score"] && $updated_score == FALSE) {
                                $sql = "UPDATE highscores SET player_name = ?, score = ? WHERE place = ? AND game_name = ?";
                                $stmt = mysqli_stmt_init($this->connection);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    echo_result(400, 'This can\'t fail... Please send this message to david.ilchmann@brentwood.ca: "highscore.php / line 47" and please describe what you did.');
                                    return FALSE;
                                } else {
                                    mysqli_stmt_bind_param($stmt, "ssss", $player["username"], $this->highscore, $row["place"], $this->gamename);
                                    $res = mysqli_stmt_get_result($stmt);
                                    $updated_score = TRUE;
                                    $return_arr = array();
                                    $counter = 0;
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        $return_arr[$counter] = $row;
                                        $counter++;
                                    }
                                    return $return_arr;
                                }
                            }
                        }
                        if (!$updated_score) {
                            echo echo_result(405, 'The high score isn\'t big enough');
                        }
                    }
                } else {
                    echo echo_result(402, 'Gamename and Highscore is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }
    }
    