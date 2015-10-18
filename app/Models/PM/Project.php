<?php 

namespace CMV\Models\PM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Project extends Model {

	use SoftDeletes;
    
    protected $columns = [
        'id',
        'files', //json encoded array ['name' => $fileName, 'url' => $fileUrl, 'uploaded_by' => $user_id, 'date_uploaded' => $date_uploaded, 'deleted' => false]
        'project_type_id',
        'developer_id',
        'project_manager_id',
        'git_url', //the ssh url for the git repo on bitbucket
        'bitbucket_id', //bitbucket ID of the project for API purposes
        'team_id',
        'name', //unique name of project
        'slug', //unique
        'requested_deadline',
        'guaranteed_deadline',
        'status',
        'subdomain', //for the staging site   {subdomain}.approvemyviews.com - these staging sites will be autodeployed based off of git_url field
        'contractor_payout', //wont use for now - for future use
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'project_type_id',
        'git_url',
        'name',
        'deadline',
        'status',
        'subdomain'
    ];

    protected $dates = [
        'guaranteed_deadline',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function team()
    {

        return $this->belongsTo( 'CMV\Team', 'customer_id' );

    }

    public function members()
    {
        return $this->hasManyThrough('CMV\User','CMV\Team');
    }

    public function developer()
    {

        return $this->belongsTo( 'CMV\User', 'developer_id' );

    }

    public function projectManager()
    {
        return $this->belongsTo('CMV\User', 'project_manager_id');
    }

    public function type()
    {

        return $this->belongsTo( 'ProjectType' );

    }

    public function briefs()
    {
        return $this->hasMany('ProjectBrief');
    }

    public function invoices()
    {

        return $this->hasMany( 'Invoice' );

    }

    public function toDos()
    {
        return $this->hasMnay('ToDo');
    }

    public function messages()
    {
        return $this->hasMany('Message');
    }

    public function createOrFindProjectTypeId($projectTypeName)
    {

        $projectType = ProjectType::firstOrCreate(['name' => $projectTypeName]);

        $this->project_type_id = $projectType->id;
        $this->save();
        
    }
}