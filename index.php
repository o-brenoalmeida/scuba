<?php
session_start();
include 'boot.php';

new Routes();

Auth::authUser() ? Routes::auth_routes() : Routes::guest_routes();