<?php
/*
Plugin Name: Misskey Post
Version: 1.0.1
Author: zinntikumugai
Author URI: https://www.zinntikumugai.com
License: GPL-3
 */
class Misskey {
    public const Name = 'Misskey Post';
    public const Slag = 'misskey-post';

    public function __construct() {
        add_action('init', [$this, 'session']);
        add_action('publish_post', [$this, 'Post']);
    }

    public function menu() {
        require_once(__DIR__ .'/misskey-Settings.php');
        $ms = new MisskeySettings;
    }

    public function session() {
        @session_start();
    }

    public function Post($postId, $post=null) {
        require_once(__DIR__ . '/misskey-Settings.php');
        require_once(__DIR__ . '/lib/API.php');
        $options = get_option(MisskeySettings::OptionName, $this->defaults);
        if(!isset($options['i']))
            return;
        $mapi = new MisskeyAPI($options['url'], $options['i']);

        if(wp_is_post_revision($postId))
            return;
        $postData = get_post($postId);
        $content = 
            $postData->post_title." \n".
            get_permalink($postId)." \n".
            get_bloginfo('name');
        //$content = "とあるぶろぐのきじ \nhttps://misskey.xyz \n ここはMisskey";
        $res = $mapi->CreateNote([
            'text' => $content,
            //'visibility' => 'private'
        ]);
    }
}
$m = new Misskey;
$m->menu();