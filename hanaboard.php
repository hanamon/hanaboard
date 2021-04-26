<?php
/**
 * Plugin Name: HanaBoard
 * Plugin URI: http://parkhana.com/
 * Description: 하나보드는 <strong>워드프레스의 개발원칙</strong>을 지킨 한국형 게시판입니다.
 * Version: 1.0.0
 * Author: 하나몬(Hanamon)
 * Author URI: http://parkhana.com/
 */

// 직접 액세스하면 종료
if ( ! defined( 'ABSPATH' ) ) exit;

class HanaBoard {
	
	// 프로퍼티 생성
	public $POST_TYPE_Board;
    public $POST_TYPE_Content;
	
	// __construct() 생성자 메소드로 초기화
	public function __construct() {		
		// 변수 선언
		$this->POST_TYPE_Board   = 'hanaboard';
		$this->POST_TYPE_Content = 'hanaboard_content';
		
		// 메소드 실행
		add_action( 'init', Array($this, 'add_hanaBoard_post_type_fn') );
		add_filter( 'pre_get_posts', Array($this, 'hanaboard_post_types_admin_order') );
		
		add_filter( 'manage_' . $this->POST_TYPE_Board . '_posts_columns', Array($this, 'columns_hanaboard') );
		add_action( 'manage_' . $this->POST_TYPE_Board . '_posts_custom_column', Array($this, 'smashing_hanaboard_column'), 10, 2 );
		
		add_filter( 'manage_' . $this->POST_TYPE_Content . '_posts_columns', Array($this, 'columns_hanaboard_content') );
		add_action( 'manage_' . $this->POST_TYPE_Content . '_posts_custom_column', Array($this, 'smashing_hanaboard_content_column'), 10, 2 );

		add_action( 'add_meta_boxes', Array($this, 'add_custom_meta_box_hanaboard') );
		add_action( 'save_post', Array($this, 'save_hanaboard_skin_meta_box_data') );
		
		add_action( 'add_meta_boxes', Array($this, 'add_custom_meta_box_hanaboard_content') );
		add_action( 'save_post', Array($this, 'save_hanaboard_content_basic_setting_meta_box_data') );
		
		add_shortcode('hanaboard', Array($this, 'hanaboard_builder') );
	}

	/*-------------------------------------
	 *
	 * 하나보드 게시판 & 게시글 - 포스트 타입 생성
	 *
	 --------------------------------------*/
	// 하나보드 게시판 & 게시글 포스트 타입 생성
	public function add_hanaBoard_post_type_fn() {
		
		$type_board_labels = Array(
			'name'               => '하나보드 게시판',
			'add_new'            => '게시판 생성',			
			'add_new_item'       => '게시판 생성',
			'edit_item'          => '게시판 수정',
			'search_items'       => '게시판 검색',
			'not_found'          => '게시판이 없습니다.',
			'not_found_in_trash' => '휴지통에 게시판이 없습니다.',
		);
		$type_board_args = Array(
			'labels'        => $type_board_labels,
			'description'   => '하나보드 게시판 및 게시판 별 데이터 보관',
			'public'        => true,
			'hierarchical'	=> true,
			'menu_position' => 5,
			'supports'      => Array( 'title' ),
			'has_archive'   => true,
		);
		
		// 하나보드 게시판 포스트 타입 생성
		register_post_type( $this->POST_TYPE_Board, $type_board_args ); 
		
		$type_content_labels = Array(
			'name'               => '하나보드 게시글',
			'add_new'            => '게시글 생성',			
			'add_new_item'       => '게시글 생성',
			'edit_item'          => '게시글 수정',
			'search_items'       => '게시글 검색',
			'not_found'          => '게시글이 없습니다.',
			'not_found_in_trash' => '휴지통에 게시글이 없습니다.',
		);
		$type_content_args = Array(
			'labels'        => $type_content_labels,
			'description'   => '하나보드 게시물 및 게시물 별 데이터 보관',
			'public'        => true,
			'hierarchical'	=> true,
			'menu_position' => 5,
			'supports'      => Array( 'title', 'thumbnail' ),
			'has_archive'   => true,
		);
		
		// 하나보드 게시글 포스트 타입 생성
		register_post_type( $this->POST_TYPE_Content, $type_content_args ); 

	}

