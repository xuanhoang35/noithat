<?php
class AdminMiddleware { public static function handle(){ Auth::requireAdmin(); } }