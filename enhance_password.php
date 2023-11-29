<?php

/*

Developer: SS88 LLC
Developer Website: https://ss88.us
Licensed under MIT License

*/

class enhance_password extends rcube_plugin {

    private $rc;
    public $task = '?(?!logout).*';
    public $noframe = true;
    public $noajax  = true;

    function init() {

        $this->rc = rcmail::get_instance();
        $this->load_config();
        $this->add_texts('localization/');

        if ($this->rc->task == 'settings') {

            $this->add_hook('settings_actions', [$this, 'settings_actions']);

            $this->register_action('plugin.enhance_password', [$this, 'password_init']);
            $this->register_action('plugin.enhance_password-save', [$this, 'password_save']);

        }

    }

    function settings_actions($args) {

        $args['actions'][] = [
            'action' => 'plugin.enhance_password',
            'class'  => 'password',
            'label'  => 'password',
            'title'  => 'password',
            'domain' => 'password',
        ];

        return $args;

    }

    function password_init() {

        $this->register_handler('plugin.body', [$this, 'show_password_form']);
        $this->rc->output->send('plugin');

    }

    function show_password_form() {

        $this->rc->output->add_label(
            'enhance_password.nopassword',
            'enhance_password.passwordsdonotmatch',
            'enhance_password.noapiset',
            'enhnace_password.passwordvalidation'
        );

        $submit_button = $this->rc->output->button([
                'command' => 'plugin.enhance_password-save',
                'class'   => 'button mainaction submit',
                'label'   => 'save',
        ]);
        $form_buttons = html::p(['class' => 'formbuttons footerleft'], $submit_button);

        $table = new html_table(['cols' => 2, 'class' => 'propform']);

        $field_id = 'newpasswd';
        $input_newpasswd = new html_passwordfield([
                'name'         => '_newpasswd',
                'id'           => $field_id,
                'size'         => 20,
                'autocomplete' => 'off',
        ]);

        $table->add('title', html::label($field_id, $this->gettext('newpassword')));
        $table->add(null, $input_newpasswd->show());

        $field_id = 'confpasswd';
        $input_confpasswd = new html_passwordfield([
                'name'         => '_confpasswd',
                'id'           => $field_id,
                'size'         => 20,
                'autocomplete' => 'off',
        ]);

        $table->add('title', html::label($field_id, $this->gettext('confirmpassword')));
        $table->add(null, $input_confpasswd->show());

        $this->rc->output->add_gui_object('passform', 'password-form');

        $form = $this->rc->output->form_tag([
                'id'     => 'password-form',
                'name'   => 'password-form',
                'method' => 'post',
                'action' => './?_task=settings&_action=plugin.enhance_password-save',
            ],
            $table->show()
        );

        $this->include_script('change_password.js');

        return html::div(['class' => 'box formcontainer scroller'], html::div(['class' => 'boxcontent formcontent'], html::p(NULL, $this->gettext('infotext')) . $form) . $form_buttons);

    }

    function password_save() {

        $this->register_handler('plugin.body', [$this, 'show_password_form']);
        $this->rc->output->set_pagetitle($this->gettext('changepassword'));

        $orchd_url = $this->rc->config->get('orchd_url');
        $orchd_key = $this->rc->config->get('orchd_key');

        if (!isset($_POST['_newpasswd']) || !strlen($_POST['_newpasswd'])) {
            $this->rc->output->command('display_message', $this->gettext('nopassword'), 'error');
        }
        elseif(empty($orchd_key) || empty($orchd_url)) {
            $this->rc->output->command('display_message', $this->gettext('noapiset'), 'error');
        }
        else {

            $email = $this->rc->get_user_name();
            $curpwd = $this->rc->get_user_password();
            
            $newpwd = rcube_utils::get_input_value('_newpasswd', rcube_utils::INPUT_POST, true);
            $conpwd = rcube_utils::get_input_value('_confpasswd', rcube_utils::INPUT_POST, true);

            if ($conpwd != $newpwd) {
                $this->rc->output->command('display_message', $this->gettext('passwordsdonotmatch'), 'error');
            }
            elseif (strlen($newpwd) < 10 || !preg_match('/[^a-zA-Z\d]/', $newpwd)) {
                $this->rc->output->command('display_message', $this->gettext('passwordvalidation'), 'error');
            }
            else {

                $headers = array(
                    'Address: ' . $email,
                    'Password: ' . $curpwd,
                    'Authorization: Bearer ' . $orchd_key,
                    'Content-Type: application/json'
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $orchd_url . '/email-client/password');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['password' => $newpwd]));
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                $data = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $json = json_decode($data, true);

                if (!$data) {

                    $this->rc->output->command('display_message', $this->gettext('internalerror'), 'error');

                }

                if($httpcode==200) {

                    rcube::write_log('enhance_password', sprintf('Password changed for user %s (ID: %d)', $email, $this->rc->user->ID));
                    
                    $_SESSION['password'] = $this->rc->encrypt($newpwd);

                    $this->rc->output->command('display_message', $this->gettext('saved'), 'confirmation');

                }
                else {

                    $err_msg = 'API Error: (Code ' . $httpcode . ') ' . $json['message'];
                    rcube::write_log('enhance_password', sprintf('Failed to change password for user %s (ID: %d), reason: %s', $email, $this->rc->user->ID, $err_msg));
                    $this->rc->output->command('display_message', $err_msg, 'error');

                }

            }

        }

        $this->rc->overwrite_action('plugin.enhance_password');
        $this->rc->output->send('plugin');

    }

}

?>