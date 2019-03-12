
<div id="tabs<?php echo $tab_id?>">
    <form method="post" enctype="multipart/form-data" action="<?php bloginfo( 'siteurl' ); ?>/wp-admin/admin.php?page=payment-methods&active_tab=tabs<?php echo $tab_id; ?>">
        <table width="100%" class="sitemile-table">
            <?php do_action('wpj_stripe_add_tab_content'); ?>
            <tr>
                <td></td>
                <td><h2><?php _e("General Settings", "jobmid"); ?></h2></td>
                <td></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet(); ?></td>
                <td valign="top"><?php _e( 'Midtrans Gateway Note:', 'jobmid' ); ?></td>
                <td>
                    <p>
                        <strong><?php _e( 'To get Midtrans Keys:', 'jobmid' ); ?></strong><br>
                        <?php _e( 'Midtrans Dashboard, Setting -> Configuration', 'jobmid' ); ?>
                    </p>
                    <p>
                        <?php _e( 'Please set your payment HTTP Notification URLs on your Midtrans Dashboard', 'jobmid' ); ?>
                    </p>
                    <p>
                        <strong><?php _e( 'Payment Notification URL', 'jobmid' ); ?></strong><br>
                        <code><?php echo get_bloginfo( 'url' ) . '/?payment_response=midtrans&action=notification'; ?></code>
                    </p>
                    <p>
                        <strong><?php _e( 'Finish Redirect URL', 'jobmid' ); ?></strong><br>
                        <code><?php echo get_bloginfo( 'url' ) . '/?payment_response=midtrans&action=finish'; ?></code>
                    </p>
                    <p>
                        <strong><?php _e( 'Unfinish Redirect URL', 'jobmid' ); ?></strong><br>
                        <code><?php echo get_bloginfo( 'url' ) . '/?payment_response=midtrans&action=unfinish'; ?></code>
                    </p>
                    <p>
                        <strong><?php _e( 'Error Redirect URL', 'jobmid' ); ?></strong><br>
                        <code><?php echo get_bloginfo( 'url' ) . '/?payment_response=midtrans&action=error'; ?></code>
                    </p>
                </td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Enable/Disable Midtrans payment gateway', 'jobmid') ); ?></td>
                <td width="200"><?php _e( 'Enable:', 'jobmid' ); ?></td>
                <td><?php echo wpjobster_get_option_drop_down( $arr, 'wpjobster_midtrans_enable', 'no' ); ?></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Enable/Disable Top Up', 'jobmid') ); ?></td>
                <td width="200"><?php _e( 'Enable Top Up:', 'jobmid' ); ?></td>
                <td><?php echo wpjobster_get_option_drop_down( $arr, 'wpjobster_midtrans_enable_topup', 'no' ); ?></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Enable/Disable Featured', 'jobmid') ); ?></td>
                <td width="200"><?php _e( 'Enable Featured:', 'jobmid' ); ?></td>
                <td><?php echo wpjobster_get_option_drop_down( $arr, 'wpjobster_midtrans_enable_featured', 'no' ); ?></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Enable/Disable Custom Extra', 'jobmid') ); ?></td>
                <td width="200"><?php _e( 'Enable Custom Extra:', 'jobmid' ); ?></td>
                <td><?php echo wpjobster_get_option_drop_down( $arr, 'wpjobster_midtrans_enable_custom_extra', 'no' ); ?></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Enable/Disable Tips', 'jobmid') ); ?></td>
                <td width="200"><?php _e( 'Enable Tips:', 'jobmid' ); ?></td>
                <td><?php echo wpjobster_get_option_drop_down( $arr, 'wpjobster_midtrans_enable_tips', 'no' ); ?></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Enable/Disable Midtrans sandbox mode.', 'jobmid' ) ); ?></td>
                <td width="200"><?php _e( 'Enable Sandbox Mode:', 'jobmid' ); ?></td>
                <td><?php echo wpjobster_get_option_drop_down( $arr, 'wpjobster_midtrans_enable_sandbox', 'yes' ); ?></td>
            </tr>

            <tr>
                <?php // _enable and _button_caption are mandatory ?>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Put the Midtrans button caption you want user to see on purchase page', 'jobmid' ) ); ?></td>
                <td><?php _e( 'Midtrans Button Caption:', 'jobmid' ); ?></td>
                <td><input type="text" size="45" name="wpjobster_<?php echo $this->unique_slug; ?>_button_caption" value="<?php echo get_option( 'wpjobster_' . $this->unique_slug . '_button_caption' ); ?>" /></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Your Midtrans Merchant ID', 'jobmid' ) ); ?></td>
                <td ><?php _e('Midtrans Merchant ID:','jobmid'); ?></td>
                <td><input type="text" size="45" name="wpjobster_midtrans_merchant_id" value="<?php echo apply_filters( 'wpj_sensitive_info_credentials', get_option('wpjobster_midtrans_merchant_id') ); ?>"/></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Your Midtrans Client key', 'jobmid' ) ); ?></td>
                <td ><?php _e('Midtrans Client key:','jobmid'); ?></td>
                <td><input type="text" size="45" name="wpjobster_midtrans_client_key" value="<?php echo apply_filters( 'wpj_sensitive_info_credentials', get_option('wpjobster_midtrans_client_key') ); ?>"/></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Your Midtrans Server key', 'jobmid' ) ); ?></td>
                <td ><?php _e('Midtrans Server key:','jobmid'); ?></td>
                <td><input type="text" size="45" name="wpjobster_midtrans_server_key" value="<?php echo apply_filters( 'wpj_sensitive_info_credentials', get_option('wpjobster_midtrans_server_key') ); ?>"/></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Finish Transaction Message', 'jobmid' ) ); ?></td>
                <td ><?php _e('Finish Transaction Message:','jobmid'); ?></td>
                <td><textarea style='width:100%;min-height:300px' name="wpjobster_midtrans_finish_message"><?php echo get_option('wpjobster_midtrans_finish_message'); ?></textarea></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Unfinish Transaction Message', 'jobmid' ) ); ?></td>
                <td ><?php _e('Unfinish Transaction Message:','jobmid'); ?></td>
                <td><textarea style='width:100%;min-height:300px' name="wpjobster_midtrans_unfinish_message"><?php echo get_option('wpjobster_midtrans_unfinish_message'); ?></textarea></td>
            </tr>

            <tr>
                <td valign=top width="22"><?php wpjobster_theme_bullet( __( 'Error Transaction Message', 'jobmid' ) ); ?></td>
                <td ><?php _e('Error Transaction Message:','jobmid'); ?></td>
                <td><textarea style='width:100%;min-height:300px' name="wpjobster_midtrans_error_message"><?php echo get_option('wpjobster_midtrans_error_message'); ?></texarea></td>
            </tr>

            <tr>
                <td>

                </td>
                <td></td>
                <td><input type="submit" name="wpjobster_save_<?php echo $this->unique_slug; ?>" value="<?php _e( 'Save Options', 'jobmid' ); ?>" /></td>
            </tr>
        </table>
    </form>
</div>
