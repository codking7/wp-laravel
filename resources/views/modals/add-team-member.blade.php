<div class="modal fade" id="add-team-member">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title">Add a Team Member</h5>
      </div>
      <div class="modal-body">

            <form method="post" action="">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="name">Email Address</label>
                            <input 
                                type="text"
                                class="form-control input-lg"
                                name="address"
                                required autofocus 
                            />

                        </div>
                        <div class="hr-divider">
                          <h3 class="hr-divider-content hr-divider-heading">
                            User Access
                          </h3>
                        </div>

                        <div class="form-group">

                            <div class="radio custom-control custom-radio">
                              <label class="well">
                                <input type="radio" id="radio1" name="radio">
                                <span class="custom-control-indicator" style="top: 40px; left: 2px;"></span>
                                Add [email_address_here] to all of my projects, including this one.
                              </label>
                            </div>
                            <div class="radio custom-control custom-radio">
                              <label class="well">
                                <input type="radio" id="radio2" name="radio">
                                <span class="custom-control-indicator" style="top: 40px; left: 2px;"></span>
                                Add [email_address_here] to just the [project_name] project. <small>you can add them to other projects in the future.</small>
                              </label>
                            </div>
                        </div>
                        <button class="btn btn-success btn-lg btn-block" type="submit">Send Invitation</button>                       
                    </div>
                </div>
            </form>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->