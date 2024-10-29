<?php

namespace App\controllers;

use App\baseClasses\AADBase;
use App\baseClasses\AADRequest;
use WP_User;

class AADHomeController extends AADBase
{

    /**
     * @var AADRequest
     */
    private $request;

    public function __construct()
    {
        $this->request = new AADRequest();
    }

	public function getUser()
	{
        
		$user_id = get_current_user_id();
        $user_avatar = get_avatar_url($user_id);
        $userObj = new WP_User($user_id);
        $get_current_layout =  aaDOption('general_setting');
        $sidebar_menu =  aaDOption('menu');
		if (isset($userObj->data->user_email)) {
			$user = $userObj->data;
			unset($user->user_pass);
			$user->permissions = $userObj->allcaps;
			$user->roles = $userObj->roles;
            $user->sidebar_object = $sidebar_menu;
            $user->layout = $get_current_layout['layout'];
            $user->logoUrl = $get_current_layout['logo']['url'];
            $user->user_avatar =$user_avatar;
            $user->admin_defualt_mode =$get_current_layout['mode'];
		} else {
			$user = [];
		}
		echo json_encode([
			'status' => true,
			'message' => esc_html__('User data', 'aad-lang'),
			'data' => $user
		]);

    }

    public function updateSidebar() {

        $request_data = $this->request->getInputs();
        aaDOption('menu', $request_data['payload'], 'update');

        echo json_encode([
            'data'=> aaDOption('menu'),
            'status' => true,
            'message' => esc_html__('Update successfully', 'aad-lang'),
        ]);
    }

    public function settings() {
        $request_data = $this->request->getInputs();
        $settings_data = [] ;
        $get_data=  aaDOption('general_setting', $settings_data, 'get');
        if($request_data['file'] !== null){
            $attachment_id = media_handle_upload('file', 0);
            $attachment_url = wp_get_attachment_url($attachment_id);
            $settings_data['logo'] = ['attachment_id' => $attachment_id, "url" => $attachment_url];
            $settings_data['layout'] = $request_data['layout'];
            $settings_data['mode'] = $request_data['mode'];
        }else{
            $settings_data['logo'] = ['attachment_id' => $get_data['logo']['attachment_id'], "url" =>$get_data['logo']['url']];
            $settings_data['layout'] = $request_data['layout'];
            $settings_data['mode'] = $request_data['mode'];
        }
        aaDOption('general_setting',$settings_data, 'update');
        echo json_encode([
            'status' => true,
            'message' => esc_html__('General settings saved successfully.', 'aad-lang'),
        ]);
    }

    public function logout()
    {
        wp_logout();
        echo json_encode([
            'status' => true,
            'message' => esc_html__('Logout successfully.', 'aad-lang'),
        ]);
    }

}
