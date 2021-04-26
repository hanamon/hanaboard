<div id="hanaboard-wrap" style="background:pink">

	<h2><? echo $hanaboard_content_board; ?></h2>
	
	<!-- 게시글 전체 -->
	<div class="hanaboard-total-count">
		전체 <?php echo $post_index; ?>
	</div>

	<!-- 리스트 시작 -->
	<div class="hanaboard-list">
		<table>
			<thead>
				<tr>
					<td class="hanaboard-list-uid">번호</td>
					<td class="hanaboard-list-title">제목</td>
					<td class="hanaboard-list-user">작성자</td>
					<td class="hanaboard-list-date">작성일</td>
					<td class="hanaboard-list-view">조회</td>
				</tr>
			</thead>
			<tbody>
				<?php while ( $loop->have_posts() ): ?>
					<?php
						$loop->the_post();
						$hanaboard_content_ID    	= get_the_ID();
						$hanaboard_content_title 	= get_post_field( 'post_title', $hanaboard_content_ID );
						$hanaboard_content_author	= get_post_field( 'post_author', $hanaboard_content_ID );
						$hanaboard_content_user		= get_user_meta( $hanaboard_content_author, 'first_name', true );
						$hanaboard_content_date		= get_post_field( 'post_date', $hanaboard_content_ID );
						$hanaboard_content_board 	= get_post_meta( $hanaboard_content_ID, '_hanaboard_board_id', true );
					?>
					<?php if( $hanaboard_title == $hanaboard_content_board ): ?>
						<tr class="kboard-list-selected">
							<td class="kboard-list-uid"><?php echo $hanaboard_content_ID; ?></td>
							<td class="kboard-list-title">
								<a href="<?php echo $notice_readpost_link; ?>" title="<?php echo $hanaboard_content_title; ?>">
									<?php echo $hanaboard_content_title; ?>
								</a>
							</td>
							<td class="kboard-list-user"><?php echo $hanaboard_content_user; ?></td>
							<td class="kboard-list-date"><?php echo $hanaboard_content_date; ?></td>
							<td class="kboard-list-view">아직안됨</td>
						</tr>
					<?php endif ?>
				<?php endwhile ?>
			</tbody>
		</table>
	</div>
	<!-- 리스트 끝 -->
	
</div>