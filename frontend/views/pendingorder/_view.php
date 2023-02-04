<?php

use common\models\Orders;
use common\models\Products;
use yii\helpers\Url;

// echo '<pre>';print_r($order_details);exit;
// echo '<pre>';print_r();exit;
// echo'<pre>';print_r($result);exit();
?>
<div class="card"><!----><!---->
    <div class="card-body"><!----><!---->
        <div class="d-sm-flex mb-5"><span class="m-auto"></span><a href="<?= Url::to($_SERVER['HTTP_REFERER']) ?>"><span  class="btn btn-outline-danger mr-3 mb-3">Back</span></a>
        <!-- <button type="button" class="btn btn-outline-danger mr-3 mb-3">Edit Invoice</button> -->
        <button type="button" class="btn btn-primary mr-3 mb-3">print Invoice</button></div>
        <div id="print-area" class="print-area">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="font-weight-bold">Order Info</h4>
                    <p><?= $order_details->order_no ?></p>
                </div>
                <div class="text-sm-right col-md-6">
                    <p><strong>Order status: </strong><span class="badge badge-primary badge-primary r-badge m-1"><?php switch($order_details->status){
                        case Orders::STATUS_QUEUED:
                            echo Orders::STATUS_QUEUED_LABEL;
                            break;
                        case Orders::STATUS_INPROGRESS:
                            echo Orders::STATUS_INPROGRESS_LABEL;
                            break;
                        case Orders::STATUS_REJECTED:
                            echo Orders::STATUS_REJECTED_LABEL;
                            break;
                        case Orders::STATUS_APPROVED:
                            echo Orders::STATUS_APPROVED_LABEL;
                            break;
                        case Orders::STATUS_DELIVERED:
                            echo Orders::STATUS_DELIVERED_LABEL;
                            break;
                    } ?></span></p>
                    <p><strong> Order date: </strong><?= date('d-m-Y',strtotime($order_details->created_dt)) ?> </p>
                </div>
            </div>
            <div class="mt-3 mb-30 border-top"></div>
            <div class="row mb-5">
                <div class="mb-3 mb-sm-0 col-md-6">
                    <h5 class="font-weight-bold">Bill From</h5>
                    <h4 class="font-weight-bold"><span style="white-space: pre-line;"><?= isset($order_details->order->dealer->dealer_name) ? $order_details->order->dealer->dealer_name : Orders::getDistributorId($order_details->order->parent_id)?> </span></h4>
                    <p>Address:<span style="white-space: pre-line;"><?= isset($order_details->order->dealer->address) ? $order_details->order->dealer->dealer_name : Orders::getDistributorAddress($order_details->order->parent_id)?> </span></p>
                </div>
                <div class="text-sm-right col-md-6">
                    <h5 class="font-weight-bold">Bill To</h5>
                    <h4 class="font-weight-bold"><span style="white-space: pre-line;"><?= isset($order_details->order->dealer->distributor->dist_name) ? $order_details->order->dealer->distributor->dist_name : "Conwax"?> </span></h4>
                    <p>Address:<span style="white-space: pre-line;"><?= isset($order_details->order->dealer->distributor->dist_name) ? $order_details->order->dealer->distributor->address : "Sitaram Complex,<br> Kothariya Solvent Main Road,<br> Kothariya, Rajkot"?> </span></p>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive col-md-12">
                    <table role="table" aria-busy="false" aria-colcount="5" class="table b-table table-hover" id="__BVID__155"><!----><!---->
                        <thead role="rowgroup" class=""><!---->
                        <tr role="row" class="">
                                <th role="columnheader" scope="col" aria-colindex="1" class="">
                                    <div>Index</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="2" class="">
                                    <div>Item Name</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="3" class="">
                                    <div>Items/pack</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="3" class="">
                                    <div>Total Pack</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="3" class="">
                                    <div>Unit Price</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="4" class="">
                                    <div>Unit</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="5" class="">
                                    <div>Discount %</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="4" class="">
                                    <div>Quantity</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="5" class="">
                                    <div>Amount</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="5" class="">
                                    <div>Points</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody role="rowgroup"><!---->
                        <?php 
                        $count=0 ;
                        $total=0;
                        $qty_total=0;
                        ?>
                        <?php foreach($result as $key => $value) { ?>
                             <?php 
                             $count++;
                             $qty_total+=$value->qty;
                             $total+=$value->amount;
                              ?>
                            <tr role="row" class="">
                                <td aria-colindex="1" role="cell" class=""><?= $count ?></td>
                                <td aria-colindex="2" role="cell" class=""><?= $value->item_name ?></td>
                                <td aria-colindex="2" role="cell" class=""><?= $value->pack ?></td>
                                <td aria-colindex="2" role="cell" class=""><?= $value->total_pack ?></td>
                                <td aria-colindex="3" role="cell" class=""><?= $value->rate?></td>
                                <td aria-colindex="4" role="cell" class=""><?= $value->pack ?></td>
                                <td aria-colindex="5" role="cell" class=""><?= $value->discount ?></td>
                                <td aria-colindex="4" role="cell" class=""><?= $value->qty ?></td>
                                <td aria-colindex="5" role="cell" class=""><?= $value->amount ?></td>
                                <td aria-colindex="5" role="cell" class=""><?= (Products::findOne($value->item_id)->point)*($value->qty) ?></td>
                            </tr>
                            <?php } ?>
                            <tr role="row" class="">
                                <td role="cell" class="" colspan="6"></td>
                                <td role="cell" class="font-weight-bold">Total:</td>
                                <td role="cell" class="font-weight-bold"><?= $qty_total ?></td>
                                <td role="cell" class="font-weight-bold"><?= $total ?></td>
                                <td role="cell" class="font-weight-bold"><?= $order_details['total_points'] ?></td>
                            </tr>
                        </tbody><!---->
                    </table>
                </div>
                <div class="col-md-12">
                    <div class="invoice-summary float-right">
                        <!-- <p> Sub total: <span>44</span></p>
                        <p> Vat: <span> 5.28 </span></p> -->
                        <!-- <h5 class="font-weight-bold"> Grand Total: <span>  </span></h5> -->
                    </div>
                </div>
            </div>
        </div>
    </div><!----><!---->
</div>