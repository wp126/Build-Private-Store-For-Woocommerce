<?php

      function login_form_for_user(){
          global $bpsfw_comman,$post;
          if($bpsfw_comman['bpsfw_enable_private_store'] == 'yes'){
            if( !is_user_logged_in() ){
              if( is_shop() || is_product_category() || is_product() ){
                if($bpsfw_comman['bpsfw_enable_private_whole_website'] == 'yes'){
                  $my_account = get_permalink ( get_option( 'woocommerce_myaccount_page_id' ) );
                  wp_redirect( $my_account );
                  exit();
                }               
              }
              $appended_pags = get_option('wg_pags_select2');
              if (!empty($appended_pags)) {
                if( in_array( get_the_id(), $appended_pags ) ){
                  if($bpsfw_comman['bpsfw_enable_private_whole_website'] == 'yes'){
                    $my_account = get_permalink ( get_option( 'woocommerce_myaccount_page_id' ) );
                    wp_redirect( $my_account );
                    exit();
                  }               
                }                
              }
            }    
          }      
        } 

        function BPSFW_translate_woocommerce_strings( $translated, $untranslated, $domain ) {
          global $bpsfw_comman;
          if ( ! is_admin() && 'woocommerce' === $domain ) {
            switch ( $translated ) {         
              case 'Login':         
                $translated = $bpsfw_comman['bpsfw_login_form_title'];
              break;         
              case 'Register':         
                $translated = $bpsfw_comman['bpsfw_registration_form_title'];
              break;
            }
          }             
          return $translated;         
        }

        function custom_pre_get_posts_query( $q ) {
          global $bpsfw_comman;
          if($bpsfw_comman['bpsfw_enable_private_whole_website'] != 'yes' && $bpsfw_comman['bpsfw_disble_price_addtocartbutton'] != "yes"){
            $productsa = get_option('wg_combo');
            $q->set( 'post__not_in', $productsa );
          }
        }    
  

       
        function BPSFW_new_user_approve_autologout(){
          if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            if ( get_user_meta($user_id, 'approval_confirmation', true )  === 'confirm_approve' ){ 
              $approved = true;
            }else{
              $approved = false;
            }     
      
            if ( $approved ){ 
                return $redirect_url;
            }else{
              wp_logout();
              wp_clear_auth_cookie();
              return add_query_arg( 'confirm_approve', 'false', get_permalink( get_option('woocommerce_myaccount_page_id') ) );
            }
          }
        }

        function my_custom_function_name($user, $password){
          global $bpsfw_comman;
          if (isset($user->user_pass)) {            
            if ($user->roles['0'] == 'administrator') {
              return $user;
            }else{
              if (get_user_meta($user->ID, 'approval_confirmation', true) == 'confirm_approve') {
                  return $user;
              }
              return new WP_Error('pending_approval', $bpsfw_comman['bpsfw_pending_account_approval'] );
            }          
          }
        }

        function createMyMenus() {
          add_users_page( __( 'Users Pending Approval', 'new-user-panding' ),'Users Pending Approval','manage_options','panding-new-users','bpsfw_callback_panding');
          add_users_page( __( 'Approved Users', 'new-user-approve' ),'Approved Users','manage_options','approve-new-users','bpsfw_callback_approvel');
          add_users_page( __( 'Denied Users', 'new-user-denied' ),'Denied Users','manage_options','denied-new-users','bpsfw_callback_denied');
        }
        
        function bpsfw_callback_panding( ){
          $exampleListTable = new pafw_panding_List_Table();
          $exampleListTable->prepare_items();                  
          ?>
          <div class="bpsfw-container">
            <div class="wrap">
              <h2><?php echo __('User Registration Approval','oc-private-store-for-woocommerce-pro'); ?></h2>                  
            </div>
            <form  method="post" class="wczp_list_postcode">
              <?php
                $page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
                $paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );

                printf( '<input type="hidden" name="page" value="%s" />', $page );
                printf( '<input type="hidden" name="paged" value="%d" />', $paged ); 
              ?>
              <?php $exampleListTable->display(); ?>
            </form>
          </div>
          <?php
        }    

        function bpsfw_callback_approvel(){
          $exampleListTable = new pafw_approve_List_Table();
          $exampleListTable->prepare_items();
          ?>
          <div class="bpsfw-container">
            <div class="wrap">
              <h2><?php echo __('Approved Users','oc-private-store-for-woocommerce-pro'); ?></h2>                  
            </div>
            <form  method="post" class="wczp_list_postcode">
              <?php
                $page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
                $paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );

                printf( '<input type="hidden" name="page" value="%s" />', $page );
                printf( '<input type="hidden" name="paged" value="%d" />', $paged ); 
              ?>
              <?php $exampleListTable->display(); ?>
            </form>
          </div>
          <?php 
        }

        function bpsfw_callback_denied(){
          $exampleListTable = new pafw_denied_List_Table();
          $exampleListTable->prepare_items();      
          ?>
          <div class="bpsfw-container">
            <div class="wrap">
              <h2><?php echo __('Denied Users','oc-private-store-for-woocommerce-pro'); ?></h2>                  
            </div>
            <form  method="post" class="wczp_list_postcode">
              <?php
                $page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
                $paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );
                printf( '<input type="hidden" name="page" value="%s" />', $page );
                printf( '<input type="hidden" name="paged" value="%d" />', $paged ); 
              ?>
              <?php $exampleListTable->display(); ?>
            </form>
          </div>
          <?php
        }

        function update_meta_value(){
          global $bpsfw_comman;
          if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'panding_to_approve'){
            $user_detail = get_userdata(sanitize_text_field($_REQUEST['user']));
            $admin_email = get_option( 'admin_email' );
            $name = $user_detail->display_name;
            $email = $admin_email;
            $message = 'hii, your account has been approve. welcome to our site..!';
            $to = $user_detail->user_email;
            $subject = 'Approve Your Account..';
            $headers = 'welcome message';
            if ($bpsfw_comman['bpsfw_account_approve_email'] == 'yes') {
              wp_mail($to, $subject, $message, $headers);
            }
            update_user_meta( sanitize_text_field($_REQUEST['user']), 'approval_confirmation', 'confirm_approve');
            wp_redirect(admin_url('/users.php?page=panding-new-users'));
            exit();
          }

          if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'denied_to_approve'){
            $user_detail = get_userdata(sanitize_text_field($_REQUEST['user']));
            $admin_email = get_option( 'admin_email' );
            $name = $user_detail->display_name;
            $email = $admin_email;
            $message = 'hii, your account has been approve. welcome to our site..!';
            $to = $user_detail->user_email;
            $subject = 'Approve Your Account..';
            $headers = 'welcome message';
            if ($bpsfw_comman['bpsfw_account_approve_email'] == 'yes') {
              wp_mail($to, $subject, $message, $headers);
            }
            update_user_meta( sanitize_text_field($_REQUEST['user']), 'approval_confirmation', 'confirm_approve');
            wp_redirect(admin_url('/users.php?page=denied-new-users'));
            exit();
          }

          if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'approve_to_denied'){
            $user_detail = get_userdata(sanitize_text_field($_REQUEST['user']));
            $admin_email = get_option( 'admin_email' );
            $name = $user_detail->display_name;
            $email = $admin_email;
            $message = 'hiii, your accound has been disable.';
            $to = $user_detail->user_email;
            $subject = 'Rejected Your Account..';
            $headers = 'reject message';
            if ($bpsfw_comman['bpsfw_account_disale_email'] == 'yes') {
              wp_mail($to, $subject, $message, $headers);
            }
            update_user_meta( sanitize_text_field($_REQUEST['user']), 'approval_confirmation', 'denied_user');
            wp_redirect(admin_url('/users.php?page=approve-new-users'));
            exit();
          }

          if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'panding_to_denied'){
            $user_detail = get_userdata(sanitize_text_field($_REQUEST['user']));
            $admin_email = get_option( 'admin_email' );
            $name = $user_detail->display_name;
            $email = $admin_email;
            $message = 'hiii, your accound has been disable.';
            $to = $user_detail->user_email;
            $subject = 'Rejected Your Account..';
            $headers = 'reject message';
            if ($bpsfw_comman['bpsfw_account_disale_email'] == 'yes') {
              wp_mail($to, $subject, $message, $headers);
            }
            update_user_meta( sanitize_text_field($_REQUEST['user']), 'approval_confirmation', 'denied_user');
            wp_redirect(admin_url('/users.php?page=panding-new-users'));
            exit();
          }
        }

        function bpsfw_hide_price_addcart_not_logged_in( $price, $product ) {
          global $bpsfw_comman;           
          if($bpsfw_comman['bpsfw_enable_private_whole_website'] != "yes" && $bpsfw_comman['bpsfw_disble_price_addtocartbutton'] == "yes"  || !empty(get_option('wg_combo'))){
            if ( ! is_user_logged_in() ) { 
              
              if(!empty(get_option('wg_combo')) && in_array(get_the_id() , get_option('wg_combo'))  ){
                $price = '<div><a  style="color:'.$bpsfw_comman['bpsfw_login_to_see_price_color'].';" href="'.get_permalink(wc_get_page_id('myaccount')).'">'.__($bpsfw_comman['bpsfw_login_to_see_price'], 'bbloomer' ) . '</a></div>';
                remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
              }else{
                add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
                add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
              }
            }
          }
          return $price;
        }


        function woo_login_regt_style(){
           global $bpsfw_comman;       
          ?>
          <style type="text/css">
            #customer_login h2{
              color : <?php echo esc_attr($bpsfw_comman['woo_login_title_color']) ?>;
            }
          </style>

          <?php 
        }
                 
          global $bpsfw_comman; 
          add_filter( 'woocommerce_get_price_html','bpsfw_hide_price_addcart_not_logged_in', 9999, 2 );  
          add_action( 'wp' ,'login_form_for_user' );
          add_filter( 'gettext','BPSFW_translate_woocommerce_strings', 999, 3 );
          add_action( 'woocommerce_product_query','custom_pre_get_posts_query');
            add_action('init','bpsfw_init_panding',8);
            function bpsfw_init_panding(){
                global $bpsfw_comman;
                if($bpsfw_comman['bpsfw_approve_registration'] == 'yes'){
                    add_filter( 'authenticate', 'my_custom_function_name', 30, 3);
                    add_action( 'woocommerce_registration_redirect','BPSFW_new_user_approve_autologout', 2 );  
                }
            }
         
          add_action('wp_footer' ,'woo_login_regt_style');
          add_action( 'admin_menu', 'createMyMenus');
          add_action( 'init','update_meta_value');
    



