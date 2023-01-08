<?php

use common\models\Orders;

?>
<div class="card"><!----><!---->
    <div class="card-body"><!----><!---->
        <div class="d-sm-flex mb-5"><span class="m-auto"></span><button type="button" class="btn btn-outline-danger mr-3 mb-3">Back To Invoices</button><button type="button" class="btn btn-outline-danger mr-3 mb-3">Edit Invoice</button><button type="button" class="btn btn-primary mr-3 mb-3">print Invoice</button></div>
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
                    <p></p><span style="white-space: pre-line;"> </span>
                </div>
                <div class="text-sm-right col-md-6">
                    <h5 class="font-weight-bold">Bill To</h5>
                    <p></p><span style="white-space: pre-line;"> </span>
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
                                    <div>Unit Price</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="4" class="">
                                    <div>Unit</div>
                                </th>
                                <th role="columnheader" scope="col" aria-colindex="5" class="">
                                    <div>Cost</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody role="rowgroup"><!---->
                            <tr role="row" class="">
                                <td aria-colindex="1" role="cell" class=""></td>
                                <td aria-colindex="2" role="cell" class=""></td>
                                <td aria-colindex="3" role="cell" class=""></td>
                                <td aria-colindex="4" role="cell" class=""></td>
                                <td aria-colindex="5" role="cell" class=""></td>
                            </tr>
                            <tr role="row" class="">
                                <td aria-colindex="1" role="cell" class=""></td>
                                <td aria-colindex="2" role="cell" class=""></td>
                                <td aria-colindex="3" role="cell" class=""></td>
                                <td aria-colindex="4" role="cell" class=""></td>
                                <td aria-colindex="5" role="cell" class=""></td>
                            </tr><!----><!---->
                        </tbody><!---->
                    </table>
                </div>
                <div class="col-md-12">
                    <div class="invoice-summary float-right">
                        <p> Sub total: <span>44</span></p>
                        <p> Vat: <span> 5.28 </span></p>
                        <h5 class="font-weight-bold"> Grand Total: <span> 49.28 </span></h5>
                    </div>
                </div>
            </div>
        </div>
    </div><!----><!---->
</div>