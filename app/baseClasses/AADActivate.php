<?php

namespace App\baseClasses;

use WP_Error;

class AADActivate extends AADBase {

	public static function activate() {
        $default_general_Setting  = array( 'logo' => array ( 'attachment_id' => '', 'url' => ''), 'layout' => 'layout1', 'mode'=> 0);
        aaDOption('general_setting', $default_general_Setting, 'update');
	}

	public function init() {
        global $pagenow ;

        if (isset($_REQUEST['page']) && $_REQUEST['page'] === "awesome-admin") {
			// Enqueue Admin-side assets...
			add_action( 'admin_enqueue_scripts', array($this,'enqueueStyles'));
			add_action( 'admin_enqueue_scripts', array($this,'enqueueScripts'));
		}

		if (isset($_REQUEST['iframe']) && $_REQUEST['iframe'] === "enabled") {
            add_action('admin_enqueue_scripts', array($this, 'enqueueIframeStyles'));
        } else {
            if (isset($_SERVER["HTTP_REFERER"])) {
	            if (strpos($_SERVER["HTTP_REFERER"], 'iframe') !== false || strpos($_SERVER["HTTP_REFERER"], 'awesome-admin') !== false) {
		            $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] . '&iframe=enabled';
		            add_action('admin_enqueue_scripts', array($this, 'enqueueIframeStyles'));
	            }
            }
        }
		// Enqueue Front-end assets...
		add_action( 'wp_enqueue_scripts', array($this,'enqueueFrontStyles'), 18);
		add_action( 'wp_enqueue_scripts', array($this,'enqueueFrontScripts'), 18);

        // Append meta tags to header...
		add_action( 'wp_head', array($this,'appendToHeader') );
        add_action( 'admin_head', array($this,'appendToHeader') );
        
        add_action( 'login_enqueue_scripts',array($this,'enqueueLoginStyles'), 10 );
		// Enable Handler...
		( new AADRoutesHandler )->init();

		// Action to add option in the sidebar...
		add_action('admin_menu', array($this, 'adminMenu'));

		// Action to remove hide sidebar and top-bar...
		add_action('admin_head', array($this, 'hideSideBar'));
        add_action('admin_notices', array($this, 'admin_menus'));

