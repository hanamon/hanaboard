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
	
	// __construct() 메소드로 초기화
	public function __construct() {
		// 변수 선언
		$this->$POST_TYPE_Board   = 'hanaboard';
		$this->$POST_TYPE_Content = 'hanaboard_content';
		
		// 메소드 실행
		add_action( 'init', array($this, 'add_hanaBoard_post_type_fn') );
	}

	/*-------------------------------------
	 *
	 * 하나보드 게시판 & 게시글 포스트 타입 생성 /wp-admin/edit.php?post_type=hanaboard
	 *
	 --------------------------------------*/
	
	// 하나보드 게시판 & 게시글 포스트 타입 생성
	public function add_hanaBoard_post_type_fn() {
		
		$type_board_labels = array(
			'name'               => '하나보드 게시판',
			'add_new'            => '게시판 생성',			
			'add_new_item'       => '게시판 생성',
			'edit_item'          => '게시판 수정',
			'search_items'       => '게시판 검색',
			'not_found'          => '게시판이 없습니다.',
			'not_found_in_trash' => '휴지통에 게시판이 없습니다.',
		);
		$type_board_args = array(
			'labels'        => $type_board_labels,
			'description'   => '하나보드 게시판 및 게시판 별 데이터 보관',
			'public'        => true,
			'hierarchical'	=> true,
			'menu_position' => 5,
			'supports'      => array( 'title' ),
			'has_archive'   => true,
		);
		
		// 하나보드 게시판 포스트 타입 생성
		register_post_type( $this->$POST_TYPE_Board, $type_board_args ); 
		
		$type_content_labels = array(
			'name'               => '하나보드 게시글',
			'add_new'            => '게시글 생성',			
			'add_new_item'       => '게시글 생성',
			'edit_item'          => '게시글 수정',
			'search_items'       => '게시글 검색',
			'not_found'          => '게시글이 없습니다.',
			'not_found_in_trash' => '휴지통에 게시글이 없습니다.',
		);
		$type_content_args = array(
			'labels'        => $type_content_labels,
			'description'   => '하나보드 게시물 및 게시물 별 데이터 보관',
			'public'        => true,
			'hierarchical'	=> true,
			'menu_position' => 5,
			'supports'      => array( 'title', 'thumbnail' ),
			'has_archive'   => true,
		);
		
		// 하나보드 게시글 포스트 타입 생성
		register_post_type( $this->$POST_TYPE_Content, $type_content_args ); 

	}
	
}

new HanaBoard();
