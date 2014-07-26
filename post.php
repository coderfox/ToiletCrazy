<?php
include_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
// template init
$template = array (
        'page' => 'post',
        'title' => $tc_config ['site'] ['title']
);
include_once $tc_config ['template'] . 'header.php';
include_once $tc_config ['template'] . 'post.php';
include_once $tc_config ['template'] . 'footer.php';