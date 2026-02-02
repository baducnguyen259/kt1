<?php
require_once(__DIR__ . "/auth.php");

function adminOnly() {
    authenticate(['admin']);
}