@extends('spark::layouts.spark')



<!-- Main Content -->
@section('content')
<div class="row"  data-controller="project/invoice" state="{{ json_encode(['invoice' => $invoice->toArray(), 'project' => $project->toArray()]) }}" v-cloak>


    <div class="col-sm-12">
        <p class="m-b-sm"><a data-pjax href="{{ route('project.invoices', ['slug' => $project->slug]) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Back to all invoices</a></p>
    </div>

    <div class="col-md-8">

        <div class="panel panel-default invoice-panel">

            <div class="panel-body">
                
                <div class="row">
                    <div class="col-sm-12">

                        <div v-if="invoice.status == 'sent' && invoice.upfront_percent" class="alert alert-warning">
                            This project requires a @{{ invoice.upfront_percent }}% deposit before it will be moved into development.  Please <strong><a class="alert-link" v-on:click.prevent="openSpeedModal" href="#">select a delivery option</a></strong> to start your project.
                        </div>

                        <div v-if="invoice.status == 'paid'" class="alert alert-success">
                            This invoice was paid in full {{ $invoice->updated_at->diffForHumans() }}.
                        </div>

                        <div v-if="invoice.status == 'deposit_paid'" class="alert alert-info">
                            We have received your deposit and your project has entered development.
                        </div>

                        <br />
                        <p class="logo-bg">
                            <strong>Code My Views Inc.</strong><br />
                            2028 E Ben White Blvd Ste 240<br />
                            Box 9450<br />
                            Austin, TX 78741
                        </p>
                    </div>



                    <div class="col-sm-6">

                        <p class="m-t-lg">
                            {{ $project->team->name }}<br />
                            {{ $project->team->owner->name }}
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-striped table-condensed m-t-lg">
                            <tbody>
                                <tr>
                                    <td>Invoice #</td>
                                    <td>{{ $invoice->id }}</td>
                                </tr>
                                <tr>
                                    <td>Invoice Date</td>
                                    <td>{{ $invoice->date->format('F d, Y') }}</td>
                                </tr>
                            </tbody>
                        </table>

                        
                    </div>
                </div>

                <div class="row">

                    <div class="col-sm-12">
                        <table class="table table-bordered m-b-sm table-middle table-white">
                            <thead>
                                <tr>
                                    <th style="width: 55%">Description</th>
                                    <th style="width: 15%" class="text-center">Unit Cost</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right" style="width: 15%">Line Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="item in invoice.line_items">
                                    <td>@{{ item.description }}</td>
                                    <td class="text-center">
                                        $@{{ item.price }}
                                    </td>
                                    <td class="text-center">
                                        @{{ item.quantity }}
                                    </td>
                                    <td class="text-right">$@{{ item.price * item.quantity }}</td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td  style="border-right: none"></td>
                                    <td style="border-left: none" colspan="2" class="text-right">Subtotal:</td>
                                    <td colspan="2" class="text-right">$@{{ invoice.subTotal }}</td>
                                </tr>
                            </tfoot>
                        </table>


                        <h5>Payments</h5>
                        <table class="table table-bordered table-middle table-white table-condensed">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>Deposit</td>
                                    <td>$@{{ invoice.depositAmount }}.00</td>
                                    <td>
                                        <em v-if="!depPayment">not yet paid</em>
                                        <span v-if="depPayment && depPayment.stripe_transaction_id">
                                            @{{ depPayment.created_at | ago }} ago
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Final Payment</td>
                                    <td>$@{{ invoice.finalAmount }}.00</td>
                                    <td>
                                        <em v-if="!finalPayment">not yet paid</em>
                                        <span v-if="finalPayment && finalPayment.stripe_transaction_id">
                                            @{{ finalPayment.created_at | ago }} ago
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            

        </div><!--panel-->
    </div>

    <div class="col-sm-4">
        <div class="panel panel-default">

            <div class="panel-body">
                <table class="table table-bordered m-b-sm table-middle table-white">
                    <tr>
                        <td class="text-right">Subtotal:</td>
                        <td class="text-right">$@{{ invoice.subTotal }}</td>
                    </tr>

                    <tr>
                        <td class="text-right">Expedited Delivery Speed:</td>
                        <td class="text-right">$@{{ invoice.speedAmount }}</td>
                    </tr>

                    <tr v-if="invoice.discount_percent != 0">
                        <td class="text-right">Discount (@{{ invoice.discount_percent }}%):</td>
                        <td class="text-right"><em>-$@{{ invoice.discountAmount }}</em></td>
                    </tr>

                    <tr>
                        <td class="text-right">Grand Total: <small class="text-muted" v-if="invoice.speed">@{{ invoice.speeds[invoice.speed].title }} time
                                <br/>
                                <a v-if="invoice.status == 'sent'" href="#" v-on:click.prevent="openSpeedModal">change delivery speed</a></small></td>
                        <td class="text-right"><strong>$@{{ invoice.grandTotal }}</strong></td>
                    </tr>
                </table>

                <a href="#" v-if="invoice.speed === null" class="btn btn-lg btn-success btn-block" v-on:click.prevent="openSpeedModal">Select Delivery Date</a>
                
                <div v-if="invoice.status == 'sent' && invoice.speed !== null">
                    <p class="text-center m-b-sm">Paying the deposit will lock in your delivery date of <strong>@{{ invoice.speeds[invoice.speed].delivery_date  | dayOfWeek }}</strong>.</p>

                    <a href="#" class="btn btn-lg btn-success btn-block" v-on:click.prevent="openPaymentModal">Pay Deposit</a>
                    
                    
                </div>
                <a href="#" v-if="invoice.status == 'deposit_paid'" class="btn btn-lg btn-success btn-block" v-on:click.prevent="openPaymentModal">Pay Balance of $@{{ invoice.finalAmount }}</a>
            </div>
        </div>

        @if ($invoice->brief)
        <div class="panel panel-default m-b-md text-center">
            <div class="panel-body">
                <h5 class="m-t-0">This invoice is for the following development brief:</h5>

                <strong>{{ $invoice->brief->text['brief_type'] }} Brief</strong>
                <p class="text-muted m-a-0">{{ $project->name }}</p>
                <div class="media-body-actions">
                    <a class="btn btn-primary btn-xs"
                       href="/project/{{ $project->slug }}/briefs/{{ $invoice->brief->id }}" target="_blank">
                        <i class="fa fa-search"></i> View Brief
                    </a>
                </div>
    
            </div>
        </div>
        @endif

        @if (isAdmin())
        <a data-pjax href="/project/{{ $project->slug }}/invoices/{{ $invoice->id }}/edit" class="btn btn-warning btn-xs pull-right"><i class="fa fa-edit"></i> Edit Invoice</a>
        @endif
    </div>
    @include('modals/delivery-date-selector')
    @include('modals/payment-info')
</div>

@endsection