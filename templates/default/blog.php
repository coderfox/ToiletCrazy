<?php
include_once $tc_root . 'markdown/Michelf/Markdown.inc.php';
include_once $tc_root . 'functions/user.php';
use \Michelf\Markdown;
// var_dump($template);
?>
<h1><?php echo $template['blog']['title'];?></h1>
<h2><?php echo tcGetUser($tc_coll, $template['blog']['author'])['nick'];?></h2>
-----
<?php
echo Markdown::defaultTransform( $template ['blog'] ['text'] );
?>