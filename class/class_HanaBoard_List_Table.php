<?php
/**
 * HanaBoard 게시판 리스트 테이블
 * @link www.parkhana.com
 * @copyright Copyright 2020 Cosmosfarm. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl.html
 */

class  Class_HanaBoard_List_Table extends WP_List_Table {
	
	/** 처리 할 테이블 항목 준비 **/
	public function prepare_items() {
		$columns  	  = $this->get_columns();
		$hidden   	  = $this->get_hidden_columns();
		$sortable 	  = $this->get_sortable_columns();
		$total    	  = $this->fetch_table_data();
		
		$per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = count( $total );

		// 페이지네이션 처리
		$this->set_pagination_args( array(
			'total_items' => $total_items, 	// 총 항목 수를 계산해야합니다.
			'per_page'    => $per_page 		// 페이지에 표시 할 항목 수를 결정해야합니다.
		) );
		
		// 일괄 작업 처리
		$this->process_bulk_action();
		
		// 워드프레스에서 _column_headers 속성을 빌드하고 가져오는데 사용      
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		// DB 테이블 가져오기
		$this->items = self::fetch_table_data( $per_page, $current_page );
	}

    /* 상위 컬럼 메소드를 대체하십시오. 목록 테이블에서 사용할 열을 정의 */
    public function get_columns() {
        $columns = array(
			'cb' => '<input type="checkbox">',
			'ID' => 'ID',
			'post_title' 	=> '게시판 이름',
			'post_type' 	=> '포스트 타입',
			'post_author' 	=> '작성자',
			'post_date' 	=> '생성일',
        );

        return $columns;
    }

	/* 숨겨진 열 정의 */
    public function get_hidden_columns() {
        return array();
    }

    /* 정렬 가능한 열 정의 */
    public function get_sortable_columns() {
        return array('ID' => array('ID', false), 'post_title' => array('post_title', false), 'post_date' => array('post_date', false));
    }

	/* DB 테이블 가져오기 */
	public function fetch_table_data( $per_page = 5, $page_number = 1 ) {
		global $wpdb;
		$wpdb_table = $wpdb->prefix . 'posts';		
		
		$orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'ID';
		$order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'desc';
		
		$sql = "SELECT
				*
				FROM $wpdb_table
				where post_type='hanaboard'
				ORDER BY $orderby $order";
				;

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		
		$results = $wpdb->get_results( $sql, ARRAY_A  );		
		return $results;
	}	

	/* 일괄 작업 처리 */
	public function process_bulk_action() {

		// 대량 작업이 트리거되는 경우 감지...
		if ( 'delete' === $this->current_action() ) {
		
			// 요청을 처리하는 파일에서 nonce를 확인합니다.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
		
			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
			die( 'Go get a life script kiddies' );
			}
			else {
			self::delete_customer( absint( $_GET['customer'] ) );
		
			wp_redirect( esc_url( add_query_arg() ) );
			exit;
			}
		
		}
		
		// 일괄 삭제 작업이 트리거 된 경우
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
			|| ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
		
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
		
			// 레코드 ID 배열을 반복하고 삭제합니다.
			foreach ( $delete_ids as $id ) {
			self::delete_customer( $id );
		
			}
		
			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
	}

	/* 게시판 데이터가 없을 시 출력 텍스트 */
	public function no_items(){
		echo _e( '게시판이 없습니다.' );
	}

    /* 테이블의 각 열에 표시 할 데이터 정의 */
	/*public function column_default( $item, $column_name ) {
        // Array $item 데이터
		// String $column_name - 현재 열 이름
		switch( $column_name ) {
            case 'cb':
            case 'ID':
            case 'post_title':
            case 'post_type':
            case 'post_author':
            case 'post_date':
				return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;		
        }
    }*/

	public function single_row( $item ) {	
		echo '<tr data-board-id="' . $item["ID"] . '">';
		
		echo '<th scope="row" class="check-column">';
		echo '<input type="checkbox" name="board_id[]" value="'. $item["ID"] .'">';
		echo '</th>';
		
		echo '<td>';
		echo $item["ID"];
		echo '</td>';

		$edit_url = admin_url("post.php?post={$item["ID"]}&action=edit");
		
		echo '<td><a href="'.$edit_url.'" title="편집" style="display:block">';
		echo $item["post_title"];
		echo '</a></td>';

		echo '<td>';
		echo $item["post_type"];
		echo '</td>';
		
		$user = get_userdata( $item["post_author"] );
		$display_name = $user->display_name;
		
		echo '<td>';
		echo $display_name;
		echo '</td>';
		
		echo '<td>';
		echo $item["post_date"];
		echo '</td>';

		echo '</tr>';
	}

}
