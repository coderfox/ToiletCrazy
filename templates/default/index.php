<?php
include_once $tc_root . 'functions/user.php';
foreach ( $template ['blogs'] as $i ) {
    ?>
<tr>
	<td><a href="blog.php?id=<?php echo $i['_id'];?>"><?php echo $i['title'];?></a></td>
	<td><?php echo tcGetUser($tc_coll, $i['author'])['nick'];?></td>
</tr><br/>
<?php
}
?>