	// 하나보드 게시판 & 게시글 DESC 날짜별로 관리자에서 출력
	public function hanaboard_post_types_admin_order( $wp_query ) {
		if( is_admin() ) {
			// 쿼리에서 게시물 유형 가져 오기
			$post_type = $wp_query->query['post_type'];
			
			if( $post_type == $this->POST_TYPE_Board || $post_type == $this->POST_TYPE_Content ) {
				$wp_query->set('orderby', 'date');
				$wp_query->set('order', 'DESC');
			}
		}
	}

	/*-------------------------------------
	 *
	 * 하나보드 게시판 & 게시글 - 포스트 리스트 칼럼 수정
	 *
	 --------------------------------------*/ 
	// 하나보드 게시판 목록 칼럼 헤더 수정
	public function columns_hanaboard( $columns ) {
		unset( $columns );
		$columns['cb'] 			= '<input type="checkbox">';	
		$columns['image']		= '썸네일';
		$columns['title'] 		= '게시판 이름';
		$columns['skin'] 		= '스킨';
		$columns['shortcode'] 	= '숏코드';
		$columns['author'] 		= '작성자';
		$columns['date'] 		= '생성일';
		return $columns;
	}
	
	// 하나보드 게시판 목록 칼럼 바디 수정
	public function smashing_hanaboard_column( $column, $post_id ) {
		// Image column
		if( 'image' === $column ) {
			echo get_the_post_thumbnail( $post_id, Array(80, 80) );
		}
		
		// title column
		if( 'title' === $column ) {
			echo get_post_field( 'post_tilte', $post_id );
		}
		
		// skin column
		if( 'skin' === $column ) {
			$skin = get_post_meta( $post_id, '_hanaboard_skin', true );
			echo $skin;
		}

		// shortcode column
		if( 'shortcode' === $column ) {
			$shortcode = '[hanaboard id="' . $post_id . '"]';
			echo $shortcode;
		}
		
		// author column
		if( 'author' === $column ) {
			$user_ID = get_post_field( 'post_author', $post_id );
			$user_data = get_userdata( $user_ID );
			echo $display_name = $user_data->display_name;	
		}	
	}

	// 하나보드 게시글 목록 칼럼 헤더 수정
	public function columns_hanaboard_content( $columns ) {
		unset( $columns );
		$columns['cb'] 			= '<input type="checkbox">';
		$columns['id'] 			= 'ID';
		$columns['title'] 		= '게시글 이름';
		$columns['board'] 		= '게시판 이름';
		$columns['author'] 		= '작성자';
		$columns['date'] 		= '생성일';
		return $columns;
	}
	
	// 하나보드 게시글 목록 칼럼 바디 수정
	public function smashing_hanaboard_content_column( $column, $post_id ) {
		// ID
		if( 'id' === $column ) {
			echo $post_id;
		}
		
		// title column
		if( 'title' === $column ) {
			echo get_post_field( 'post_tilte', $post_id );
		}
		
		// board column
		if( 'board' === $column ) {
			$board_title = get_post_meta( $post_id, '_hanaboard_board_id', true );
			if( $board_title != NULL ){
				echo $board_title;
			}
			else{
				echo '게시판 연결 없음';
			}
			
		}
		
		// author column
		if( 'author' === $column ) {
			$user_ID = get_post_field( 'post_author', $post_id );
			$user_data = get_userdata( $user_ID );
			echo $display_name = $user_data->display_name;	
		}
	}

