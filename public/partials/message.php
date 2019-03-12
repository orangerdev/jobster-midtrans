<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <?php wp_head(); ?>
        <link rel="stylesheet" href="<?php echo plugin_dir_url(dirname(__FILE__)).'css/jobmid-public.css' ; ?>" />
    </head>
    <body>
        <div class="jobmid-message-holder">
            <div class="jobmid-message-content">
                <?php echo wpautop($message); ?>
                <p>
                    Anda akan dialihkan ke halaman transaksi dalam waktu <span id='jobmid-countdown'>5</span> detik
                </p>
                <div class="jobmid-button">
                    <a href="<?php echo home_url('/my-account/shopping'); ?>">
                        Klik tombol ini jika anda tidak dialihkan otomatis
                    </a>
                </div>
            </div>
        </div>
        <script type="text/javascript" src='<?php echo plugin_dir_url(dirname(__FILE__)).'js/jobmid-public.js' ; ?>'></script>
        <?php wp_footer(); ?>
    </body>
</html>
