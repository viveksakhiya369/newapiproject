<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'dist-assets/css/themes/lite-purple.min.css',
        'dist-assets/css/plugins/perfect-scrollbar.min.css',
        'css/select2.min.css',
        'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css',
    ];
    public $js = [
        // 'dist-assets/js/plugins/jquery-3.3.1.min.js',
        'dist-assets/js/plugins/bootstrap.bundle.min.js',
        'dist-assets/js/plugins/perfect-scrollbar.min.js',
        'dist-assets/js/scripts/script.min.js',
        'dist-assets/js/scripts/sidebar.large.script.min.js',
        'dist-assets/js/plugins/echarts.min.js',
        'dist-assets/js/scripts/echart.options.min.js',
        'dist-assets/js/scripts/dashboard.v1.script.min.js',
        'dist-assets/js/scripts/customizer.script.min.js',
        'dist-assets/js/select2.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
