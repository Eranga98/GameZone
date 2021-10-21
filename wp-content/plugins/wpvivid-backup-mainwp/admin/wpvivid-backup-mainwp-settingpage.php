<?php

class Mainwp_WPvivid_Extension_SettingPage
{
    private $setting;
    private $setting_addon;
    private $select_pro;
    private $site_id;

    public function __construct()
    {
        $this->load_setting_ajax();
    }

    public function set_site_id($site_id)
    {
        $this->site_id=$site_id;
    }

    public function set_setting_info($setting, $setting_addon=array(), $select_pro=0)
    {
        $this->setting=$setting;
        $this->setting_addon=$setting_addon;
        $this->select_pro=$select_pro;
    }

    public function load_setting_ajax()
    {
        add_action('wp_ajax_mwp_wpvivid_set_general_setting_addon', array($this, 'set_general_setting_addon'));
        add_action('wp_ajax_mwp_wpvivid_set_global_general_setting_addon', array($this, 'set_global_general_setting_addon'));
        add_action('wp_ajax_mwp_wpvivid_set_general_setting', array($this, 'set_general_setting'));
        add_action('wp_ajax_mwp_wpvivid_set_global_general_setting', array($this, 'set_global_general_setting'));
        add_action('wp_ajax_mwp_wpvivid_sync_setting', array($this, 'sync_setting'));
    }