if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class pafw_approve_List_Table extends WP_List_Table {
  public function __construct() {
    parent::__construct(
      array(
          'singular' => 'singular_form',
          'plural'   => 'plural_form',
          'ajax'     => false
      )
    );
  }


  public function prepare_items() {
    $columns = $this->get_columns();
    $hidden = $this->get_hidden_columns();
    $sortable = $this->get_sortable_columns();
    $data = $this->table_data();
    //usort( $data, array( &$this, 'sort_data' ) );
    $perPage = 5;
    $currentPage = $this->get_pagenum();
    $totalItems = count($data);
    $this->set_pagination_args( array(
      'total_items' => $totalItems,
      'per_page'    => $perPage
    ));
    $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
    $this->_column_headers = array($columns, $hidden, $sortable);
    $this->items = $data;
    $this->process_bulk_action();
  }

  public function get_columns() {
    $columns = array(
      'id'     => 'ID',
      'title'  => 'Users Name',
      'email'  => 'E-mail',
      'role'   => 'User Role',
      'action' => 'Action',
    );
    return $columns;
  }

  public function get_hidden_columns() {
    return array();
  }

  public function get_sortable_columns() {
    return array('id' => array('id', false));
  }

  private function table_data() {
    $data = array();
      $q = new WP_User_Query( 
        array(
          'orderby'  => 'ID',
          'wp_user'   => array(
          'relation'  => 'AND',
          )
        ) 
    );
    $user_query = $q->results;
    foreach ($user_query as $value) {
      $user_info = get_user_meta($value->ID);
      if (isset($user_info['approval_confirmation']['0'])) {
        if($user_info['approval_confirmation']['0'] == 'confirm_approve'){
          $data[] = array(
            'id'    => $value->ID,
            'title' =>  get_avatar($value->user_email).'<a href='. get_edit_user_link( $value->ID ).'>'.$value->display_name.'</a>',
            'email' => '<a href=mailto:'. $value->user_email.'>'. $value->user_email.'</a>' ,
            'role' => $value->roles['0'],
            'action'=>'action',          
          );
        }  
      }
    }
    if ($user_query != 'administrator') {
      return $data;
    }        
  }

  public function column_default( $item, $column_name ) {
    $denied_link =  get_option( 'siteurl' ) . '?action=approve_to_denied&user=' . $item['id'] ;
      switch( $column_name ) {
        case 'id':
          return $item['id'];
        case 'title':
          return $item['title'];
        case 'email':
          return $item['email'];
        case 'role':
          return $item['role'];            
        case 'action':
        $user = new WP_User( $item['id'] );           
        if($user->roles['0'] == 'administrator'){     
          return false;
        }    
        return '<a class="button" href="'.$denied_link.'">Deny</a>';
        default:
        return print_r( $item, true ) ;
      }
  }

  function column_cb($item) {
    return sprintf(
      '<input type="checkbox" name="id[]" value="%s" />', $item['id']
    );    
  }
}

