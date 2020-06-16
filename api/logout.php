<?php

    function logout($connection, $token) {
        deleteToken($connection, $token);
        echo echo_result(150, 'You are successfully logged out');
    }
    

    