	/*-------------------------------------
	 *
	 * 하나보드 게시판 & 게시글 - 포스트 메타 박스 생성 : 기본 설정
	 *
	 --------------------------------------*/
	// 게시판 메타 상자 만들기 (참고 : 메타 상자 != 사용자 지정 메타 필드)
	public function add_custom_meta_box_hanaboard( $post_id ) {
		add_meta_box(
			'hanaboard-basic-setting', 									// 메타 박스 'id' 속성
			'게시판 기본 설정', 												// 메타 박스 제목
			Array($this, 'hanaboard_basic_setting_meta_box_callback'), 	// 콜백 함수
			'hanaboard', 												// 포스트 타입 ( 생략하면 모든 포스트 타입에 출력됨)
			'normal',													// $context
			'high'                         								// $priority
		);
	}
	
	// 게시판 사용자 정의 양식 필드 표시
	public function hanaboard_basic_setting_meta_box_callback( $post ) {
		// 나중에 확인할 수 있도록 임시 필드를 추가하십시오.
		wp_nonce_field( 'hanaboard_skin_nonce', 'hanaboard_skin_nonce' );
	
		// 하나보드 게시판 포스트 메타 박스 벨류 가져오기
		$value_skin = get_post_meta( $post->ID, '_hanaboard_skin', true );
		/*echo '<strong>- $value_skin : </strong>' . $value_skin . '<br />';*/
		
		/*---------------------------- 
		*
		* skin 하위 폴더 모두 가져오기
		* 
		----------------------------*/
		// 폴더명 지정
		$dir_skin = __DIR__ . '/skin';
		/*echo '<strong>- $dir_skin : </strong>' . $dir_skin . '<br /><br />';*/
		
		// 핸들 획득
		$handle = opendir($dir_skin);
		
		// 저장할 폴더 배열 생성
		$folder = Array();
		
		// 디렉터리에 포함된 파일을 저장한다. 
		while( false !== ($folderName = readdir($handle)) ) {
			if( $folderName == "." || $folderName == ".." ){
				continue;
			}
			
			// 폴더인 경우만 목록에 추가한다. ( 파일인 경우 is_file() 사용)
			if( is_dir( $dir_skin . "/" . $folderName ) ) {
				$folder[] = $folderName;
			}
		}

		// 핸들 해제
		closedir($handle);
		
		// 정렬 sort 사용 (역순으로 정렬하려면  rsort 사용)
		sort($folder);
		
		// 파일명을 출력한다.
		/*echo '<strong>skin 폴더 모두 출력</strong><br />';*/
		foreach( $folder as $skin_item ) {
			/*echo '-' . $skin_item . '<br />';*/
		}
		
		// 메타 박스 출력
		echo '<p><strong>게시판 숏코드 : </strong>[hanaboard id="' . ($post->ID) . '"]</p>';
		
		if( !$value_skin ) {
			$value_skin = 'default';
		}
		
		?>
		<p>
			<label for="hanaboard_skin"><strong>게시판 스킨 : </strong></label>
			<select id="hanaboard_skin" name="hanaboard_skin">			
				<?php foreach( $folder as $skin_item ): ?>
					<option value="<?php echo $skin_item ?>" <?php if($value_skin == $skin_item): ?> selected <?php endif ?> ><?php echo $skin_item ?></option>
				<?php endforeach ?>
			</select>
		</p>
		<?php
	}