class pafw_denied_List_Table extends WP_List_Table {
  public function __construct() {
    parent::__construct(
      array(
          'singular' => 'singular_form',
          'plural'   => 'plural_form',
          'ajax'     => false
      )
    );
  }


  public function prepare_items() {
    $columns = $this->get_columns();
    $hidden = $this->get_hidden_columns();
    $sortable = $this->get_sortable_columns();
    $data = $this->table_data();
    usort( $data, array( &$this, 'sort_data' ) );
    $perPage = 5;
    $currentPage = $this->get_pagenum();
    $totalItems = count($data);
    $this->set_pagination_args( array(
      'total_items' => $totalItems,
      'per_page'    => $perPage
    ));
    $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
    $this->_column_headers = array($columns, $hidden, $sortable);
    $this->items = $data;
    $this->process_bulk_action();
  }
 

  public function get_columns() {
    $columns = array(
      'id'     => 'ID',
      'title'  => 'Users Name',
      'email'  => 'E-mail',
      'role'   => 'User Role',
      'action' => 'Action',
    );
    return $columns;
  }
 

  public function get_hidden_columns() {
    return array();
  }


  public function get_sortable_columns() {
    return array('id' => array('id', false));
  }


  private function table_data() {
    $data = array();
    $q = new WP_User_Query( 
      array(
        'orderby'  => 'ID',
        'wp_user'    => array(
            'relation'  => 'AND',
        )
      ) 
    );
    $user_query = $q->results;
    foreach ($user_query as $value) {
      $user_info = get_user_meta($value->ID);
      if (isset($user_info['approval_confirmation']['0'])) {  
        if($user_info['approval_confirmation']['0'] == 'denied_user'){
          $data[] = array(
            'id'    => $value->ID,
            'title' =>  get_avatar($value->user_email).'<a href='. get_edit_user_link( $value->ID ).'>'.$value->display_name.'</a>',
            'email' => '<a href=mailto:'. $value->user_email.'>'. $value->user_email.'</a>' ,
            'role' => $value->roles['0'],
            'action'=>'action',          
          );
        }
      }
    }
    return $data;
  }
 

