<div class="col-sm-3">
    <div class="panel panel-default visible-md-block visible-lg-block">
        <div class="panel-body">
            <a href="#new-activity" data-toggle="modal" class="btn btn-primary btn-block">Log New Activity</a>

            <hr class="m-t m-b p-a-0" />

            <form action="{{ route('prospector.update-company', ['id' => $company->id]) }}" method="post">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="custom-select custom-select-sm form-control">{!! get_status_options($company->status) !!}</select>
                </div>
                <button type="submit" class="btn btn-success-outline">
                    Save Status
                </button>
            </form>
            

            <form action="{{ route('prospector.update-company', ['id' => $company->id]) }}" method="post">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="form-group m-t">
                    <label>Company Type</label>
                    <select name="type" class="custom-select custom-select-sm form-control">
                        @if($company->type == null)
                        <option value="">Not selected</option>
                        @endif
                        <option value="agency" @if($company->type == "agency") selected="selected" @endif>Agency</option>
                        <option value="brand" @if($company->type == "brand") selected="selected" @endif>Brand</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success-outline">
                    Save Type
                </button>
            </form>

            @if($company->salesRep)
            <p class="m-t-md">This contact is assigned to:<br /><strong>{{ $company->salesRep()->first()->name }}</strong></p>
            @endif
        </div>
    </div>
</div>

@include('modals/new-activity')