<?php
/**
 * View File
 *
 * @package Premium SEO Pack
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );
if ( ! isset( $view ) ) {
	exit();
}
?>
<div id="psp_blocklogin" class="col-md-6 text-center no-p">
    <div class="panel panel-transparent">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo esc_html__( 'Connect with Squirrly.co Server', _PSP_PLUGIN_NAME_ ); ?></h3>
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <ul style="display: none;">
                    <li>
                        <div class="sq_error psp_error" style="display: none"></div>
                        <div class="sq_message psp_message" style="width: auto; display: none;"></div>
                    </li>
                    <li class="col-md-12 m-b-xxs">
                        <div class="col-md-3 p-v-xxs"><?php echo esc_html__( 'Email:', _PSP_PLUGIN_NAME_ ); ?></div>
                        <div class="col-md-6"><input class="form-control" type="text" id="sq_user" name="sq_user"/>
                        </div>
                    </li>
                    <li class="col-md-12 m-b-xxs">
                        <div class="col-md-3 p-v-xxs"><?php echo esc_html__( 'Password:', _PSP_PLUGIN_NAME_ ); ?></div>
                        <div class="col-md-6"><input class="form-control" type="password" id="sq_password"
                                                     name="sq_password"/></div>
                    </li>
                    <li class="col-md-12 m-b-xxs">
                        <button class="btn btn-success save m-b-xxs"
                                id="sq_login"><?php echo esc_html__( 'Login', _PSP_PLUGIN_NAME_ ); ?></button>
                    </li>
                    <li>
                        <a id="sq_signup" href="javascript:void(0);" target="_blank" title="<?php echo esc_attr__( 'Register', _PSP_PLUGIN_NAME_ ); ?>"><?php echo esc_html__( 'Register to Squirrly.co', _PSP_PLUGIN_NAME_ ); ?></a>
                        |
                        <a href="<?php echo esc_url(_PSP_DASH_URL_ . 'login/?action=lostpassword') ?>" target="_blank" title="<?php echo esc_attr__( 'Lost password?', _PSP_PLUGIN_NAME_ ); ?>"><?php echo esc_html__( 'Lost password', _PSP_PLUGIN_NAME_ ); ?></a>
                    </li>
                </ul>
                <div id="sq_autologin" style="align-content: center">
                    <div class="panel panel-transparent">
                        <div class="panel-heading">
                            <h3 class="m-t-sm"><?php echo esc_html__( 'Enter your email', _PSP_PLUGIN_NAME_ ); ?><span
                                        id="sq_register_wait"></span></h3>

                        </div>
                        <div class="panel-body">
                            <div class="sq_error psp_error" style="display: none"></div>
                            <div id="sq_register_email" class="col-md-12 m-b-xxs">
                                <div class="col-md-3 p-v-md"><?php echo esc_html__( 'Your Email:', _PSP_PLUGIN_NAME_ ); ?></div>
                                <div class="col-md-9 p-v-xxs">
                                    <input class="form-control input-lg" style="width: 100%" type="text" id="sq_email" name="sq_email" value="<?php $c_user = wp_get_current_user(); echo esc_attr($c_user->user_email); ?>"/>
                                </div>
                            </div>
                            <button class="btn btn-success save  m-b-xxs" id="sq_loginimage"><?php echo esc_html__( 'Sign Up', _PSP_PLUGIN_NAME_ ); ?></button>
                            <div><a href="javascript:void(0);" id="sq_signin"><?php echo esc_html__( 'I already have an account', _PSP_PLUGIN_NAME_ ); ?></a>
                            </div>
                            <div class="small m-t-lg">
                                <em><?php echo esc_html__( "This email it's just to connect you to Squirrly.co. You will not get spammy emails from us.", _PSP_PLUGIN_NAME_ ); ?></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="sq_login_success" style="display: none;">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo esc_html__( "Congratulations! You're connected", _PSP_PLUGIN_NAME_ ); ?></h3>
            </div>
            <div class="panel-body">
                <div class="col-md-12 text-center p-v-lg"><img src="<?php echo esc_url(_PSP_THEME_URL_ . 'img/settings/logo.png') ?>" alt="" style="max-width: 100%; max-height: 315px;"></div>
                <div class="col-md-12 m-t-md m-b-xxs">
                    <button class="btn btn-success save m-t-md m-b-xxs">
                        <a href="javascript:location.reload();" style="color: white"><?php echo esc_html__( 'Start using Premium SEO Pack', _PSP_PLUGIN_NAME_ ); ?></a>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        //Call the autologin
        var __invalid_email = '<?php echo esc_html__( 'The email address is invalid!', _PSP_PLUGIN_NAME_ ); ?>';
        var __try_again = '<?php echo esc_html__( 'Click on Sign Up button and try again ...', _PSP_PLUGIN_NAME_ ); ?>';
        var __error_login = '<?php echo esc_html__( 'An error occured while logging in!', _PSP_PLUGIN_NAME_ ); ?>';
        var __connecting = '<?php echo esc_html__( 'Connecting ...', _PSP_PLUGIN_NAME_ ); ?>';
        jQuery('#sq_loginimage').on('click', function () {
            sq_autoLogin();
        });

        //listenLogin(); //listen the login
    </script>
</div>