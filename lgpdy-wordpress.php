<?php
/*
Plugin Name: LGPDY
Plugin URI: https://lgpdy.com?utm_source=Wordpress
Description: Lgpdy allows your site to get and manage cookie consents
Version: 0.0.4
Author: LGPDY
License: GPLv2 or later
Text Domain: lgpdy
*/

function lgpdy_general_config() {
    register_setting( 
        'lgpdy_admin_options',
        'account_id',
        [
            'sanitize_callback' => function ( $value) {
                if ( ! preg_match('/^[1-9][0-9]*$/', $value)) {
                    add_settings_error( 
                        'account_id', 
                        esc_attr('account_id_error'),
                        'LGPDY account_id está no formato errado',
                        'error'
                    );
                    return get_option( 'account_id' );
                }

                return $value;
            },
        ]
    );

    add_settings_section(
        'lgpdy_section',
        'LGPDY',
        function () {
            echo '<h4>Insira aqui seu <b>account_id</b></h4>';
        },
        'lgpdy_admin_options'
    );
    add_settings_field(
        'account_id',
        'Account ID',
        function ( $args ) {
            $options = get_option( 'account_id' )
            ?>
                <input 
                    type="text" 
                    name="account_id"
                    id="<?php echo esc_attr( $args['label_for'] ); ?>"
                    value="<?php echo esc_attr( $options )?>"
                >
            <?php
        },
        'lgpdy_admin_options',
        'lgpdy_section',
        [
            'label_for' => 'account_id',
            'class' => 'minha-classe'
        ]
    );
}
add_action('admin_init', 'lgpdy_general_config');

function lgpdy_html_generator() {
    ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php 
                    settings_fields( 'lgpdy_admin_options');
                    do_settings_sections( 'lgpdy_admin_options');
                    submit_button();
                ?>
            </form>
        </div>
    <?php
}

function lgpdy_config() {
    add_options_page(
        'Minhas configurações',
        'LGPDY',
        'manage_options',
        'lgpdy-configurações',
        'lgpdy_html_generator'
    );
}
add_action('admin_menu', 'lgpdy_config');

include('includes/lgpdy_activate.php');

function lgpdy_script() {
    $options = get_option( 'account_id' );
    ?>
    <style>
        .lgpdy-modal-header-close h1:before, 
        .lgpdy-modal-header-close h2:before,
        #modalHeader h1:before,
        .lgpdy-category-body h2:before,
        .body-cookie-content h2:before  {
            background-color: unset;
            content: none;
        }
        .lgpdy-modal-header-close {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-preferences::hover {
            background-color: #0043F6;
        }
    </style>
    <script>
        (function (){
            var s = document.createElement("script");
            s.type = "text/javascript";
            s.async = true;
            s.src ="https://www.lgpdy.com/v2/embed-banner.js";
            s.id = "lgpdy-sc-banner";
            s.setAttribute("lgpdy-banner-id", <?php echo $options ?>);
            var x = document.getElementsByTagName("script")[0];
            x.parentNode.insertBefore(s, x);
            })();
    </script>
    <?php
}
add_action( 'wp_head', 'lgpdy_script' );
register_activation_hook(__FILE__, 'lgpdy_activate');

?>