        // Action on plugin init default redirection to awesome dashboard 
        add_action('admin_init', array($this, 'redirect_to_awesome'));
    }
    
    public function redirect_to_awesome() {

        global $pagenow ;
        $get_general_settings =  aaDOption('general_setting');

        if(!empty($get_general_settings)) {
            if(isset($get_general_settings['mode']) && $get_general_settings !== '' && $get_general_settings['mode'] == 1) {
                if($pagenow === 'index.php' && (strpos( $_SERVER['HTTP_REFERER'], 'admin.php?page=awesome-admin') == false ) &&  $_SERVER['REQUEST_URI'] !== '' && $_SERVER['REQUEST_URI'] !== null && (strpos($_SERVER['REQUEST_URI'], 'admin.php?page=awesome-admin') == false )) {
                    wp_redirect( admin_url ( 'admin.php?page=awesome-admin'));
                }
            }
        }
    } 

	public function adminMenu () {
		add_menu_page( __('Awesome Admin'), 'Awesome Admin' , 'read', 'awesome-admin', [$this, 'adminDashboard'],$this->plugin_url . 'assets/images/icon.png', 99);
	}

	public function adminDashboard() {
		include(AWESOME_ADMIN_DIR . 'resources/views/awesome_admin.php');
    }
    
	public function enqueueIframeStyles() {
        wp_enqueue_style('iframe-custom', $this->plugin_url . 'assets/css/iframe-custom.min.css');
    }

	public function enqueueStyles() {
		wp_enqueue_style( 'poppins-google-fonts', $this->plugin_url . 'assets/css/poppins-google-fonts.css' );
		wp_enqueue_style( 'app', $this->plugin_url . 'assets/css/app.min.css' );
		wp_enqueue_style( 'font-awesome-all', $this->plugin_url . 'assets/css/font-awesome-all.min.css'  );
        wp_dequeue_style( 'stylesheet' );
        wp_deregister_style('wp-admin');
    }

	public function enqueueFrontStyles() {
		wp_enqueue_style( 'font-awesome-all', $this->plugin_url . 'assets/css/font-awesome-all.min.css'  );
		wp_enqueue_style('font-awesome-all');
		wp_dequeue_style( 'stylesheet' );
    }

    function enqueueLoginStyles() {
       	wp_enqueue_style( 'front-app', $this->plugin_url . 'assets/css/login.min.css' );
    }

	public function enqueueScripts() {
		wp_enqueue_script( 'app', $this->plugin_url . 'assets/js/app.min.js', ['jquery'], false, true );
		wp_enqueue_script( 'custom', $this->plugin_url . 'assets/js/custom.js', ['jquery'], false, true );

		wp_localize_script( 'app', 'request_data', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('ajax_post'),
            'awsomeAdminPluginURL' => $this->plugin_url,
		) );

		wp_enqueue_script( 'Js_bundle' );
	}

	public function enqueueFrontScripts() {
        wp_enqueue_script( 'custom', $this->plugin_url . 'assets/js/custom.js', ['jquery'], false, true );
	}

	public function appendToHeader () {
		echo '<meta name="pluginBASEURL" content="' . $this->plugin_url .'" />';
        echo '<meta name="pluginPREFIX" content="' . $this->getPluginPrefix() .'" />';
        echo '<meta name="pluginAdminURL" content="' . admin_url() .'" />';
	}

	public function hideSideBar() {
		if(isset($_REQUEST['page']) && $_REQUEST['page'] === "awesome-admin") {
			echo '<style type="text/css">
					#wpcontent, #footer { margin-left: 0px !important;padding-left: 0px !important; }
					html.wp-toolbar { padding-top: 0px !important; }
					#adminmenuback, #adminmenuwrap, #wpadminbar, #wpfooter,#adminmenumain, #screen-meta { display: none !important; }
					
				</style>';
		}
	}

    function admin_menus() {
        global $menu, $submenu, $pagenow;
//        echo "<pre>";
//        print_r($menu); die;
        $mainMenu = collect([]);
		$arr = array_values($menu);
        foreach ($arr as $key => $m) {
            // Temp array for the main menu...
            if(isset($m[0]) && isset($m[6]) && strpos($m[2],'separator') !== 0) {

                $imageType = 'icon' ;

                if (strpos($m[6], 'data:image') !== false || strpos($m[6], 'http') !== false) {
                    $imageType = 'image';
                }

                if (strpos($m[2], '.php') === false) {
                    $url = 'admin.php?page='.$m[2];
                } else {
                    $url = isset($m[2]) ? admin_url($m[2]) : '' ;
                }

                $newMenu = [
                    'label' => isset($m[0]) ? $m[0] : '',
                    'permission' => isset($m[1]) ? $m[1] : '',
                    'url' => $url ,
                    'url_param' => '',
                    'icon' => isset($m[6]) ? $m[6] : '',
                    'is_vue' => false,
                    'image_type' => $imageType,
                    'sequence' => count($mainMenu)
                ];

                $start = strpos($newMenu['url'], "?");

                if ($start && $start >= 0) {
                    $newMenu['url_param'] = substr($newMenu['url'], $start + 1);
                    $newMenu['url'] = substr($newMenu['url'], 0, $start);
                }

                if (isset($submenu[$m[2]])) {
                    $newMenu['subMenu'] = [];

                    // Loop for the setup child menu...
                    foreach ($submenu[$m[2]] as $sm) {

//                        $url = menu_page_url($sm[2], false);
//                        $url = $url ? $url : admin_url($sm[2]);
                          $url = $sm[2];

                        $s = strpos($url, "?");
                        $newSubMenu = [
                            'label' => $sm[0],
                            'permission' => $sm[1],
                            'url' => ($s && $s >= 0) ? substr($url, 0, $s) : $url,
                            'url_param' => ($s && $s >= 0) ? substr($url, $s + 1) : '',
                            'is_vue' => false
                        ];
                        $newMenu['subMenu'][] = $newSubMenu;
                    }
                    if (count($newMenu['subMenu']) > 0) {
                        $newMenu['url'] = $newMenu['subMenu'][0]['url'];
                        $newMenu['url_param'] = $newMenu['subMenu'][0]['url_param'];
                    }
                }
                $mainMenu->push($newMenu);
            }
		}
//        echo "<pre>";
//        print_r($mainMenu);
//
//        die;

        $wordpressMenu = collect(aaDOption('menu'));

        if ($wordpressMenu->filter()->count() > 0) {
            foreach ($mainMenu as $newMenu) {
                $menuCount = $wordpressMenu->where('label', $newMenu['label'])->count();
                if ($menuCount === 0 ) {
                    $wordpressMenu->push($newMenu);
                }
            }
        } else {
            aaDOption('menu', $mainMenu->toArray(), 'update');
        }
        aaDOption('menu', $mainMenu->toArray(), 'update');
    }

}


