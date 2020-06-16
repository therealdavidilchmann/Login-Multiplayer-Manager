<?php
    require 'path.php';
    require 'config.php';
    require 'highscore.php';
    require 'login.php';
    require 'logout.php';
    require 'maps.php';
    require 'multiplayer.php';
    require 'register.php';


    //highscore login logout maps multiplayer register


    $path = new Path();

    if ($path->getPath()["file"] == "functions") {
        
        if ($path->getPath()["function"] == "check_token") { checkTokenExtended($conn, $path->getPath()["token"]); }
        if ($path->getPath()["function"] == "get_player_data") { get_player_data_extended($conn, $path->getPath()["token"]); }
        else { echo echo_result(0, 'No function called ' . $path->getPath()["function"]); }

    }
    else if ($path->getPath()["file"] == "highscore") {
        $highscore = new Highscore($conn, $path->getPath()["token"]);

        if ($path->getPath()["function"] == "get_highscores") { $highscore->get_highscores(); }
        else if ($path->getPath()["function"] == "update_highscores") { $highscore->update_highscores(); }
        else { echo echo_result(0, 'No function called ' . $path->getPath()["function"]); }

    }
    else if ($path->getPath()["file"] == "login") {
        $login = new Login($conn);

        if ($path->getPath()["function"] == "login") { $login->login(); }
        else { echo echo_result(0, 'No function called ' . $path->getPath()["function"]); }

    }
    else if ($path->getPath()["file"] == "logout") {

        if ($path->getPath()["function"] == "logout") { logout($conn, $path->getPath()["token"]); }
        else { echo echo_result(0, 'No function called ' . $path->getPath()["function"]); }

    }
    else if ($path->getPath()["file"] == "maps") {
        $map = new Map($conn, $path->getPath()["token"]);
        
        if ($path->getPath()["function"] == "get_map") { $map->get_map(); }
        else if ($path->getPath()["function"] == "create_map") { $map->create_map(); }
        else { echo echo_result(0, 'No function called ' . $path->getPath()["function"]); }

    }
    else if ($path->getPath()["file"] == "multiplayer") {
        $multiplayer = new Multiplayer($conn, $conn_multiplayer, $path->getPath()["token"]);

        if ($path->getPath()["function"] == "create") { $multiplayer->create_new_multiplayer_game(); }
        else if ($path->getPath()["function"] == "add_player") { $multiplayer->add_player(); }
        else if ($path->getPath()["function"] == "check_if_user_is_in_multiplayer_game") { $multiplayer->check_if_user_is_in_multiplayer_game(); }
        else if ($path->getPath()["function"] == "check_if_finished") { $multiplayer->check_if_finished(); }
        else if ($path->getPath()["function"] == "get_winner") { $multiplayer->get_winner(); }
        else if ($path->getPath()["function"] == "get_players") { $multiplayer->get_players(); }
        else if ($path->getPath()["function"] == "get_player_from_id") { $multiplayer->get_player_from_id(); }
        else if ($path->getPath()["function"] == "leave") { $multiplayer->leave(); }
        else if ($path->getPath()["function"] == "get_host") { $multiplayer->get_host(); }
        else if ($path->getPath()["function"] == "delete_game") { $multiplayer->delete_game(); }
        else if ($path->getPath()["function"] == "remove_player") { $multiplayer->remove_player(); }
        else if ($path->getPath()["function"] == "get_game_name") { $multiplayer->get_game_name(); }
        else if ($path->getPath()["function"] == "get_game_information") { $multiplayer->get_game_information(); }
        else if ($path->getPath()["function"] == "switch_user_ready") { $multiplayer->switch_user_ready(); }
        else { echo echo_result(0, 'No function called ' . $path->getPath()["function"]); }

    }
    else if ($path->getPath()["file"] == "register") {
        $register = new Register($conn);

        if ($path->getPath()["function"] == "register") { $register->register_once(); }
        else { echo echo_result(0, 'No function called ' . $path->getPath()["function"]); }

    }