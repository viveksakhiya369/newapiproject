<?php

use common\models\User;
use yii\helpers\Url;
?>
<div class="side-content-wrap">
            <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar="" data-suppress-scroll-x="true">
                <ul class="navigation-left">
                    <li class="nav-item"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['site/get-index']) ?>"><i class="nav-icon i-Bar-Chart"></i><span class="nav-text">Dashboard</span></a>
                        <div class="triangle"></div>
                    </li>
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DISTRIBUTOR,User::DEALER])){ ?>
                    <li class="nav-item" data-item="users"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['user/index']) ?>"><i class="nav-icon i-Administrator"></i><span class="nav-text">Manage Users</span></a>
                        <div class="triangle"></div>
                    </li>
                    <!-- <?php //} if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){ ?>
                    <li class="nav-item"><a class="nav-item-hold" href="<?php //echo yii\helpers\Url::to(['distributor/index']) ?>"><i class="nav-icon i-Add-UserStar"></i><span class="nav-text">Manage Distributer</span></a>
                        <div class="triangle"></div>
                    </li> -->
                    <!-- <?php //} if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DISTRIBUTOR])) { ?>
                     <li class="nav-item"><a class="nav-item-hold" href="<?php // echo yii\helpers\Url::to(['dealer/index']) ?>"><i class="nav-icon i-Affiliate"></i><span class="nav-text">Manage Dealers</span></a>
                        <div class="triangle"></div>
                    </li> -->
                    <?php } if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DISTRIBUTOR,User::SALES_PERSON,User::DEALER])) { ?>
                    <li class="nav-item" data-item="widgets"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['order/index']) ?>"><i class="nav-icon i-Library"></i><span class="nav-text">Manage Order</span></a>
                        <div class="triangle"></div>
                    </li>
                    <?php } if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DISTRIBUTOR,User::DEALER])){  ?>
                    <li class="nav-item" data-item="stock"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['stock/index']) ?>"><i class="nav-icon i-Big-Data"></i><span class="nav-text">Manage Stock</span></a>
                        <div class="triangle"></div>
                    </li>
                    <?php } if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DISTRIBUTOR,User::DEALER])){  ?>
                    <li class="nav-item"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['points/index']) ?>"><i class="nav-icon i-Coins"></i><span class="nav-text">Manage Points</span></a>
                        <div class="triangle"></div>
                    </li>
                    <?php } if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){  ?>
                    <li class="nav-item" ><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['product/index']) ?>"><i class="nav-icon i-Suitcase"></i><span class="nav-text">Manage Products</span></a>
                        <div class="triangle"></div>
                    </li>
                    <!-- <?php //}if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){ ?>
                    <li class="nav-item"><a class="nav-item-hold" href="<?php //echo yii\helpers\Url::to(['salesman/index']) ?>"><i class="nav-icon i-Address-Book"></i><span class="nav-text">Manage Salesman</span></a>
                        <div class="triangle"></div>
                    </li> -->
                    <?php }if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){ ?>
                    <li class="nav-item"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['tax-master/index']) ?>"><i class="nav-icon i-Cube-Molecule"></i><span class="nav-text">Tax Master</span></a>
                        <div class="triangle"></div>
                    </li>
                    <?php } ?>
                    <!-- <?php // } if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DEALER])){ ?>
                    <li class="nav-item"><a class="nav-item-hold" href="<?php //echo yii\helpers\Url::to(['karigar/index']) ?>"><i class="nav-icon i-Farmer"></i><span class="nav-text">Manage Karigar</span></a>
                        <div class="triangle"></div>
                    </li>
                    <?php //} ?> -->
                    <!--<li class="nav-item" data-item="widgets"><a class="nav-item-hold" href="#"><i class="nav-icon i-Computer-Secure"></i><span class="nav-text">Widgets</span></a>
                        <div class="triangle"></div>
                    </li>
                    <li class="nav-item" data-item="charts"><a class="nav-item-hold" href="#"><i class="nav-icon i-File-Clipboard-File--Text"></i><span class="nav-text">Charts</span></a>
                        <div class="triangle"></div>
                    </li>
                    <li class="nav-item" data-item="forms"><a class="nav-item-hold" href="#"><i class="nav-icon i-File-Clipboard-File--Text"></i><span class="nav-text">Forms</span></a>
                        <div class="triangle"></div>
                    </li>
                    <li class="nav-item"><a class="nav-item-hold" href="datatables.html"><i class="nav-icon i-File-Horizontal-Text"></i><span class="nav-text">Datatables</span></a>
                        <div class="triangle"></div>
                    </li>
                    <li class="nav-item" data-item="sessions"><a class="nav-item-hold" href="#"><i class="nav-icon i-Administrator"></i><span class="nav-text">Sessions</span></a>
                        <div class="triangle"></div>
                    </li>
                    <li class="nav-item active" data-item="others"><a class="nav-item-hold" href="#"><i class="nav-icon i-Double-Tap"></i><span class="nav-text">Others</span></a>
                        <div class="triangle"></div>
                    </li>
                    <li class="nav-item"><a class="nav-item-hold" href="http://demos.ui-lib.com/gull-html-doc/" target="_blank"><i class="nav-icon i-Safe-Box1"></i><span class="nav-text">Doc</span></a>
                        <div class="triangle"></div>
                    </li> -->
                </ul>
            </div>
            <div class="sidebar-left-secondary rtl-ps-none" data-perfect-scrollbar="" data-suppress-scroll-x="true">
                <!-- Submenu Dashboards-->
                <!-- <ul class="childNav" data-parent="extrakits" style="display: block;">
                    <!-- <li class="nav-item"><a href=""><i class="nav-icon i-Crop-2"></i><span class="item-name">Role Permissions</span></a></li> -->
                    <!-- <li class="nav-item"><a href="loaders.html"><i class="nav-icon i-Loading-3"></i><span class="item-name">Loaders</span></a></li>
                    <li class="nav-item"><a href="ladda.button.html"><i class="nav-icon i-Loading-2"></i><span class="item-name">Ladda Buttons</span></a></li>
                    <li class="nav-item"><a href="toastr.html"><i class="nav-icon i-Bell"></i><span class="item-name">Toastr</span></a></li>
                    <li class="nav-item"><a href="sweet.alerts.html"><i class="nav-icon i-Approved-Window"></i><span class="item-name">Sweet Alerts</span></a></li>
                    <li class="nav-item"><a href="tour.html"><i class="nav-icon i-Plane"></i><span class="item-name">User Tour</span></a></li>
                    <li class="nav-item"><a href="upload.html"><i class="nav-icon i-Data-Upload"></i><span class="item-name">Upload</span></a></li> -->
                <!--</ul> -->
                <ul class="childNav" data-parent="users">
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){ ?>
                        <li class="nav-item"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['user/index']) ?>"><i class="nav-icon i-Administrator"></i><span class="item-name">Manage Users</span></a></li>
                    <?php } ?>
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){ ?>
                        <li class="nav-item"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['distributor/index']) ?>"><i class="nav-icon i-Add-UserStar"></i><span class="item-name">Manage Distributer</span></a></li>
                    <?php } ?>
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DISTRIBUTOR])) { ?>
                        <li class="nav-item"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['dealer/index']) ?>"><i class="nav-icon i-Affiliate"></i><span class="item-name">Manage Dealers</span></a></li>
                    <?php } ?>
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){ ?>
                        <li class="nav-item"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['salesman/index']) ?>"><i class="nav-icon i-Address-Book"></i><span class="item-name">Manage Salesman</span></a></li>
                    <?php } ?>
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DEALER])){ ?>
                        <li class="nav-item"><a class="nav-item-hold" href="<?php echo yii\helpers\Url::to(['karigar/index']) ?>"><i class="nav-icon i-Farmer"></i><span class="item-name">Manage Karigar</span></a></li>
                    <?php } ?>
                </ul>
                <ul class="childNav" data-parent="stock">
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DEALER,User::DISTRIBUTOR])) { ?>
                        <li class="nav-item"><a href="<?php echo yii\helpers\Url::to(['godown/index']) ?>"><i class="nav-icon i-Monitor-Analytics"></i><span class="item-name">Godown Stock</span></a></li>
                    <?php } ?>
                    <?php //if(in_array(Yii::$app->user->identity->role_id,[User::DEALER,User::DISTRIBUTOR])) { ?>
                        <!-- <li class="nav-item"><a href="<?php //echo yii\helpers\Url::to(['shop/index']) ?>"><i class="nav-icon i-Network-Window"></i><span class="item-name">Shop Stock</span></a></li> -->
                    <?php //} ?>
                </ul>
                <!-- <ul class="childNav" data-parent="point">
                    <?php //if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DEALER,User::DISTRIBUTOR])) { ?>
                        <li class="nav-item"><a href="<?php //echo yii\helpers\Url::to(['sendpoints/index']) ?>"><i class="nav-icon i-Sharethis"></i><span class="item-name">Send Points</span></a></li>
                    <?php //} ?>
                    <?php //if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::DEALER,User::DISTRIBUTOR])) { ?>
                        <li class="nav-item"><a href="<?php //echo yii\helpers\Url::to(['sendpoints/index']) ?>"><i class="nav-icon i-Sharethis"></i><span class="item-name">Send Points</span></a></li>
                    <?php //} ?>
                </ul>  -->
                <ul class="childNav" data-parent="widgets">
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::SALES_PERSON,User::DISTRIBUTOR])) { ?>
                        <li class="nav-item"><a href="<?php echo yii\helpers\Url::to(['order/index','receieved'=>true]) ?>"><i class="nav-icon i-Clock-3"></i><span class="item-name">Received Orders</span></a></li>
                    <?php } ?>
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SALES_PERSON,User::DISTRIBUTOR,User::DEALER])) { ?>
                    <li class="nav-item"><a href="<?php echo yii\helpers\Url::to(['order/index','sent'=>true]) ?>"><i class="nav-icon i-Clock-4"></i><span class="item-name">Sent Orders</span></a></li>
                    <?php } ?>
                    <?php if(in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN,User::SALES_PERSON,User::DISTRIBUTOR,User::DEALER])) { ?>
                        <li class="nav-item"><a href="<?php echo yii\helpers\Url::to(['pendingorder/index']) ?>"><i class="nav-icon i-Clock-4"></i><span class="item-name">Pending Orders</span></a></li>
                    <?php }if(in_array(Yii::$app->user->identity->role_id,[User::DEALER])){ ?>
                        <li class="nav-item"><a href="<?php echo yii\helpers\Url::to(['sellorder/index','sell_order'=>true]) ?>"><i class="nav-icon i-Clock-4"></i><span class="item-name">Selling Orders</span></a></li>
                    <?php } ?>
                </ul>
                
            </div>
            <div class="sidebar-overlay"></div>
        </div>