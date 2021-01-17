<?php
/**
 * Plugin Name: HanaBoard
 * Plugin URI: http://parkhana.com/
 * Description: 하나보드는 <strong>워드프레스의 개발원칙</strong>을 지킨 한국형 게시판입니다.
 * Version: 1.0.0
 * Author: 하나몬(Hanamon)
 * Author URI: http://parkhana.com/
 *
 */

// 직접 액세스하면 종료
if ( ! defined( 'ABSPATH' ) ) exit;

	init_hooks_fn();

	// 초기화
	function init_hooks_fn() {
		// 관리자단에 메뉴 추가
		add_action( 'admin_menu', 'add_hanaBoard_menu_fn' );
	}
	
	// 하나보드 관리자 메뉴 생성
	function add_hanaBoard_menu_fn() {
		add_menu_page(
			'하나보드', 						// 페이지 이름
			'하나보드', 						// 메뉴 이름
			'administrator', 				// 접근 권한
			'hanaboard', 					// 슬러그
			'dashboard_menu_fn', 			// 호출 함수
			'dashicons-buddicons-activity' 	// 아이콘
		);
		add_submenu_page(
			'hanaboard', 
			'대시보드', 
			'대시보드', 
			'administrator', 
			'hanaboard'
		);
		add_submenu_page(
			'hanaboard', 
			'게시판', 
			'게시판', 
			'administrator', 
			'hanaboard_board',
			'board_menu_fn'
		);
		add_submenu_page(
			'hanaboard', 
			'게시글', 
			'게시글', 
			'administrator', 
			'hanaboard_content',
			'post_menu_fn'
		);
		add_submenu_page(
			'hanaboard',
			'설정',
			'설정',
			'administrator',
			'hanaboard_config',
			'config_menu_fn'
		);
	}
	
	function dashboard_menu_fn() {
		require_once ( __DIR__ . '/pages/admin/hanaboard_dashboard.php' );
	}

	function board_menu_fn() {
		// WP_List_Table은 자동으로로드되지 않으므로 애플리케이션에로드해야합니다.
		if( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		
		require_once ( __DIR__ . '/class/class_HanaBoard_List_Table.php');
		
		// 클래스 실행
		$hanaBoardListTable = new Class_HanaBoard_List_Table();
		$hanaBoardListTable->prepare_items();
		
		// 페이지 실행
		require_once ( __DIR__ . '/pages/admin/hanaboard_board_list.php');
	}
	
	function post_menu_fn() {
		require_once ( __DIR__ . '/pages/admin/hanaboard_content_list.php' );
	}
	
	function config_menu_fn() {
		print '<h1>아직 아무것도 없음</h1>';
	}
	/* 하나보드 관리자 메뉴 생성 끝 */
