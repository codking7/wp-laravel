<div class="row">
    <div class="col-sm-3">
        <ul class="nav nav-pills nav-stacked" role="tablist">
            <li role="presentation"
                v-for="view in brief.views" class="@{{ $index == 0 ? 'active' : null }}">
                <a data-toggle="tab"
                   role="tab"
                   href="#@{{ 'view-tab-' + $index }}"
                   aria-controls="@{{ 'view-tab-' + $index }}">@{{ view.name }}</a>
            </li>
        </ul>

        <div class="text-center m-t">
            <a href="#" class="btn btn-xs btn-success"
               v-on:click.prevent="addListItem('brief.views')">
                <i class="fa fa-plus"></i> Add View
            </a>
        </div>
    </div><!--col-->

    <div class="tab-content">
        <div role="tabpanel" class="col-sm-9 tab-pane @{{ viewIndex == 0 ? 'active' : null }}" id="@{{ 'view-tab-'+viewIndex }}"
             v-for="(viewIndex, view) in brief.views">

            <small class="pull-right">
                <a href="#" class="text-danger"
                   v-on:click.prevent="removeListItem('brief.views', viewIndex)"><i class="fa fa-trash"></i> Delete View</a>
            </small>

            <div class="form-group">
                <label>View Name</label>
                <input type="text" class="form-control" placeholder="the unique name of the view"
                       v-model="view.name" />
            </div>

            <div class="form-group">
                <label>Design File</label>

                <select class="custom-select form-control" v-model="view.design_file_id">
                    <option value="">No Design File Yet</option>
                    <option v-for="file in projectFiles" value="@{{ file.id }}">@{{ file.name }}</option>
                </select>
            </div>

            <div class="form-group">
                <label>Quick View Summary</label>
                    <textarea class="form-control" rows="2" cols="4" placeholder="A description of the page."
                              v-model="view.summary"></textarea>
            </div>

            <design-proofs v-if="view.design_proofs"
                           :path="'brief.views['+viewIndex+'].design_proofs'"
                           :design_proofs.sync="view.design_proofs">
            </design-proofs>

            <div class="clearfix"></div>

            <brief-checklist v-if="view.checklist"
                             :path="'brief.views['+viewIndex+'].checklist'"
                             :checklist.sync="view.checklist"
                             :with-categories="true">
            </brief-checklist>

        </div>

        <div v-if="brief.views.length == 0" role="tabpanel" class="col-sm-9 tab-pane active">

            <div class="well well-small text-center">
                <h4>No Views Added. <br /><small class="text-muted">Click the "Add View" button to the left to create the first view.</small></h4>
            </div>
        </div>
    </div>
</div>