  public function column_default( $item, $column_name ) {
    $approve_link =  get_option( 'siteurl' ) . '?action=denied_to_approve&user=' . $item['id'] ;
    switch( $column_name ) {
      case 'id':
        return $item['id'];
      case 'title':
        return $item['title'];
      case 'email':
        return $item['email'];
      case 'role':
        return $item['role'];
      case 'action':                
        return '<a class="button" href="'.$approve_link.'">Approve</a>';    
      default:
        return print_r( $item, true ) ;
    }
  }


  function column_cb($item) {
    return sprintf(
      '<input type="checkbox" name="id[]" value="%s" />', $item['id']
    );    
  }
}

class pafw_panding_List_Table extends WP_List_Table {
  public function __construct() {
    parent::__construct(
      array(
        'singular' => 'singular_form',
        'plural'   => 'plural_form',
        'ajax'     => false
      )
    );
  }


  public function prepare_items() {
    $columns = $this->get_columns();
    $hidden = $this->get_hidden_columns();
    $sortable = $this->get_sortable_columns();
    $data = $this->table_data();
    usort( $data, array( &$this, 'sort_data' ) );
    $perPage = 5;
    $currentPage = $this->get_pagenum();
    $totalItems = count($data);
    $this->set_pagination_args( array(
      'total_items' => $totalItems,
      'per_page'    => $perPage
    ));
    $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
    $this->_column_headers = array($columns, $hidden, $sortable);
    $this->items = $data;
    $this->process_bulk_action();
  }
 

