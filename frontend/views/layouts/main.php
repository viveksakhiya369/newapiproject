<?php

/** @var \yii\web\View $this */
/** @var string $content */
use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\web\View;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />
    <?php $this->head() ?>
</head>
<body class="text-left">
    <?php $this->beginBody() ?>
<div class="app-admin-wrap layout-sidebar-large">
    <?= $this->render('main_header') ?>
    <?= $this->render('side_bar') ?>

    <div  class="main-content-wrap sidenav-open d-flex flex-column">
        <?= $content ?>
        <?php 
            $flashes=Yii::$app->session->getAllFlashes();
            foreach($flashes as $type => $message){
                $this->registerJs('
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                  };
                toastr["'.$type.'"]("'.$message.'");
                ', View::POS_END);
            }
            Yii::$app->session->removeAllFlashes();
        ?>
        <div class="flex-grow-1"></div>
        <?= $this->render('footer') ?>
    </div>
</div>
<?= $this->render('search') ?>
<?php $this->endBody() ?>
</body>
<?php
$this->registerJs('
$(".select2").select2({
    width: "100%"
});
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "slideDown",
    "hideMethod": "slideUp"
  };
$(document).on("select2:open", () => {
    document.querySelector(".select2-search__field").focus();
  });
//   // Disable right-click
//   document.addEventListener("contextmenu", (e) => e.preventDefault());
  
//   function ctrlShiftKey(e, keyCode) {
//     return e.ctrlKey && e.shiftKey && e.keyCode === keyCode.charCodeAt(0);
//   }
  
//   document.onkeydown = (e) => {
//     // Disable F12, Ctrl + Shift + I, Ctrl + Shift + J, Ctrl + U
//     if (
//       event.keyCode === 123 ||
//       ctrlShiftKey(e, "I") ||
//       ctrlShiftKey(e, "J") ||
//       ctrlShiftKey(e, "C") ||
//       (e.ctrlKey && e.keyCode === "U".charCodeAt(0))
//     )
//       return false;
//   };
  
  ', View::POS_END);
?>
</html>
<?php $this->endPage(); ?>
