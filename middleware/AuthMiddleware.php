<?php
class AuthMiddleware { public static function handle(){ Auth::requireLogin(); } }