  public function get_columns() {
    $columns = array(
      'id'     => 'ID',
      'title'  => 'Users Name',
      'email'  => 'E-mail',
      'role'   => 'User Role',
      'action' => 'Action',
    );
    return $columns;
  }
 

  public function get_hidden_columns() {
    return array();
  }


  public function get_sortable_columns() {
    return array('id' => array('id', false));
  }


  private function table_data() {
    $data = array();
    $q = new WP_User_Query( 
      array(
        'orderby'  => 'ID',
        'wp_user'    => array(
            'relation'  => 'AND',                 
        )
      ) 
    );
    $user_query = $q->results;
    foreach ($user_query as $value) {
      $user_info = get_user_meta($value->ID);
      if (isset($user_info['approval_confirmation']['0'])) {
        if($user_info['approval_confirmation']['0'] == 'not_confirm_approve'){
          $data[] = array(
            'id'    => $value->ID,
            'title' =>  get_avatar($value->user_email).'<a href='. get_edit_user_link( $value->ID ).'>'.$value->display_name.'</a>',
            'email' => '<a href=mailto:'. $value->user_email.'>'. $value->user_email.'</a>' ,
            'role' => $value->roles['0'],
            'action'=>'action',          
          );
        }
      }
    }
    return $data;
  }
 





  public function column_default( $item, $column_name ) {
    $approve_link =  get_option( 'siteurl' ) . '?action=panding_to_approve&user=' . $item['id'] ;
    $denied_link =  get_option( 'siteurl' ) . '?action=panding_to_denied&user=' . $item['id'] ;
    switch( $column_name ) {
      case 'id':
        return $item['id'];
      case 'title':
        return $item['title'];
      case 'email':
        return $item['email'];
      case 'role':
        return $item['role'];
      case 'action':                
        return '<a class="button" href="'.$approve_link.'">Approve</a>&nbsp&nbsp<a class="button" href="'.$denied_link.'">Deny</a>';    
      default:
        return print_r( $item, true ) ;
    }
  }


  function column_cb($item) {
    return sprintf(
      '<input type="checkbox" name="id[]" value="%s" />', $item['id']
    );    
  }
}