	// 게시판이 저장되면 사용자 지정 데이터를 저장합니다.	
	public function save_hanaboard_skin_meta_box_data( $post_id ) {
		// nonce가 설정되어 있는지 확인하십시오.
		if( ! isset( $_POST['hanaboard_skin_nonce'] ) ) {
			return;
		}
	
		// nonce가 유효한지 확인하십시오.
		if( ! wp_verify_nonce( $_POST['hanaboard_skin_nonce'], 'hanaboard_skin_nonce' ) ) {
			return;
		}
	
		// 자동 저장인 경우 양식이 제출되지 않은 것이므로 아무것도하고 싶지 않습니다.
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
	
		// 사용자의 권한을 확인하십시오.
		if( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		}
		else{
			if( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
	
		/* 이제 데이터를 저장해도 안전합니다. */
	
		// 설정되어 있는지 확인하십시오.
		if( ! isset( $_POST['hanaboard_skin'] ) ) {
			return;
		}
	
		// 사용자 입력을 삭제합니다.
		$my_data = sanitize_text_field( $_POST['hanaboard_skin'] );
	
		// 데이터베이스의 메타 필드를 업데이트합니다.
		update_post_meta( $post_id, '_hanaboard_skin', $my_data );	
	}

	// 게시글 메타 상자 만들기 (참고 : 메타 상자 != 사용자 지정 메타 필드)
	public function add_custom_meta_box_hanaboard_content( $post_id ) {
		add_meta_box(
			'hanaboard-content-basic-setting', 									// 메타 박스 'id' 속성
			'게시글 기본 설정', 														// 메타 박스 제목
			Array($this, 'hanaboard_content_basic_setting_meta_box_callback'), 	// 콜백 함수
			'hanaboard_content', 												// 포스트 타입 ( 생략하면 모든 포스트 타입에 출력됨)
			'normal',															// $context
			'high'                         										// $priority
		);
	}

	public function hanaboard_content_basic_setting_meta_box_callback( $post ) { // $post -> 현재 포스트		
		// 나중에 확인할 수 있도록 임시 필드를 추가하십시오.
		wp_nonce_field( 'hanaboard_board_id_nonce', 'hanaboard_board_id_nonce' );
	
		// 하나보드 게시판 포스트 메타 박스 벨류 가져오기
		$value_id = get_post_meta( $post->ID, '_hanaboard_board_id', true );
		
		/*echo '<strong>- $value_id : </strong>' . $value_id;*/
		
		// 저장할 폴더 배열 생성
		$board_title = Array();
		
		// 하나보드 게시판 총 개수
		$count_posts = wp_count_posts('hanaboard');
		$total_posts = $count_posts->publish;
		/*echo '하나보드 게시판 총 : ' . $total_posts . '개<br />';*/
		
		$args = Array( 'post_type' => 'hanaboard' );
		$loop = new WP_Query( $args );
		
		/*echo '<strong>- 게시판 목록</strong><br />';*/
		while ( $loop->have_posts() ) {
			$loop->the_post(); // 없으면 무한 반복함.
			/*echo '-- ' . get_the_ID() . ' : ';*/
			
			$board_title[] = get_post_field( 'post_title', get_the_ID() );
			/*echo get_post_field( 'post_title', get_the_ID() ) . '<br />';*/
		}
			
		// 정렬 sort 사용 (역순으로 정렬하려면  rsort 사용)
		rsort($board_title);
		
		?>
		<p>
			<label for="hanaboard_board_id"><strong>게시판 선택 : </strong></label>
			<select id="hanaboard_board_id" name="hanaboard_board_id">			
				<?php foreach( $board_title as $board_id ): ?>
					<option value="<?php echo $board_id ?>" <?php if($value_id == $board_id): ?> selected <?php endif?> ><?php echo $board_id ?></option>
				<?php endforeach ?>
			</select>
		</p>
		<?php
	}

	// 게시글이 저장되면 사용자 지정 데이터를 저장합니다.
	public function save_hanaboard_content_basic_setting_meta_box_data( $post_id ) {	
		// nonce가 설정되어 있는지 확인하십시오.
		if( ! isset( $_POST['hanaboard_board_id_nonce'] ) ) {
			return;
		}
	
		// nonce가 유효한지 확인하십시오.
		if( ! wp_verify_nonce( $_POST['hanaboard_board_id_nonce'], 'hanaboard_board_id_nonce' ) ) {
			return;
		}
	
		// 자동 저장인 경우 양식이 제출되지 않은 것이므로 아무것도하고 싶지 않습니다.
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
	
		// 사용자의 권한을 확인하십시오.
		if( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
	
			if( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
	
		}
		else{
	
			if( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
	
		/* 이제 데이터를 저장해도 안전합니다. */
	
		// 설정되어 있는지 확인하십시오.
		if( ! isset( $_POST['hanaboard_board_id'] ) ) {
			return;
		}
	
		// 사용자 입력을 삭제합니다.
		$my_data = sanitize_text_field( $_POST['hanaboard_board_id'] );
	
		// 데이터베이스의 메타 필드를 업데이트합니다.
		update_post_meta( $post_id, '_hanaboard_board_id', $my_data );
	}

	/*---------------------------- 
	 *
	 * 하나보드 게시판 생성 숏코드
	 * 
	----------------------------*/	
	public function hanaboard_builder( $args ){

		if( !isset($args['id']) || !$args['id'] ) {
			return 'hanaboard 알림 :: id=null, 아이디값은 필수입니다.';
		}

		// 스킨 디렉토리 변수
		$dir_skin = __DIR__ . '/skin';
		echo '@ 스킨 폴더 디렉토리 :  ' . $dir_skin . '<br />';
		
		// 숏코드에 입력된 ID
		$hanaboard_id = $args['id'];
		echo '@ 게시판 ID :  ' . $hanaboard_id . '<br />';
		
		// 숏코드에 입력된 ID -> 하나보드 게시판 -> 스킨 필드 메타 값
		$value_skin = get_post_meta( $hanaboard_id, '_hanaboard_skin', true );		
		echo '@ 게시판 스킨명 :  ' . $value_skin . '<br />';
		
		// 숏코드에 입력된 ID -> 하나보드 게시판 -> 스킨 디렉토리 변수
		$board_skin_uri = $dir_skin . '/' . $value_skin;
		echo '@ 게시판의 스킨 URI :  ' . $board_skin_uri . '<br />';
		
		// 현재 게시판의 타이틀
		$hanaboard_title = get_post_field( 'post_title', $hanaboard_id );
		echo '@ 게시판 제목 :  ' . $hanaboard_title . '<br /><br />';
		
		// 숏코드에 입력된 ID -> 하나보드 게시판 -> 속한 게시글 출력
		$args = Array( 'post_type'=>'hanaboard_content' );
		$loop = new WP_Query( $args );

		// 게시판 전제 게시글
		$post_index = 0;
	
		while( $loop->have_posts() ) {
			$loop->the_post(); // 없으면 무한 반복함.
			
			$hanaboard_content_ID    	= get_the_ID();
			$hanaboard_content_title  	= get_post_field( 'post_title', $hanaboard_content_ID );
			$hanaboard_content_author	= get_post_field( 'post_author', $hanaboard_content_ID );
			$hanaboard_content_user		= get_user_meta( $hanaboard_content_author, 'first_name', true );
			$hanaboard_content_date		= get_post_field( 'post_date', $hanaboard_content_ID );
			$hanaboard_content_board 	= get_post_meta( $hanaboard_content_ID, '_hanaboard_board_id', true );
			
			$pageid = isset($_GET['pageid']) ? $_GET['pageid'] : 1;
			$params = array(
				'uid' 	 => $hanaboard_content_ID,
				'mod' 	 => "document",
				'pageid' => $pageid,
			);
			$notice_readpost_link = add_query_arg( $params );

			// 게시판에 속한 게시글만 배열에 저장
			if( $hanaboard_title == $hanaboard_content_board ){	
				echo '게시글 ID :  ' . $hanaboard_content_ID . '<br />';
				echo '게시글 제목 :  ' . $hanaboard_content_title . '<br />';
				echo '게시글 작성자 :  ' . $hanaboard_content_user . '<br />';
				echo '게시글 작성일 :  ' . $hanaboard_content_date . '<br />';
				echo '게시글의 연결된 게시판 :  ' . $hanaboard_content_board . '<br /><br />';
				
				$post_index++;
			}
		}

		include $board_skin_uri . '/list.php';

	}	

}

new HanaBoard();