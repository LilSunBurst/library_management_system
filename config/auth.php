<?php
/**
 * Check if session has started, if not start a new session.
 */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Checks if the user is authenticated by checking if the 'user_id' session variable is set.
 *
 * @return bool Returns true if the 'user_id' session variable is set, false otherwise.
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirects to a specified location if the user is not authenticated.
 *
 * @param string $location The location to redirect to.
 */
function redirectIfNotAuthenticated($location) {
    if (!isAuthenticated()) {
        header("Location: $location");
        exit();
    }
}