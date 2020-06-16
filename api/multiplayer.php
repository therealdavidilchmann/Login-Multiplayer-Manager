<?php

    class Multiplayer {
        private $player;
        private $isValid = TRUE;
        private $MAX_PLAYERS_PER_SESSION = 30;

        function __construct($connection, $connection_multiplayer, $token) {
            if (checkToken($connection, $token)) { //check if user is authenticated

                $this->connection = $connection;
                $this->connection_multiplayer = $connection_multiplayer;
                $this->token = $token;
                $this->player = get_player_data($this->connection, $this->token);

                if (isset($_GET["gameId"])) { $this->gameId = $_GET["gameId"]; }
                if (isset($_GET["highscore"])) { $this->highscore = $_GET["highscore"]; }
                if (isset($_GET["multiplayer_player_id"])) { $this->multiplayer_player_id = $_GET["multiplayer_player_id"]; }
                if (isset($_GET["index"])) { $this->index = $_GET["index"]; }
                if (isset($_GET["title"])) { $this->title = $_GET["title"]; }
                if (isset($_GET["ready"])) { $this->ready = $_GET["ready"]; }
                
            } else {
                echo echo_result(106, 'User must be logged in');
                $this->isValid = FALSE;
            }
        }

        function create_new_table() {
            if ($this->isValid) {
                $players = array();
                for ($i=0; $i < $this->MAX_PLAYERS_PER_SESSION; $i++) {
                    $c = $i + 1;
                    array_push($players, "player$c VARCHAR(100) NOT NULL");
                }
                $new_players = implode(", ", $players);
                $sql = "CREATE TABLE multiplayer(id VARCHAR(10) NOT NULL, host VARCHAR(100) NOT NULL, $new_players)";
                mysqli_query($this->connection, $sql);
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function create_new_multiplayer_game() {
            if ($this->isValid) {
                if (isset($this->title)) {
                    $game_id = rand(1000000000, 9999999999);
                    $placeholders = array(0, "0", "0000000000", "?");

                    $sql = "INSERT INTO multiplayer (id, host, title, finished, highscore, winner, player1, player2, player3, player4, player5, player6, player7, player8, player9, player10, player11, player12, player13, player14, player15, player16, player17, player18, player19, player20, player21, player22, player23, player24, player25, player26, player27, player28, player29, player30) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo echo_result(500, "SQL Statement Failed");
                    } else {
                        mysqli_stmt_bind_param($stmt, "ssssssssssssssssssssssssssssssssssss", $game_id, $this->player["id"], $this->title, $placeholders[0], $placeholders[1], $placeholders[2], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3], $placeholders[3]);
                        mysqli_stmt_execute($stmt);
                        
                        echo echo_result(550, "Successfully created multiplayer game. ID: " . $game_id);
                    }
                } else {
                    echo echo_result(501, 'Title is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function add_player() {
            if ($this->isValid) {
                if (isset($this->gameId)) {
                    $index_of_last_player_in_session = 1;

                    $sql = "SELECT * FROM multiplayer WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($res)) {
                            for ($i=0; $i < $this->MAX_PLAYERS_PER_SESSION; $i++) {
                                $c = $i + 1;
                                if ($row["player$c"] == "?") {

                                    $sql = "UPDATE multiplayer SET player$c=? WHERE id=?";
                                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                                    if (mysqli_stmt_prepare($stmt, $sql)) {
                                        mysqli_stmt_bind_param($stmt, "ss", $this->player["id"], $this->gameId);
                                        mysqli_stmt_execute($stmt);

                                        echo echo_result(551, 'Successfully added User to multiplayer game');
                                        exit();
                                    } else {
                                        echo echo_result(553, 'Failed to add User to the multiplayer game');
                                        exit();
                                    }
                                } else if ($row["player$c"] == $this->player["id"]) {
                                    echo echo_result(502, 'User is already signed up in this multiplayer game');
                                    exit();
                                }
                            }
                        }
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Game Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function check_if_finished() {
            if ($this->isValid) {
                if (isset($this->gameId) && isset($this->highscore)) {
                    $sql = "SELECT finished FROM multiplayer WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($res)) {
                            if ($row["finished"] < $this->highscore) {
                                $this->insert_winner();
                            } else {
                                echo_result(553, 'Somebody got a higher score');
                            }
                        }
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Game Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function insert_winner() {
            if (isset($this->highscore)) {
                $sql = "UPDATE multiplayer SET finished=?, highscore=?, winner=? WHERE id=?";
                $stmt = mysqli_stmt_init($this->connection_multiplayer);

                $placeholders = array(1);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ssss", $placeholders[0], $this->highscore, $this->player["id"], $this->gameId);
                    mysqli_stmt_execute($stmt);

                    echo echo_result(556, 'Nobody won the game yet, you won');
                } else {
                    echo echo_result(500, 'Something went wrong while storing that the player won');
                }
            } else {
                echo echo_result(504, 'Highscore is required.');
            }
        }

        function get_winner() {
            if ($this->isValid) {
                if (isset($this->gameId)) {
                    $sql = "SELECT finished, highscore, winner FROM multiplayer WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($res)) {
                            if ($row["finished"] != 0) {
                                echo echo_result(555, array('winner'=>$row["winner"], 'highscore'=>$row["highscore"]));
                            } else {
                                echo echo_result(554, 'Nobody won the game yet');
                            }
                        }
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Game Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function check_if_user_is_in_multiplayer_game() {
            if ($this->isValid) {
                $found = FALSE;
                $sql = "SELECT * FROM multiplayer";
                $stmt = mysqli_stmt_init($this->connection_multiplayer);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_execute($stmt);

                    $res = mysqli_stmt_get_result($stmt);
                    while ($row = mysqli_fetch_assoc($res)) {
                        if ($row["host"] == $this->player["id"]) {
                            echo echo_result(557, 'User is already signed up in a multiplayer game. ID: ' . $row["id"]);
                            $found = TRUE;
                            exit();
                        }
                        for ($i=0; $i < $this->MAX_PLAYERS_PER_SESSION; $i++) { 
                            $c = $i+1;
                            if ($row["player$c"] == $this->player["id"] && $found == FALSE) {
                                echo echo_result(557, 'User is already signed up in a multiplayer game. ID: ' . $row["id"]);
                                $found = TRUE;
                                exit();
                            }
                        }
                    }
                    if ($found == FALSE) {
                        echo echo_result(558, 'User isn\'t signed up in any multiplayer game.');
                    }
                } else {
                    echo echo_result(500, 'A weird error occured.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function get_players() {
            if ($this->isValid) {
                if (isset($this->gameId)) {
                    $sql = "SELECT * FROM multiplayer WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($res)) {
                            $players = array();
                            for ($i=0; $i < $this->MAX_PLAYERS_PER_SESSION; $i++) {
                                $c = $i + 1;

                                if ($row["player$c"] == "?") {
                                    array_push($players, array("id"=>"?", "ready"=>TRUE));
                                } else {
                                    $sql2 = "SELECT * FROM users WHERE uid=?";
                                    $stmt2 = mysqli_stmt_init($this->connection);

                                    if (mysqli_stmt_prepare($stmt2, $sql2)) {
                                        mysqli_stmt_bind_param($stmt2, "s", $row["player$c"]);
                                        mysqli_stmt_execute($stmt2);

                                        $res2 = mysqli_stmt_get_result($stmt2);
                                        while ($row2 = mysqli_fetch_assoc($res2)) {
                                            array_push($players, array("id"=>$row["player$c"], "ready"=>$row2["ready"]));
                                        }
                                    } else {
                                        echo echo_result(600, 'An error occured, your data is invalid');
                                    }
                                }
                            }
                            echo echo_result(559, $players);
                        }
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Game Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function get_game_information() {
            if ($this->isValid) {
                if (isset($this->gameId)) {
                    $sql = "SELECT title, host FROM multiplayer WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($res)) {
                            echo echo_result(564, array('title'=>$row["title"], 'host'=>$row["host"]));
                        }
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Game Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function get_game_name() {
            if ($this->isValid) {
                if (isset($this->gameId)) {
                    $sql = "SELECT title FROM multiplayer WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($res)) {
                            echo echo_result(563, $row["title"]);
                        }
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Game Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function get_player_from_id() {
            if ($this->isValid) {
                if (isset($this->multiplayer_player_id)) {
                    $sql = "SELECT username FROM users WHERE uid=?";
                    $stmt = mysqli_stmt_init($this->connection);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->multiplayer_player_id);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($res)) {
                            echo echo_result(560, $row["username"]);
                        }
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Player Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }
        
        function leave() {
            if ($this->isValid) {
                $placeholders = ["?", 0];
                if (isset($this->gameId)) {
                    $sql = "SELECT * FROM multiplayer WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);
    
                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);
    
                        $res = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($res)) {
                            for ($i=0; $i < $this->MAX_PLAYERS_PER_SESSION; $i++) {
                                $c = $i + 1;
                                if ($row["player$c"] == $this->player["id"]) {
    
                                    $sql = "UPDATE multiplayer SET player$c=? WHERE id=?";
                                    $stmt = mysqli_stmt_init($this->connection_multiplayer);
    
                                    if (mysqli_stmt_prepare($stmt, $sql)) {
                                        mysqli_stmt_bind_param($stmt, "ss", $placeholders[0], $this->gameId);
                                        mysqli_stmt_execute($stmt);
    
                                        echo echo_result(551, 'Successfully removed User from multiplayer game');
                                    } else {
                                        echo echo_result(550, 'Failed to remove User from the multiplayer game');
                                    }
                                }
                            }
                        }
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Game Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function get_host() {
            if ($this->isValid) {
                if (isset($this->gameId)) {
                    $sql = "SELECT host FROM multiplayer WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);

                        $res = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($res)) {
                            echo echo_result(561, "$row[host]");
                            exit();
                        }
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Game Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function delete_game() {
            if ($this->isValid) {
                if (isset($this->gameId)) {
                    $sql = "DELETE FROM multiplayer WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);

                        echo echo_result(562, "Successfully deleted game");
                        exit();
                        
                    } else {
                        echo echo_result(500, 'This game is not created yet');
                    }
                } else {
                    echo echo_result(501, 'Game Id is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function remove_player() {
            if ($this->isValid) {
                if (isset($this->gameId) && isset($this->index)) {
                    $sql = "UPDATE multiplayer SET player" . $this->index . " = '?' WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection_multiplayer);

                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $this->gameId);
                        mysqli_stmt_execute($stmt);

                        echo echo_result(562, "Successfully removed User");
                        exit();
                    } else {
                        echo echo_result(500, 'This User is not in this game');
                    }
                } else {
                    echo echo_result(501, 'Game Id and index are required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function switch_user_ready() {
            if ($this->isValid) {
                if (isset($this->ready)) {
                    $sql = "UPDATE users SET ready=? WHERE id=?";
                    $stmt = mysqli_stmt_init($this->connection);

                    $ready = TRUE;
                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "ss", $ready, $this->player["id"]);
                        mysqli_stmt_execute($stmt);

                        echo echo_result(562, "Successfully switched user to ready=".$this->ready);
                        exit();
                    } else {
                        echo echo_result(500, 'This User is not in this game');
                    }
                } else {
                    echo echo_result(501, 'Ready is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }
    }


    

    