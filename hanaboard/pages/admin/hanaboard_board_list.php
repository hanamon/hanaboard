<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class='wrap'>
	<h1 class="wp-heading-inline">게시판 목록</h1>	
	
	<form method="post">
		<?php
		$hanaBoardListTable->display();
		?>
	</form>
</div>
