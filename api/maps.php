<?php
    
    class Map {
        private $isValid = TRUE;
        private $connection;

        function __construct($connection) {

            $this->connection = $connection;

            if (isset($_GET["level"])) { $this->level = $_GET["level"]; }
            if (isset($_GET["map"])) { $this->map = $_GET["map"]; }
            
        }

        function get_map() {
            if ($this->isValid) {
                if (isset($this->level)) {
                    $sql = "SELECT * FROM levels WHERE level = ?";
                    $stmt = mysqli_stmt_init($this->connection);
        
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        
                        echo echo_result(300, 'SQL statement failed');
                    } else {
                        mysqli_stmt_bind_param($stmt, "s", $this->level);
                        mysqli_stmt_execute($stmt);
        
                        $result = mysqli_stmt_get_result($stmt);
                        $row = mysqli_fetch_assoc($result);
        
                        if ($row > 0) {
                            
                            echo echo_result(350, array('level'=>$row["level"], 'map'=>$row["map"]));
                        } else {
                            
                            echo echo_result(301, "This level is not created yet.");
                        }
                    }
                } else {
                    echo echo_result(302, 'Level is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }

        function create_map() {
            if ($this->isValid) {
                if (isset($this->map) && isset($this->level)) {
                    $sql = "INSERT INTO levels (level, map) VALUES (?, ?)";
                    $stmt = mysqli_stmt_init($this->connection);
        
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        
                        echo echo_result(300, 'SQL statement failed');
                    } else {
                        mysqli_stmt_bind_param($stmt, "ss", $this->level, $this->map);
                        mysqli_stmt_execute($stmt);
        
                        $result = mysqli_stmt_get_result($stmt);
                        $row = mysqli_fetch_assoc($result);
                        
                        echo echo_result(351, 'Successfully created Level ' . $row['level']);
                    }
                } else {
                    echo echo_result(303, 'Level and Map is required.');
                }
            } else {
                echo echo_result(600, 'An error occured, your data is invalid.');
            }
        }
    }

    

?>