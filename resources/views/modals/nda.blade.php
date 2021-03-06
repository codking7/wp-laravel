<div class="modal" id="modal-nda">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h2 class="modal-title">Non-Disclosure Agreement</h2>
                <p class="m-b-0">Because we care about your project confidentiality.</p>
            </div>
            <div class="modal-body-scroller modal-body" id="full-nda" style="display: none">
                <a href="#" class="pull-left toggle-nda"><small><i class="fa fa-angle-left"></i> Back to cliff notes</small></a>
                <div class="clearfix"></div>
                @include('partials/nda')
            </div>
            <div class="modal-body" id="nda-cliff-notes">
                <p class="m-b-md">This Non-Disclosure Agreement is entered into by and between <strong>Code My Views Inc. ("CMV")</strong> with its principal offices at 600 Congress Ave. 14th Floor, Austin, TX 78701 and you, <strong>our future customer</strong>.</p>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">

                        <h3 class="text-primary text-center">NDA Cliff Notes:</h3>
                        <p class="text-center"><small>To read all of the terms, <a  href="#" class="toggle-nda">click here</a>.</small></p>
                        <ol class="how-list">
                            <li>All projects with CMV are confidential</li>
                            <li>Any material provided by Customer to CMV is considered proprietary to Customer</li>
                            <li>CMV services are 100% white-labeled services on behalf of Customer.  CMV will never display the work we provide for you in a portfolio</li>
                        </ol>
                    </div><!--col-->
                </div><!--row-->
            </div>

            <div class="modal-body text-center">
                
                <button class="btn btn-success"
                        v-on:click="agreeToNDA($event)"
                        v-submit="ndaing"><i class="fa fa-check"></i> Continue</button>

                <br />
                <br />
                <a href="#" data-dismiss="modal" class="btn btn-default-outline btn-xs">Skip <i class="fa fa-angle-right"></i></a>
            </div>
        </div>
    </div>
</div>