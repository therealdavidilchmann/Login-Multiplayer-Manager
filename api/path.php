<?php
    class Path {
        function getPath() {
            $path = explode(":", $_GET["path"]);
            /*
            if (isset($path[2])) {
                if ($path[2] == "" && $path[0] != "register") {
                    $return_array = array("file"=>"login", "function"=>"login", "token"=>"");
                } else {
                    $return_array = array("file"=>$path[0], "function"=>$path[1], "token"=>$path[2]);
                }
            } else {
                if ($path[0] != "register") {
                    $return_array = array("file"=>"login", "function"=>"login", "token"=>"");
                } else {
                    $return_array = array("file"=>$path[0], "function"=>$path[1], "token"=>"");
                }
            }*/
            if (isset($path[2])) {
                $return_array = array("file"=>$path[0], "function"=>$path[1], "token"=>$path[2]);
            } else {
                $return_array = array("file"=>$path[0], "function"=>$path[1], "token"=>"");
            }
            
            
            return $return_array;
        }
    }




    