    public function set_general_setting_addon()
    {
        global $mainwp_wpvivid_extension_activator;
        $mainwp_wpvivid_extension_activator->mwp_ajax_check_security();
        try {
            if(isset($_POST['site_id']) && !empty($_POST['site_id']) && is_string($_POST['site_id']) &&
                isset($_POST['setting']) && !empty($_POST['setting']) && is_string($_POST['setting'])) {
                $setting = array();
                $site_id = sanitize_text_field($_POST['site_id']);
                $post_data['mwp_action'] = 'wpvivid_set_general_setting_addon_mainwp';
                $json = stripslashes(sanitize_text_field($_POST['setting']));
                $setting = json_decode($json, true);

                $options=Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_get_option(sanitize_text_field($_GET['id']), 'settings_addon', array());

                $mwp_use_temp_file = isset($setting['mwp_use_temp_file_addon']) ? $setting['mwp_use_temp_file_addon'] : 1;
                $mwp_use_temp_size = isset($setting['mwp_use_temp_size_addon']) ? $setting['mwp_use_temp_size_addon'] : 16;
                $mwp_compress_type = isset($setting['mwp_compress_type_addon']) ? $setting['mwp_compress_type_addon'] : 'zip';

                $setting['mwp_use_temp_file_addon'] = intval($mwp_use_temp_file);
                $setting['mwp_use_temp_size_addon'] = intval($mwp_use_temp_size);
                $setting['mwp_exclude_file_size_addon'] = intval($setting['mwp_exclude_file_size_addon']);
                $setting['mwp_max_execution_time_addon'] = intval($setting['mwp_max_execution_time_addon']);
                $setting['mwp_max_backup_count_addon'] = intval($setting['mwp_max_local_backup_count_addon']);
                if(isset($setting['mwp_max_remote_backup_count_addon'])){
                    $setting['mwp_max_remote_backup_count_addon'] = intval($setting['mwp_max_remote_backup_count_addon']);
                }
                else{
                    $setting['mwp_max_remote_backup_count_addon'] = 30;
                }
                $setting['mwp_max_resume_count_addon'] = intval($setting['mwp_max_resume_count_addon']);

                $setting_data['wpvivid_compress_setting']['compress_type'] = $mwp_compress_type;
                $setting_data['wpvivid_compress_setting']['max_file_size'] = $setting['mwp_max_file_size_addon'] . 'M';
                $setting_data['wpvivid_compress_setting']['no_compress'] = $setting['mwp_no_compress_addon'];
                $setting_data['wpvivid_compress_setting']['use_temp_file'] = $setting['mwp_use_temp_file_addon'];
                $setting_data['wpvivid_compress_setting']['use_temp_size'] = $setting['mwp_use_temp_size_addon'];
                $setting_data['wpvivid_compress_setting']['exclude_file_size'] = $setting['mwp_exclude_file_size_addon'];
                $setting_data['wpvivid_compress_setting']['subpackage_plugin_upload'] = $setting['mwp_subpackage_plugin_upload_addon'];

                $setting_data['wpvivid_local_setting']['path'] = $setting['mwp_path_addon'];
                $setting_data['wpvivid_local_setting']['save_local'] = isset($options['wpvivid_local_setting']['save_local']) ? $options['wpvivid_local_setting']['save_local'] : 1;

                $setting_data['wpvivid_common_setting']['max_execution_time'] = $setting['mwp_max_execution_time_addon'];
                $setting_data['wpvivid_common_setting']['log_save_location'] = $setting['mwp_path_addon'] . '/wpvivid_log';
                $setting_data['wpvivid_common_setting']['max_backup_count'] = $setting['mwp_max_backup_count_addon'];
                $setting_data['wpvivid_common_setting']['max_remote_backup_count'] = $setting['mwp_max_remote_backup_count_addon'];
                $setting_data['wpvivid_common_setting']['show_admin_bar'] = isset($options['wpvivid_common_setting']['show_admin_bar']) ? $options['wpvivid_common_setting']['show_admin_bar'] : 1;
                $setting_data['wpvivid_common_setting']['clean_old_remote_before_backup'] = $setting['mwp_clean_old_remote_before_backup_addon'];
                $setting_data['wpvivid_common_setting']['estimate_backup'] = $setting['mwp_estimate_backup_addon'];
                $setting_data['wpvivid_common_setting']['ismerge'] = $setting['mwp_ismerge_addon'];
                $setting_data['wpvivid_common_setting']['domain_include'] = isset($options['wpvivid_common_setting']['domain_include']) ? $options['wpvivid_common_setting']['domain_include'] : 1;
                $setting_data['wpvivid_common_setting']['memory_limit'] = $setting['mwp_memory_limit_addon'] . 'M';
                $setting_data['wpvivid_common_setting']['max_resume_count'] = $setting['mwp_max_resume_count_addon'];
                $setting_data['wpvivid_common_setting']['db_connect_method'] = $setting['mwp_db_connect_method_addon'];

                $setting_data['wpvivid_staging_options']['staging_db_insert_count'] = intval($setting['mwp_staging_db_insert_count_addon']);
                $setting_data['wpvivid_staging_options']['staging_db_replace_count'] = intval($setting['mwp_staging_db_replace_count_addon']);
                $setting_data['wpvivid_staging_options']['staging_file_copy_count'] = intval($setting['mwp_staging_file_copy_count_addon']);
                $setting_data['wpvivid_staging_options']['staging_exclude_file_size'] = intval($setting['mwp_staging_exclude_file_size_addon']);
                $setting_data['wpvivid_staging_options']['staging_memory_limit'] = $setting['mwp_staging_memory_limit_addon'].'M';
                $setting_data['wpvivid_staging_options']['staging_max_execution_time'] = intval($setting['mwp_staging_max_execution_time_addon']);
                $setting_data['wpvivid_staging_options']['staging_resume_count'] = intval($setting['mwp_staging_resume_count_addon']);

                foreach ($setting_data as $option_name => $option) {
                    $options[$option_name] = $setting_data[$option_name];
                }

                Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_update_option($site_id, 'settings_addon', $options);

                $post_data['setting'] = json_encode($options);

                $information = apply_filters('mainwp_fetchurlauthed', $mainwp_wpvivid_extension_activator->childFile, $mainwp_wpvivid_extension_activator->childKey, $site_id, 'wpvivid_backuprestore', $post_data);

                if (isset($information['error'])) {
                    $ret['result'] = 'failed';
                    $ret['error'] = $information['error'];
                } else {
                    $ret['result'] = 'success';
                }
                echo json_encode($ret);
            }
            die();
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
    }

    public function set_global_general_setting_addon()
    {
        global $mainwp_wpvivid_extension_activator;
        $mainwp_wpvivid_extension_activator->mwp_ajax_check_security();
        try {
            $setting = array();
            $schedule = array();
            if (isset($_POST['setting']) && !empty($_POST['setting']) && is_string($_POST['setting'])) {
                $json = stripslashes(sanitize_text_field($_POST['setting']));
                $setting = json_decode($json, true);
                $options = Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_get_global_option('settings_addon', array());
                $mwp_use_temp_file = isset($setting['mwp_use_temp_file_addon']) ? $setting['mwp_use_temp_file_addon'] : 1;
                $mwp_use_temp_size = isset($setting['mwp_use_temp_size_addon']) ? $setting['mwp_use_temp_size_addon'] : 16;
                $mwp_compress_type = isset($setting['mwp_compress_type_addon']) ? $setting['mwp_compress_type_addon'] : 'zip';

                $setting['mwp_use_temp_file_addon'] = intval($mwp_use_temp_file);
                $setting['mwp_use_temp_size_addon'] = intval($mwp_use_temp_size);
                $setting['mwp_exclude_file_size_addon'] = intval($setting['mwp_exclude_file_size_addon']);
                $setting['mwp_max_execution_time_addon'] = intval($setting['mwp_max_execution_time_addon']);
                $setting['mwp_max_backup_count_addon'] = intval($setting['mwp_max_local_backup_count_addon']);
                if(isset($setting['mwp_max_remote_backup_count_addon'])){
                    $setting['mwp_max_remote_backup_count_addon'] = intval($setting['mwp_max_remote_backup_count_addon']);
                }
                else{
                    $setting['mwp_max_remote_backup_count_addon'] = 30;
                }
                $setting['mwp_max_resume_count_addon'] = intval($setting['mwp_max_resume_count_addon']);

                $setting_data['wpvivid_compress_setting']['compress_type'] = $mwp_compress_type;
                $setting_data['wpvivid_compress_setting']['max_file_size'] = $setting['mwp_max_file_size_addon'] . 'M';
                $setting_data['wpvivid_compress_setting']['no_compress'] = $setting['mwp_no_compress_addon'];
                $setting_data['wpvivid_compress_setting']['use_temp_file'] = $setting['mwp_use_temp_file_addon'];
                $setting_data['wpvivid_compress_setting']['use_temp_size'] = $setting['mwp_use_temp_size_addon'];
                $setting_data['wpvivid_compress_setting']['exclude_file_size'] = $setting['mwp_exclude_file_size_addon'];
                $setting_data['wpvivid_compress_setting']['subpackage_plugin_upload'] = $setting['mwp_subpackage_plugin_upload_addon'];

                $setting_data['wpvivid_local_setting']['path'] = $setting['mwp_path_addon'];
                $setting_data['wpvivid_local_setting']['save_local'] = isset($options['wpvivid_local_setting']['save_local']) ? $options['wpvivid_local_setting']['save_local'] : 1;

                $setting_data['wpvivid_common_setting']['max_execution_time'] = $setting['mwp_max_execution_time_addon'];
                $setting_data['wpvivid_common_setting']['log_save_location'] = $setting['mwp_path_addon'] . '/wpvivid_log';
                $setting_data['wpvivid_common_setting']['max_backup_count'] = $setting['mwp_max_backup_count_addon'];
                $setting_data['wpvivid_common_setting']['max_remote_backup_count'] = $setting['mwp_max_remote_backup_count_addon'];
                $setting_data['wpvivid_common_setting']['show_admin_bar'] = isset($options['wpvivid_common_setting']['show_admin_bar']) ? $options['wpvivid_common_setting']['show_admin_bar'] : 1;
                $setting_data['wpvivid_common_setting']['clean_old_remote_before_backup'] = $setting['mwp_clean_old_remote_before_backup_addon'];
                $setting_data['wpvivid_common_setting']['estimate_backup'] = $setting['mwp_estimate_backup_addon'];
                $setting_data['wpvivid_common_setting']['ismerge'] = $setting['mwp_ismerge_addon'];
                $setting_data['wpvivid_common_setting']['domain_include'] = isset($options['wpvivid_common_setting']['domain_include']) ? $options['wpvivid_common_setting']['domain_include'] : 1;
                $setting_data['wpvivid_common_setting']['memory_limit'] = $setting['mwp_memory_limit_addon'] . 'M';
                $setting_data['wpvivid_common_setting']['max_resume_count'] = $setting['mwp_max_resume_count_addon'];
                $setting_data['wpvivid_common_setting']['db_connect_method'] = $setting['mwp_db_connect_method_addon'];

                $setting_data['wpvivid_staging_options']['staging_db_insert_count'] = intval($setting['mwp_staging_db_insert_count_addon']);
                $setting_data['wpvivid_staging_options']['staging_db_replace_count'] = intval($setting['mwp_staging_db_replace_count_addon']);
                $setting_data['wpvivid_staging_options']['staging_file_copy_count'] = intval($setting['mwp_staging_file_copy_count_addon']);
                $setting_data['wpvivid_staging_options']['staging_exclude_file_size'] = intval($setting['mwp_staging_exclude_file_size_addon']);
                $setting_data['wpvivid_staging_options']['staging_memory_limit'] = $setting['mwp_staging_memory_limit_addon'].'M';
                $setting_data['wpvivid_staging_options']['staging_max_execution_time'] = intval($setting['mwp_staging_max_execution_time_addon']);
                $setting_data['wpvivid_staging_options']['staging_resume_count'] = intval($setting['mwp_staging_resume_count_addon']);

                if(empty($options)){
                    $options = array();
                }
                foreach ($setting_data as $option_name => $option) {
                    $options[$option_name] = $setting_data[$option_name];
                }

                Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_update_global_option('settings_addon', $options);

                $ret['result'] = 'success';

                echo json_encode($ret);
            }
            die();
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
    }

    public function set_general_setting()
    {
        global $mainwp_wpvivid_extension_activator;
        $mainwp_wpvivid_extension_activator->mwp_ajax_check_security();
        try {
            if(isset($_POST['site_id']) && !empty($_POST['site_id']) && is_string($_POST['site_id']) &&
                isset($_POST['setting']) && !empty($_POST['setting']) && is_string($_POST['setting'])) {
                $setting = array();
                $site_id = sanitize_text_field($_POST['site_id']);
                $post_data['mwp_action'] = 'wpvivid_set_general_setting_mainwp';
                $json = stripslashes(sanitize_text_field($_POST['setting']));
                $setting = json_decode($json, true);

                $options=Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_get_option(sanitize_text_field($_GET['id']), 'settings', array());

                $mwp_use_temp_file = isset($setting['mwp_use_temp_file']) ? $setting['mwp_use_temp_file'] : 1;
                $mwp_use_temp_size = isset($setting['mwp_use_temp_size']) ? $setting['mwp_use_temp_size'] : 16;
                $mwp_compress_type = isset($setting['mwp_compress_type']) ? $setting['mwp_compress_type'] : 'zip';

                $setting['mwp_use_temp_file'] = intval($mwp_use_temp_file);
                $setting['mwp_use_temp_size'] = intval($mwp_use_temp_size);
                $setting['mwp_exclude_file_size'] = intval($setting['mwp_exclude_file_size']);
                $setting['mwp_max_execution_time'] = intval($setting['mwp_max_execution_time']);
                $setting['mwp_max_backup_count'] = intval($setting['mwp_max_backup_count']);
                $setting['mwp_max_resume_count'] = intval($setting['mwp_max_resume_count']);

                $setting_data['wpvivid_compress_setting']['compress_type'] = $mwp_compress_type;
                $setting_data['wpvivid_compress_setting']['max_file_size'] = $setting['mwp_max_file_size'] . 'M';
                $setting_data['wpvivid_compress_setting']['no_compress'] = $setting['mwp_no_compress'];
                $setting_data['wpvivid_compress_setting']['use_temp_file'] = $setting['mwp_use_temp_file'];
                $setting_data['wpvivid_compress_setting']['use_temp_size'] = $setting['mwp_use_temp_size'];
                $setting_data['wpvivid_compress_setting']['exclude_file_size'] = $setting['mwp_exclude_file_size'];
                $setting_data['wpvivid_compress_setting']['subpackage_plugin_upload'] = $setting['mwp_subpackage_plugin_upload'];

                $setting_data['wpvivid_local_setting']['path'] = $setting['mwp_path'];
                $setting_data['wpvivid_local_setting']['save_local'] = isset($options['wpvivid_local_setting']['save_local']) ? $options['wpvivid_local_setting']['save_local'] : 1;

                $setting_data['wpvivid_common_setting']['max_execution_time'] = $setting['mwp_max_execution_time'];
                $setting_data['wpvivid_common_setting']['log_save_location'] = $setting['mwp_path'] . '/wpvivid_log';
                $setting_data['wpvivid_common_setting']['max_backup_count'] = $setting['mwp_max_backup_count'];
                $setting_data['wpvivid_common_setting']['show_admin_bar'] = isset($options['wpvivid_common_setting']['show_admin_bar']) ? $options['wpvivid_common_setting']['show_admin_bar'] : 1;
                $setting_data['wpvivid_common_setting']['estimate_backup'] = $setting['mwp_estimate_backup'];
                $setting_data['wpvivid_common_setting']['ismerge'] = $setting['mwp_ismerge'];
                $setting_data['wpvivid_common_setting']['domain_include'] = $setting['mwp_domain_include'];
                $setting_data['wpvivid_common_setting']['memory_limit'] = $setting['mwp_memory_limit'] . 'M';
                $setting_data['wpvivid_common_setting']['max_resume_count'] = $setting['mwp_max_resume_count'];
                $setting_data['wpvivid_common_setting']['db_connect_method'] = $setting['mwp_db_connect_method'];

                foreach ($setting_data as $option_name => $option) {
                    $options[$option_name] = $setting_data[$option_name];
                }

                Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_update_option($site_id, 'settings', $options);

                $post_data['setting'] = json_encode($options);

                $information = apply_filters('mainwp_fetchurlauthed', $mainwp_wpvivid_extension_activator->childFile, $mainwp_wpvivid_extension_activator->childKey, $site_id, 'wpvivid_backuprestore', $post_data);

                if (isset($information['error'])) {
                    $ret['result'] = 'failed';
                    $ret['error'] = $information['error'];
                } else {
                    $ret['result'] = 'success';
                }
                echo json_encode($ret);
            }
            die();
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
    }

    public function set_global_general_setting()
    {
        global $mainwp_wpvivid_extension_activator;
        $mainwp_wpvivid_extension_activator->mwp_ajax_check_security();
        try {
            $setting = array();
            $schedule = array();
            if (isset($_POST['setting']) && !empty($_POST['setting']) && is_string($_POST['setting'])) {
                $json = stripslashes(sanitize_text_field($_POST['setting']));
                $setting = json_decode($json, true);
                $options = Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_get_global_option('settings', array());

                $mwp_use_temp_file = isset($setting['mwp_use_temp_file']) ? $setting['mwp_use_temp_file'] : 1;
                $mwp_use_temp_size = isset($setting['mwp_use_temp_size']) ? $setting['mwp_use_temp_size'] : 16;
                $mwp_compress_type = isset($setting['mwp_compress_type']) ? $setting['mwp_compress_type'] : 'zip';

                $setting['mwp_use_temp_file'] = intval($mwp_use_temp_file);
                $setting['mwp_use_temp_size'] = intval($mwp_use_temp_size);
                $setting['mwp_exclude_file_size'] = intval($setting['mwp_exclude_file_size']);
                $setting['mwp_max_execution_time'] = intval($setting['mwp_max_execution_time']);
                $setting['mwp_max_backup_count'] = intval($setting['mwp_max_backup_count']);
                $setting['mwp_max_resume_count'] = intval($setting['mwp_max_resume_count']);

                $setting_data['wpvivid_compress_setting']['compress_type'] = $mwp_compress_type;
                $setting_data['wpvivid_compress_setting']['max_file_size'] = $setting['mwp_max_file_size'] . 'M';
                $setting_data['wpvivid_compress_setting']['no_compress'] = $setting['mwp_no_compress'];
                $setting_data['wpvivid_compress_setting']['use_temp_file'] = $setting['mwp_use_temp_file'];
                $setting_data['wpvivid_compress_setting']['use_temp_size'] = $setting['mwp_use_temp_size'];
                $setting_data['wpvivid_compress_setting']['exclude_file_size'] = $setting['mwp_exclude_file_size'];
                $setting_data['wpvivid_compress_setting']['subpackage_plugin_upload'] = $setting['mwp_subpackage_plugin_upload'];

                $setting_data['wpvivid_local_setting']['path'] = $setting['mwp_path'];
                $setting_data['wpvivid_local_setting']['save_local'] = isset($options['wpvivid_local_setting']['save_local']) ? $options['wpvivid_local_setting']['save_local'] : 1;

                $setting_data['wpvivid_common_setting']['max_execution_time'] = $setting['mwp_max_execution_time'];
                $setting_data['wpvivid_common_setting']['log_save_location'] = $setting['mwp_path'] . '/wpvivid_log';
                $setting_data['wpvivid_common_setting']['max_backup_count'] = $setting['mwp_max_backup_count'];
                $setting_data['wpvivid_common_setting']['show_admin_bar'] = isset($options['wpvivid_common_setting']['show_admin_bar']) ? $options['wpvivid_common_setting']['show_admin_bar'] : 1;
                $setting_data['wpvivid_common_setting']['estimate_backup'] = $setting['mwp_estimate_backup'];
                $setting_data['wpvivid_common_setting']['ismerge'] = $setting['mwp_ismerge'];
                $setting_data['wpvivid_common_setting']['domain_include'] = $setting['mwp_domain_include'];
                $setting_data['wpvivid_common_setting']['memory_limit'] = $setting['mwp_memory_limit'] . 'M';
                $setting_data['wpvivid_common_setting']['max_resume_count'] = $setting['mwp_max_resume_count'];
                $setting_data['wpvivid_common_setting']['db_connect_method'] = $setting['mwp_db_connect_method'];

                if(empty($options)){
                    $options = array();
                }
                foreach ($setting_data as $option_name => $option) {
                    $options[$option_name] = $setting_data[$option_name];
                }

                Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_update_global_option('settings', $options);

                $ret['result'] = 'success';

                echo json_encode($ret);
            }
            die();
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
    }

    public function sync_setting()
    {
        global $mainwp_wpvivid_extension_activator;
        $mainwp_wpvivid_extension_activator->mwp_ajax_check_security();
        try {
            if(isset($_POST['id']) && !empty($_POST['id']) && is_string($_POST['id'])) {
                $site_id = sanitize_text_field($_POST['id']);
                $check_addon = '0';
                if(isset($_POST['addon']) && !empty($_POST['addon']) && is_string($_POST['addon'])) {
                    $check_addon = sanitize_text_field($_POST['addon']);
                }
                if($check_addon == '1'){
                    $post_data['mwp_action'] = 'wpvivid_set_general_setting_addon_mainwp';
                    $setting = Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_get_global_option('settings_addon', array());
                    Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_update_option($site_id, 'settings_addon', $setting);
                }
                else {
                    $post_data['mwp_action'] = 'wpvivid_set_general_setting_mainwp';
                    $setting = Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_get_global_option('settings', array());
                    Mainwp_WPvivid_Extension_DB_Option::get_instance()->wpvivid_update_option($site_id, 'settings', $setting);
                }
                $post_data['setting'] = json_encode($setting);
                $information = apply_filters('mainwp_fetchurlauthed', $mainwp_wpvivid_extension_activator->childFile, $mainwp_wpvivid_extension_activator->childKey, $site_id, 'wpvivid_backuprestore', $post_data);
                if (isset($information['error'])) {
                    $ret['result'] = 'failed';
                    $ret['error'] = $information['error'];
                } else {
                    $ret['result'] = 'success';
                }

                echo json_encode($ret);
            }
            die();
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
    }

    public function render($check_pro, $global=false)
    {
        if(isset($_GET['synchronize']) && isset($_GET['addon']))
        {
            $check_addon = sanitize_text_field($_GET['addon']);
            $this->mwp_wpvivid_synchronize_setting($check_addon);
        }
        else {
            ?>
            <div style="padding: 10px;">
                <?php
                if($global){
                    if($this->select_pro){
                        $select_pro_check = 'checked';
                    }
                    else{
                        $select_pro_check = '';
                    }
                    ?>
                    <div class="mwp-wpvivid-block-bottom-space" style="background: #fff;">
                        <div class="postbox" style="padding: 10px; margin-bottom: 0;">
                            <div style="float: left; margin-top: 7px; margin-right: 25px;"><?php _e('Switch to WPvivid Backup Pro'); ?></div>
                            <div class="ui toggle checkbox mwp-wpvivid-pro-swtich" style="float: left; margin-top:4px; margin-right: 10px;">
                                <input type="checkbox" <?php esc_attr_e($select_pro_check); ?> />
                                <label for=""></label>
                            </div>
                            <div style="float: left;"><input class="ui green mini button" type="button" value="Save" onclick="mwp_wpvivid_switch_pro_setting();" /></div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                    <div style="clear: both;"></div>
                    <?php
                    if($this->select_pro){
                        $this->mwp_wpvivid_setting_page_addon($global);
                    }
                    else{
                        $this->mwp_wpvivid_setting_page($global);
                    }
                    ?>
                    <?php
                }
                else{
                    if($check_pro){
                        $this->mwp_wpvivid_setting_page_addon($global);
                    }
                    else{
                        $this->mwp_wpvivid_setting_page($global);
                    }
                }
                ?>
            </div>
            <script>
                function mwp_wpvivid_switch_pro_setting(){
                    if(jQuery('.mwp-wpvivid-pro-swtich').find('input:checkbox').prop('checked')){
                        var pro_setting = 1;
                    }
                    else{
                        var pro_setting = 0;
                    }
                    var ajax_data = {
                        'action': 'mwp_wpvivid_switch_pro_setting',
                        'pro_setting': pro_setting
                    };
                    mwp_wpvivid_post_request(ajax_data, function (data) {
                        try {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success') {
                                location.reload();
                            }
                            else {
                                alert(jsonarray.error);
                            }
                        }
                        catch (err) {
                            alert(err);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown) {
                        var error_message = mwp_wpvivid_output_ajaxerror('changing base settings', textStatus, errorThrown);
                        alert(error_message);
                    });
                }

                function mwp_wpvivid_swtich_global_setting_tab(evt, contentName){
                    var i, tabcontent, tablinks;
                    tabcontent = document.getElementsByClassName("mwp-global-setting-tab-content");
                    for (i = 0; i < tabcontent.length; i++) {
                        tabcontent[i].style.display = "none";
                    }
                    tablinks = document.getElementsByClassName("mwp-global-setting-nav-tab");
                    for (i = 0; i < tablinks.length; i++) {
                        tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
                    }
                    document.getElementById(contentName).style.display = "block";
                    evt.currentTarget.className += " nav-tab-active";
                }
            </script>
            <?php
        }
    }

    public function mwp_wpvivid_setting_page_addon($global){
        ?>
        <div>
            <div class="mwp-wpvivid-block-bottom-space mwp-wpvivid-block-right-space" style="float: left;">
                <img src="<?php echo esc_url(MAINWP_WPVIVID_EXTENSION_PLUGIN_URL.'/admin/images/settings.png'); ?>" style="width:50px;height:50px;">
            </div>
            <div class="mwp-wpvivid-block-bottom-space">
                <div>This tab allows you to modify WPvivid Backup Pro plugin settings for child sites, including general settings, advanced settings and staging settings.</div>
            </div>
            <div style="clear: both;"></div>
        </div>
        <?php
        if(!class_exists('Mainwp_WPvivid_Tab_Page_Container'))
        include_once MAINWP_WPVIVID_EXTENSION_PLUGIN_DIR . '/includes/wpvivid-backup-mainwp-tab-page-container.php';
        $this->main_tab=new Mainwp_WPvivid_Tab_Page_Container();

        $args['is_parent_tab']=0;
        $args['transparency']=1;
        $args['global']=$global;
        $this->main_tab->add_tab('General Settings','general_addon',array($this, 'output_general_setting_addon'), $args);
        $this->main_tab->add_tab('Advanced Settings','advance_addon',array($this, 'output_advance_setting_addon'), $args);
        $this->main_tab->add_tab('Staging Settings', 'staging_addon', array($this, 'output_staging_setting_addon'), $args);
        $this->main_tab->display();
        ?>
        <?php
        if ($global === false) {
            $save_change_id = 'mwp_wpvivid_setting_general_save_addon';
        } else {
            $save_change_id = 'mwp_wpvivid_global_setting_general_save_addon';
        }
        ?>
        <div><input class="ui green mini button" id="<?php esc_attr_e($save_change_id); ?>" type="button" value="<?php esc_attr_e('Save Changes and Sync'); ?>" /></div>
        <script>
            jQuery('#mwp_wpvivid_setting_general_save_addon').click(function(){
                mwp_wpvivid_set_general_settings_addon();
            });
            jQuery('#mwp_wpvivid_global_setting_general_save_addon').click(function(){
                mwp_wpvivid_set_global_general_settings_addon();
            });
            function mwp_wpvivid_set_general_settings_addon()
            {
                var setting_data = mwp_wpvivid_ajax_data_transfer('mwp-setting-addon');
                var ajax_data = {
                    'action': 'mwp_wpvivid_set_general_setting_addon',
                    'setting': setting_data,
                    'site_id': '<?php echo esc_html($this->site_id); ?>'
                };
                jQuery('#mwp_wpvivid_setting_general_save_addon').css({'pointer-events': 'none', 'opacity': '0.4'});
                mwp_wpvivid_post_request(ajax_data, function (data) {
                    try {
                        var jsonarray = jQuery.parseJSON(data);

                        jQuery('#mwp_wpvivid_setting_general_save_addon').css({'pointer-events': 'auto', 'opacity': '1'});
                        if (jsonarray.result === 'success') {
                            location.reload();
                        }
                        else {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err) {
                        alert(err);
                        jQuery('#mwp_wpvivid_setting_general_save_addon').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('#mwp_wpvivid_setting_general_save_addon').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = mwp_wpvivid_output_ajaxerror('changing base settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function mwp_wpvivid_set_global_general_settings_addon()
            {
                var setting_data = mwp_wpvivid_ajax_data_transfer('mwp-setting-addon');
                var ajax_data = {
                    'action': 'mwp_wpvivid_set_global_general_setting_addon',
                    'setting': setting_data,
                };
                jQuery('#mwp_wpvivid_global_setting_general_save_addon').css({'pointer-events': 'none', 'opacity': '0.4'});
                mwp_wpvivid_post_request(ajax_data, function (data) {
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        jQuery('#mwp_wpvivid_global_setting_general_save_addon').css({'pointer-events': 'auto', 'opacity': '1'});
                        if (jsonarray.result === 'success') {
                            window.location.href = window.location.href + "&synchronize=1&addon=1";
                        }
                        else {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err) {
                        alert(err);
                        jQuery('#mwp_wpvivid_global_setting_general_save_addon').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('#mwp_wpvivid_global_setting_general_save_addon').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = mwp_wpvivid_output_ajaxerror('changing base settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    public function output_general_setting_addon($global){
        $display_local_backup_count = isset($this->setting_addon['wpvivid_common_setting']['max_backup_count']) ? $this->setting_addon['wpvivid_common_setting']['max_backup_count'] : '30';
        $display_local_backup_count = intval($display_local_backup_count);

        $wpvivid_setting_estimate_backup = 'checked';
        if(isset($this->setting_addon['wpvivid_common_setting']['estimate_backup'])){
            $wpvivid_setting_estimate_backup = $this->setting_addon['wpvivid_common_setting']['estimate_backup'] == '1' ? 'checked' : '';
        }

        $wpvivid_clean_old_remote_before_backup = 'checked';
        if(isset($this->setting_addon['wpvivid_common_setting']['clean_old_remote_before_backup'])){
            $wpvivid_clean_old_remote_before_backup = $this->setting_addon['wpvivid_common_setting']['clean_old_remote_before_backup'] == '1' ? 'checked' : '';
        }

        $wpvivid_setting_ismerge = 'checked';
        if(isset($this->setting_addon['wpvivid_common_setting']['ismerge'])){
            $wpvivid_setting_ismerge = $this->setting_addon['wpvivid_common_setting']['ismerge'] == '1' ? 'checked' : '';
        }

        $wpvivid_local_directory = isset($this->setting_addon['wpvivid_local_setting']['path']) ? $this->setting_addon['wpvivid_local_setting']['path'] : 'wpvividbackups';
        ?>
        <div style="margin-top: 10px;">
            <div class="postbox mwp-wpvivid-setting-block mwp-wpvivid-block-bottom-space">
                <div style="margin-bottom: 20px;"><strong><?php _e('General Settings'); ?></strong></div>
                <div>
                    <div class="mwp-wpvivid-block-bottom-space mwp-wpvivid-font-right-space" style="float: left;">
                        <input type="text" option="mwp-setting-addon" name="mwp_max_local_backup_count_addon" id="mwp_wpvivid_local_backup_count" value="<?php esc_attr_e($display_local_backup_count); ?>" style="width: 50px;" />
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space mwp-wpvivid-schedule-font-fix" style="float: left;">
                        <strong><?php _e('Backups retained (localhost)'); ?></strong>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="checkbox" option="mwp-setting-addon" name="mwp_clean_old_remote_before_backup_addon" <?php esc_attr_e($wpvivid_clean_old_remote_before_backup); ?> />
                        <span><?php _e('Remove the oldest backups stored in remote storage before creating a backup if the current backups reached the limit of backup retention for remote storage. It is recommended to uncheck this option if there is a unstable connection between your site and remote storge'); ?></span>
                    </label>
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="checkbox" option="mwp-setting-addon" name="mwp_estimate_backup_addon" <?php esc_attr_e($wpvivid_setting_estimate_backup); ?> />
                        <span><?php _e('Calculate the size of files, folder and database before backing up'); ?></span>
                    </label>
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="checkbox" option="mwp-setting-addon" name="mwp_ismerge_addon" <?php esc_attr_e($wpvivid_setting_ismerge); ?> />
                        <span><?php _e('Merge all the backup files into single package when a backup completes. This will save great disk spaces, though takes longer time. We recommended you check the option especially on sites with insufficient server resources.'); ?></span>
                    </label>
                </div>
                <div class="mwp-wpvivid-block-bottom-space"><?php _e('Name your folder, this folder must be writable for creating backup files.'); ?></div>
                <div class="mwp-wpvivid-block-bottom-space"><input type="text" class="all-options" option="mwp-setting-addon" name="mwp_path_addon" value="<?php esc_attr_e($wpvivid_local_directory); ?>" onkeyup="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" /></div>
                <div>
                    <div class="mwp-wpvivid-block-bottom-space mwp-wpvivid-font-right-space" style="float: left;"><?php _e('Child-Site Storage Directory: '); ?></div>
                    <div class="mwp-wpvivid-block-bottom-space" style="float: left;"><?php _e('http(s)://child-site/wp-content/'); ?></div>
                    <div class="mwp-wpvivid-block-bottom-space" style="float: left;"><?php _e($wpvivid_local_directory); ?></div>
                    <div style="clear: both;"></div>
                </div>
            </div>
        </div>
        <?php
    }

    public function output_advance_setting_addon($global){
        $wpvivid_lower_resource_mode = '';
        if(isset($this->setting_addon['wpvivid_compress_setting']['subpackage_plugin_upload'])){
            $wpvivid_lower_resource_mode = $this->setting_addon['wpvivid_compress_setting']['subpackage_plugin_upload'] == '1' ? 'checked' : '';
        }

        $wpvivid_setting_no_compress='';
        $wpvivid_setting_compress='';
        if(isset($this->setting_addon['wpvivid_compress_setting']['no_compress'])) {
            if ($this->setting_addon['wpvivid_compress_setting']['no_compress']) {
                $wpvivid_setting_no_compress = 'checked';
            } else {
                $wpvivid_setting_compress = 'checked';
            }
        }
        else{
            $wpvivid_setting_no_compress = 'checked';
        }

        $wpvivid_max_file_size = isset($this->setting_addon['wpvivid_compress_setting']['max_file_size']) ? $this->setting_addon['wpvivid_compress_setting']['max_file_size'] : '0M';
        $wpvivid_exclude_file_size = isset($this->setting_addon['wpvivid_compress_setting']['exclude_file_size']) ? $this->setting_addon['wpvivid_compress_setting']['exclude_file_size'] : 0;
        $wpvivid_max_exec_time =  isset($this->setting_addon['wpvivid_common_setting']['max_execution_time']) ? $this->setting_addon['wpvivid_common_setting']['max_execution_time'] : 900;
        $wpvivid_memory_limit = isset($this->setting_addon['wpvivid_common_setting']['memory_limit']) ? $this->setting_addon['wpvivid_common_setting']['memory_limit'] : '256M';
        $wpvivid_resume_time = isset($this->setting_addon['wpvivid_common_setting']['max_resume_count']) ? $this->setting_addon['wpvivid_common_setting']['max_resume_count'] : '6';
        $wpvivid_resume_time = intval($wpvivid_resume_time);

        $db_method_wpdb = 'checked';
        $db_method_pdo  = '';
        if(isset($this->setting_addon['wpvivid_common_setting']['db_connect_method'])){
            if($this->setting_addon['wpvivid_common_setting']['db_connect_method'] === 'wpdb'){
                $db_method_wpdb = 'checked';
                $db_method_pdo  = '';
            }
            else{
                $db_method_wpdb = '';
                $db_method_pdo  = 'checked';
            }
        }
        ?>
        <div style="margin-top: 10px;">
            <div class="postbox mwp-wpvivid-setting-block mwp-wpvivid-block-bottom-space">
                <div style="margin-bottom: 20px;"><strong><?php _e('Advanced Settings'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Enable the option when backup failed.', 'wpvivid'); ?></strong><?php _e(' Special optimization for web hosting/shared hosting', 'wpvivid'); ?></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="checkbox" option="mwp-setting-addon" name="mwp_subpackage_plugin_upload_addon" <?php esc_attr_e($wpvivid_lower_resource_mode); ?> />
                        <span><strong><?php _e('Enable optimization mode for web hosting/shared hosting', 'wpvivid'); ?></strong></span>
                    </label>
                </div>
                <div class="mwp-wpvivid-block-bottom-space"><?php _e('Enabling this option can improve the backup success rate, but it will take more time for backup.', 'wpvivid'); ?></div>

                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Database access method'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="radio" option="mwp-setting-addon" name="mwp_db_connect_method_addon" value="wpdb" <?php esc_attr_e($db_method_wpdb); ?> />
                        <span class="mwp-wpvivid-block-right-space"><strong>WPDB</strong></span><span><?php _e('WPDB option has a better compatibility, but the speed of backup and restore is slower.', 'wpvivid'); ?></span>
                    </label>
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="radio" option="mwp-setting-addon" name="mwp_db_connect_method_addon" value="pdo" <?php esc_attr_e($db_method_pdo); ?> />
                        <span class="mwp-wpvivid-block-right-space"><strong>PDO</strong></span><span><?php _e('It is recommended to choose PDO option if pdo_mysql extension is installed on your server, which lets you backup and restore your site faster.', 'wpvivid'); ?></span>
                    </label>
                </div>

                <div>
                    <div class="mwp-wpvivid-block-bottom-space mwp-wpvivid-block-right-space" style="float: left;">
                        <label>
                            <input type="radio" option="mwp-setting-addon" name="mwp_no_compress_addon" value="1" <?php esc_attr_e($wpvivid_setting_no_compress); ?> />
                            <span title="<?php esc_attr_e( 'It will cause a lower CPU Usage and is recommended in a web hosting/ shared hosting environment.'); ?>"><?php _e('Only Archive without compressing'); ?></span>
                        </label>
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space mwp-wpvivid-block-right-space" style="float: left;">
                        <label>
                            <input type="radio" option="mwp-setting-addon" name="mwp_no_compress_addon" value="0" <?php esc_attr_e($wpvivid_setting_compress); ?> />
                            <span title="<?php esc_attr_e( 'It will cause a higher CPU Usage and is recommended in a VPS/ dedicated hosting environment.'); ?>"><?php _e('Compress and Archive'); ?></span>
                        </label>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div>
                    <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Compress Files Every'); ?></strong></div>
                    <div class="mwp-wpvivid-block-bottom-space">
                        <input type="text" class="all-options mwp-wpvivid-font-right-space" option="mwp-setting-addon" name="mwp_max_file_size_addon" value="<?php esc_attr_e(str_replace('M', '', $wpvivid_max_file_size)); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space"><?php _e('Some web hosting providers limit large zip files (e.g. 200MB), and therefore splitting your backup into many parts is an ideal way to avoid hitting the limitation if you are running a big website. Please try to adjust the value if you are encountering backup errors. If you use a value of 0 MB, any backup files won\'t be split.'); ?></div>

                    <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Exclude the files which are larger than'); ?></strong></div>
                    <div class="mwp-wpvivid-block-bottom-space">
                        <input type="text" class="all-options mwp-wpvivid-font-right-space" option="mwp-setting-addon" name="mwp_exclude_file_size_addon" value="<?php esc_attr_e($wpvivid_exclude_file_size); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space"><?php _e('Using the option will ignore the file larger than the certain size in MB when backing up, \'0\' (zero) means unlimited.'); ?></div>

                    <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('PHP script execution timeout'); ?></strong></div>
                    <div class="mwp-wpvivid-block-bottom-space">
                        <input type="text" class="all-options mwp-wpvivid-font-right-space" option="mwp-setting-addon" name="mwp_max_execution_time_addon" value="<?php esc_attr_e($wpvivid_max_exec_time); ?>" onkeyup="value=value.replace(/\D/g,'')" />Seconds
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space"><?php _e('The time-out is not your server PHP time-out. With the execution time exhausted, our plugin will shut the process of backup down. If the progress of backup encounters a time-out, that means you have a medium or large sized website, please try to scale the value bigger.'); ?></div>

                    <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('PHP Memory Limit for backup'); ?></strong></div>
                    <div class="mwp-wpvivid-block-bottom-space">
                        <input type="text" class="all-options mwp-wpvivid-font-right-space" option="mwp-setting-addon" name="mwp_memory_limit_addon" value="<?php esc_attr_e(str_replace('M', '', $wpvivid_memory_limit)); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space"><?php _e('Adjust this value to apply for a temporary PHP memory limit for WPvivid backup plugin to run a backup. We set this value to 256M by default. Increase the value if you encounter a memory exhausted error. Note: some web hosting providers may not support this.'); ?></div>

                    <div class="mwp-wpvivid-block-bottom-space">
                        <strong>Retrying </strong>
                        <select option="mwp-setting-addon" name="mwp_max_resume_count_addon">
                            <?php
                            for($resume_count=3; $resume_count<10; $resume_count++){
                                if($resume_count === $wpvivid_resume_time){
                                    _e('<option selected="selected" value="'.$resume_count.'">'.$resume_count.'</option>');
                                }
                                else{
                                    _e('<option value="'.$resume_count.'">'.$resume_count.'</option>');
                                }
                            }
                            ?>
                        </select><strong><?php _e(' times when encountering a time-out error', 'wpvivid'); ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function output_staging_setting_addon($global){
        $wpvivid_staging_db_insert_count = isset($this->setting_addon['wpvivid_staging_options']['staging_db_insert_count']) ? $this->setting_addon['wpvivid_staging_options']['staging_db_insert_count'] : 10000;
        $wpvivid_staging_db_replace_count = isset($this->setting_addon['wpvivid_staging_options']['staging_db_replace_count']) ? $this->setting_addon['wpvivid_staging_options']['staging_db_replace_count'] : 5000;
        $wpvivid_staging_file_copy_count = isset($this->setting_addon['wpvivid_staging_options']['staging_file_copy_count']) ? $this->setting_addon['wpvivid_staging_options']['staging_file_copy_count'] : 500;
        $wpvivid_staging_exclude_file_size = isset($this->setting_addon['wpvivid_staging_options']['staging_exclude_file_size']) ? $this->setting_addon['wpvivid_staging_options']['staging_exclude_file_size'] : 30;
        $wpvivid_staging_memory_limit = isset($this->setting_addon['wpvivid_staging_options']['staging_memory_limit']) ? $this->setting_addon['wpvivid_staging_options']['staging_memory_limit'] : '256M';
        $wpvivid_staging_memory_limit = str_replace('M', '', $wpvivid_staging_memory_limit);
        $wpvivid_staging_max_execution_time = isset($this->setting_addon['wpvivid_staging_options']['staging_max_execution_time']) ? $this->setting_addon['wpvivid_staging_options']['staging_max_execution_time'] : 900;
        $wpvivid_staging_resume_count = isset($this->setting_addon['wpvivid_staging_options']['staging_resume_count']) ? $this->setting_addon['wpvivid_staging_options']['staging_resume_count'] : '6';
        ?>
        <div style="margin-top: 10px;">
            <div class="postbox mwp-wpvivid-setting-block mwp-wpvivid-block-bottom-space">
                <div style="margin-bottom: 20px;"><strong><?php _e('Staging Settings'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('DB Copy Count'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <input type="text" class="all-options" option="mwp-setting-addon" name="mwp_staging_db_insert_count_addon" value="<?php esc_attr_e($wpvivid_staging_db_insert_count); ?>"
                           placeholder="10000" onkeyup="value=value.replace(/\D/g,'')" />
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <?php _e('Number of DB rows, that are copied within one ajax query. The higher value makes the database copy process faster.
                Please try a high value to find out the highest possible value. If you encounter timeout errors, try lower values until no
                more errors occur.'); ?>
                </div>

                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('DB Replace Count'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <input type="text" class="all-options" option="mwp-setting-addon" name="mwp_staging_db_replace_count_addon" value="<?php esc_attr_e($wpvivid_staging_db_replace_count); ?>"
                           placeholder="5000" onkeyup="value=value.replace(/\D/g,'')" />
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <?php _e('Number of DB rows, that are processed within one ajax query. The higher value makes the DB replacement process faster. 
                If timeout erros occur, decrease the value because this process consumes a lot of memory.'); ?>
                </div>

                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('File Copy Count'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <input type="text" class="all-options" option="mwp-setting-addon" name="mwp_staging_file_copy_count_addon" value="<?php esc_attr_e($wpvivid_staging_file_copy_count); ?>"
                           placeholder="500" onkeyup="value=value.replace(/\D/g,'')" />
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <?php _e('Number of files to copy that will be copied within one ajax request. The higher value makes the file file copy process 
                faster. Please try a high value to find out the highest possible value. If you encounter timeout errors, try lower values until 
                no more errors occur.'); ?>
                </div>

                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Max File Size'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <input type="text" class="all-options" option="mwp-setting-addon" name="mwp_staging_exclude_file_size_addon" value="<?php esc_attr_e($wpvivid_staging_exclude_file_size); ?>"
                           placeholder="30" onkeyup="value=value.replace(/\D/g,'')" />
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <?php _e('Maximum size of the files copied to a staging site. All files larger than this value will be ignored. If you set the value
                 of 0 MB, all files will be copied to a staging site.'); ?>
                </div>

                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Staging Memory Limit'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <input type="text" class="all-options" option="mwp-setting-addon" name="mwp_staging_memory_limit_addon" value="<?php esc_attr_e($wpvivid_staging_memory_limit); ?>"
                           placeholder="256" onkeyup="value=value.replace(/\D/g,'')" />MB
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <?php _e('Adjust this value to apply for a temporary PHP memory limit for WPvivid backup plugin while creating a staging site.
                We set this value to 256M by default. Increase the value if you encounter a memory exhausted error. Note: some web hosting
                providers may not support this.'); ?>
                </div>

                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('PHP script execution timeout'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <input type="text" class="all-options" option="mwp-setting-addon" name="mwp_staging_max_execution_time_addon" value="<?php esc_attr_e($wpvivid_staging_max_execution_time); ?>"
                           placeholder="900" onkeyup="value=value.replace(/\D/g,'')" />
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <?php _e('The time-out is not your server PHP time-out. With the execution time exhausted, our plugin will shut down the progress of 
                creating a staging site. If the progress  encounters a time-out, that means you have a medium or large sized website. Please try to
                scale the value bigger.'); ?>
                </div>

                <div class="mwp-wpvivid-block-bottom-space">
                    <strong>Retrying </strong>
                    <select option="mwp-setting-addon" name="mwp_staging_resume_count_addon">
                        <?php
                        for($resume_count=3; $resume_count<10; $resume_count++){
                            if($resume_count === $wpvivid_staging_resume_count){
                                _e('<option selected="selected" value="'.$resume_count.'">'.$resume_count.'</option>');
                            }
                            else{
                                _e('<option value="'.$resume_count.'">'.$resume_count.'</option>');
                            }
                        }
                        ?>
                    </select><strong><?php _e(' times when encountering a time-out error', 'wpvivid'); ?></strong>
                </div>
            </div>
        </div>
        <?php
    }

    public function mwp_wpvivid_setting_page($global){
        if(!class_exists('Mainwp_WPvivid_Tab_Page_Container'))
            include_once MAINWP_WPVIVID_EXTENSION_PLUGIN_DIR . '/includes/wpvivid-backup-mainwp-tab-page-container.php';
        $this->main_tab=new Mainwp_WPvivid_Tab_Page_Container();

        $args['is_parent_tab']=0;
        $args['transparency']=1;
        $args['global']=$global;
        $this->main_tab->add_tab('General Settings','general',array($this, 'output_general_setting'), $args);
        $this->main_tab->add_tab('Advanced Settings','advance',array($this, 'output_advance_setting'), $args);
        $this->main_tab->display();
        ?>
        <?php
        if ($global === false) {
            $save_change_id = 'mwp_wpvivid_setting_general_save';
        } else {
            $save_change_id = 'mwp_wpvivid_global_setting_general_save';
        }
        ?>
        <div><input class="ui green mini button" id="<?php esc_attr_e($save_change_id); ?>" type="button" value="<?php esc_attr_e('Save Changes'); ?>" /></div>

        <script>
            jQuery('#mwp_wpvivid_setting_general_save').click(function(){
                mwp_wpvivid_set_general_settings();
            });
            jQuery('#mwp_wpvivid_global_setting_general_save').click(function(){
                mwp_wpvivid_set_global_general_settings();
            });
            function mwp_wpvivid_set_general_settings()
            {
                var setting_data = mwp_wpvivid_ajax_data_transfer('mwp-setting');
                var ajax_data = {
                    'action': 'mwp_wpvivid_set_general_setting',
                    'setting': setting_data,
                    'site_id': '<?php echo esc_html($this->site_id); ?>'
                };
                jQuery('#mwp_wpvivid_setting_general_save').css({'pointer-events': 'none', 'opacity': '0.4'});
                mwp_wpvivid_post_request(ajax_data, function (data) {
                    try {
                        var jsonarray = jQuery.parseJSON(data);

                        jQuery('#mwp_wpvivid_setting_general_save').css({'pointer-events': 'auto', 'opacity': '1'});
                        if (jsonarray.result === 'success') {
                            location.reload();
                        }
                        else {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err) {
                        alert(err);
                        jQuery('#mwp_wpvivid_setting_general_save').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('#mwp_wpvivid_setting_general_save').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = mwp_wpvivid_output_ajaxerror('changing base settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function mwp_wpvivid_set_global_general_settings()
            {
                var setting_data = mwp_wpvivid_ajax_data_transfer('mwp-setting');
                var ajax_data = {
                    'action': 'mwp_wpvivid_set_global_general_setting',
                    'setting': setting_data,
                };
                jQuery('#mwp_wpvivid_global_setting_general_save').css({'pointer-events': 'none', 'opacity': '0.4'});
                mwp_wpvivid_post_request(ajax_data, function (data) {
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        jQuery('#mwp_wpvivid_global_setting_general_save').css({'pointer-events': 'auto', 'opacity': '1'});
                        if (jsonarray.result === 'success') {
                            window.location.href = window.location.href + "&synchronize=1&addon=0";
                        }
                        else {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err) {
                        alert(err);
                        jQuery('#mwp_wpvivid_global_setting_general_save').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('#mwp_wpvivid_global_setting_general_save').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = mwp_wpvivid_output_ajaxerror('changing base settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    public function output_general_setting($global){
        $display_backup_count = isset($this->setting['wpvivid_common_setting']['max_backup_count']) ? $this->setting['wpvivid_common_setting']['max_backup_count'] : '3';
        $display_backup_count = intval($display_backup_count);

        $wpvivid_setting_estimate_backup ='checked';
        if(isset($this->setting['wpvivid_common_setting']['estimate_backup'])){
            $wpvivid_setting_estimate_backup = $this->setting['wpvivid_common_setting']['estimate_backup'] == '1' ? 'checked' : '';
        }

        $wpvivid_setting_ismerge = 'checked';
        if(isset($this->setting['wpvivid_common_setting']['ismerge'])){
            $wpvivid_setting_ismerge = $this->setting['wpvivid_common_setting']['ismerge'] == '1' ? 'checked' : '';
        }

        $wpvivid_local_directory = isset($this->setting['wpvivid_local_setting']['path']) ? $this->setting['wpvivid_local_setting']['path'] : 'wpvividbackups';

        $wpvivid_domain_prefix = 'checked';
        if(isset($this->setting['wpvivid_common_setting']['domain_include'])){
            $wpvivid_domain_prefix = $this->setting['wpvivid_common_setting']['domain_include'] == '1' ? 'checked' : '';
        }
        ?>
        <div style="margin-top: 10px;">
            <div class="postbox mwp-wpvivid-setting-block mwp-wpvivid-block-bottom-space">
                <div style="margin-bottom: 20px;"><strong><?php _e('General Settings'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <select class="mwp-wpvivid-font-right-space" option="mwp-setting" name="mwp_max_backup_count">
                        <?php
                        for($local_count=1; $local_count<8; $local_count++){
                            if($local_count === $display_backup_count){
                                _e('<option selected="selected" value="'.$local_count.'">'.$local_count.'</option>');
                            }
                            else{
                                _e('<option value="'.$local_count.'">'.$local_count.'</option>');
                            }
                        }
                        ?>
                    </select><strong><?php _e('backups retained'); ?></strong>
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="checkbox" option="mwp-setting" name="mwp_estimate_backup" <?php esc_attr_e($wpvivid_setting_estimate_backup); ?> />
                        <span><?php _e('Calculate the size of files, folder and database before backing up'); ?></span>
                    </label>
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="checkbox" option="mwp-setting" name="mwp_ismerge" <?php esc_attr_e($wpvivid_setting_ismerge); ?> />
                        <span><?php _e('Merge all the backup files into single package when a backup completes. This will save great disk spaces, though takes longer time. We recommended you check the option especially on sites with insufficient server resources.'); ?></span>
                    </label>
                </div>
                <div class="mwp-wpvivid-block-bottom-space"><?php _e('Name your folder, this folder must be writable for creating backup files.'); ?></div>
                <div class="mwp-wpvivid-block-bottom-space"><input type="text" class="all-options" option="mwp-setting" name="mwp_path" value="<?php esc_attr_e($wpvivid_local_directory); ?>" onkeyup="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" /></div>
                <div>
                    <div class="mwp-wpvivid-block-bottom-space mwp-wpvivid-font-right-space" style="float: left;"><?php _e('Child-Site Storage Directory: '); ?></div>
                    <div class="mwp-wpvivid-block-bottom-space" style="float: left;"><?php _e('http(s)://child-site/wp-content/'); ?></div>
                    <div class="mwp-wpvivid-block-bottom-space" style="float: left;"><?php _e($wpvivid_local_directory); ?></div>
                    <div style="clear: both;"></div>
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="checkbox" option="mwp-setting" name="mwp_domain_include" <?php esc_attr_e($wpvivid_domain_prefix); ?> />
                        <span><?php _e('Display domain(url) of current site in backup name. (e.g. domain_wpvivid-5ceb938b6dca9_2019-05-27-07-36_backup_all.zip)'); ?></span>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    public function output_advance_setting($global){
        $wpvivid_lower_resource_mode = '';
        if(isset($this->setting['wpvivid_compress_setting']['subpackage_plugin_upload'])){
            $wpvivid_lower_resource_mode = $this->setting['wpvivid_compress_setting']['subpackage_plugin_upload'] == '1' ? 'checked' : '';
        }

        $wpvivid_setting_no_compress='';
        $wpvivid_setting_compress='';
        if($this->setting['wpvivid_compress_setting']['no_compress']) {
            $wpvivid_setting_no_compress='checked';
        }
        else{
            $wpvivid_setting_compress='checked';
        }

        $wpvivid_max_file_size = isset($this->setting['wpvivid_compress_setting']['max_file_size']) ? $this->setting['wpvivid_compress_setting']['max_file_size'] : '0M';
        $wpvivid_exclude_file_size = isset($this->setting['wpvivid_compress_setting']['exclude_file_size']) ? $this->setting['wpvivid_compress_setting']['exclude_file_size'] : 0;
        $wpvivid_max_exec_time =  isset($this->setting['wpvivid_common_setting']['max_execution_time']) ? $this->setting['wpvivid_common_setting']['max_execution_time'] : 900;
        $wpvivid_memory_limit = isset($this->setting['wpvivid_common_setting']['memory_limit']) ? $this->setting['wpvivid_common_setting']['memory_limit'] : '256M';

        $wpvivid_resume_time = isset($this->setting['wpvivid_common_setting']['max_resume_count']) ? $this->setting['wpvivid_common_setting']['max_resume_count'] : '6';
        $wpvivid_resume_time = intval($wpvivid_resume_time);

        $db_method_wpdb = 'checked';
        $db_method_pdo  = '';
        if(isset($this->setting['wpvivid_common_setting']['db_connect_method'])){
            if($this->setting['wpvivid_common_setting']['db_connect_method'] === 'wpdb'){
                $db_method_wpdb = 'checked';
                $db_method_pdo  = '';
            }
            else{
                $db_method_wpdb = '';
                $db_method_pdo  = 'checked';
            }
        }
        ?>
        <div style="margin-top: 10px;">
            <div class="postbox mwp-wpvivid-setting-block mwp-wpvivid-block-bottom-space">
                <div style="margin-bottom: 20px;"><strong><?php _e('Advanced Settings'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Enable the option when backup failed.', 'wpvivid'); ?></strong><?php _e(' Special optimization for web hosting/shared hosting', 'wpvivid'); ?></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="checkbox" option="mwp-setting" name="mwp_subpackage_plugin_upload" <?php esc_attr_e($wpvivid_lower_resource_mode); ?> />
                        <span><strong><?php _e('Enable optimization mode for web hosting/shared hosting', 'wpvivid'); ?></strong></span>
                    </label>
                </div>
                <div class="mwp-wpvivid-block-bottom-space"><?php _e('Enabling this option can improve the backup success rate, but it will take more time for backup.', 'wpvivid'); ?></div>
                <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Database access method'); ?></strong></div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="radio" option="mwp-setting" name="mwp_db_connect_method" value="wpdb" <?php esc_attr_e($db_method_wpdb); ?> />
                        <span class="mwp-wpvivid-block-right-space"><strong>WPDB</strong></span><span><?php _e('WPDB option has a better compatibility, but the speed of backup and restore is slower.', 'wpvivid'); ?></span>
                    </label>
                </div>
                <div class="mwp-wpvivid-block-bottom-space">
                    <label>
                        <input type="radio" option="mwp-setting" name="mwp_db_connect_method" value="pdo" <?php esc_attr_e($db_method_pdo); ?> />
                        <span class="mwp-wpvivid-block-right-space"><strong>PDO</strong></span><span><?php _e('It is recommended to choose PDO option if pdo_mysql extension is installed on your server, which lets you backup and restore your site faster.', 'wpvivid'); ?></span>
                    </label>
                </div>
                <div>
                    <div class="mwp-wpvivid-block-bottom-space mwp-wpvivid-block-right-space" style="float: left;">
                        <label>
                            <input type="radio" option="mwp-setting" name="mwp_no_compress" value="1" <?php esc_attr_e($wpvivid_setting_no_compress); ?> />
                            <span title="<?php esc_attr_e( 'It will cause a lower CPU Usage and is recommended in a web hosting/ shared hosting environment.'); ?>"><?php _e('Only Archive without compressing'); ?></span>
                        </label>
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space mwp-wpvivid-block-right-space" style="float: left;">
                        <label>
                            <input type="radio" option="mwp-setting" name="mwp_no_compress" value="0" <?php esc_attr_e($wpvivid_setting_compress); ?> />
                            <span title="<?php esc_attr_e( 'It will cause a higher CPU Usage and is recommended in a VPS/ dedicated hosting environment.'); ?>"><?php _e('Compress and Archive'); ?></span>
                        </label>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div>
                    <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Compress Files Every'); ?></strong></div>
                    <div class="mwp-wpvivid-block-bottom-space">
                        <input type="text" class="all-options mwp-wpvivid-font-right-space" option="mwp-setting" name="mwp_max_file_size" value="<?php esc_attr_e(str_replace('M', '', $wpvivid_max_file_size)); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space"><?php _e('Some web hosting providers limit large zip files (e.g. 200MB), and therefore splitting your backup into many parts is an ideal way to avoid hitting the limitation if you are running a big website. Please try to adjust the value if you are encountering backup errors. If you use a value of 0 MB, any backup files won\'t be split.'); ?></div>

                    <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('Exclude the files which are larger than'); ?></strong></div>
                    <div class="mwp-wpvivid-block-bottom-space">
                        <input type="text" class="all-options mwp-wpvivid-font-right-space" option="mwp-setting" name="mwp_exclude_file_size" value="<?php esc_attr_e($wpvivid_exclude_file_size); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space"><?php _e('Using the option will ignore the file larger than the certain size in MB when backing up, \'0\' (zero) means unlimited.'); ?></div>

                    <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('PHP script execution timeout'); ?></strong></div>
                    <div class="mwp-wpvivid-block-bottom-space">
                        <input type="text" class="all-options mwp-wpvivid-font-right-space" option="mwp-setting" name="mwp_max_execution_time" value="<?php esc_attr_e($wpvivid_max_exec_time); ?>" onkeyup="value=value.replace(/\D/g,'')" />Seconds
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space"><?php _e('The time-out is not your server PHP time-out. With the execution time exhausted, our plugin will shut the process of backup down. If the progress of backup encounters a time-out, that means you have a medium or large sized website, please try to scale the value bigger.'); ?></div>

                    <div class="mwp-wpvivid-block-bottom-space"><strong><?php _e('PHP Memory Limit for backup'); ?></strong></div>
                    <div class="mwp-wpvivid-block-bottom-space">
                        <input type="text" class="all-options mwp-wpvivid-font-right-space" option="mwp-setting" name="mwp_memory_limit" value="<?php esc_attr_e(str_replace('M', '', $wpvivid_memory_limit)); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                    </div>
                    <div class="mwp-wpvivid-block-bottom-space"><?php _e('Adjust this value to apply for a temporary PHP memory limit for WPvivid backup plugin to run a backup. We set this value to 256M by default. Increase the value if you encounter a memory exhausted error. Note: some web hosting providers may not support this.'); ?></div>

                    <div class="mwp-wpvivid-block-bottom-space">
                        <strong>Retrying </strong>
                        <select option="mwp-setting" name="mwp_max_resume_count">
                            <?php
                            for($resume_count=3; $resume_count<10; $resume_count++){
                                if($resume_count === $wpvivid_resume_time){
                                    _e('<option selected="selected" value="'.$resume_count.'">'.$resume_count.'</option>');
                                }
                                else{
                                    _e('<option value="'.$resume_count.'">'.$resume_count.'</option>');
                                }
                            }
                            ?>
                        </select><strong><?php _e(' times when encountering a time-out error', 'wpvivid'); ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function mwp_wpvivid_synchronize_setting($check_addon)
    {
        global $mainwp_wpvivid_extension_activator;
        $mainwp_wpvivid_extension_activator->render_sync_websites_page('mwp_wpvivid_sync_setting', $check_addon);
        ?>
        <script>
            function mwp_wpvivid_sync_setting()
            {
                var website_ids= [];
                mwp_wpvivid_sync_index=0;
                jQuery('.mwp-wpvivid-sync-row').each(function()
                {
                    jQuery(this).children('td:first').each(function(){
                        if (jQuery(this).children().children().prop('checked')) {
                            var id = jQuery(this).attr('website-id');
                            website_ids.push(id);
                        }
                    });
                });
                if(website_ids.length>0)
                {
                    jQuery('#mwp_wpvivid_sync_setting').css({'pointer-events': 'none', 'opacity': '0.4'});
                    var check_addon = '<?php echo $check_addon; ?>';
                    mwp_wpvivid_sync_site(website_ids,check_addon,'mwp_wpvivid_sync_setting','Extensions-Wpvivid-Backup-Mainwp&tab=settings','mwp_wpvivid_settings_tab');
                }
            }
            jQuery('#mwp_wpvivid_sync_setting').click(function(){
                mwp_wpvivid_sync_setting();
            });
        </script>
        <?php
    }
}