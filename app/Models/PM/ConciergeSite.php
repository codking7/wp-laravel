<?php

namespace CMV\Models\PM;

use Illuminate\Database\Eloquent\Model;

class ConciergeSite extends Model
{
    protected $columns = [
        'id',
        'files',
        'type', //wordpress || laravel  (default wordpress)
        'developer_id',
        'project_manager_id',
        'bitbucket_id', //id of the site in bitbucket for api sync purposes
        'git_url', //ssh url of the git repo for the site
        'team_id',
        'site_name',
        'url',
        'saved_credentials', //I am thinking this will be an encrypted text field where we can store shared credential information
        'status', //statuses TBD
        'subdomain', //subdomain for the staging site {subdomain}.concierge.approvemyviews.com
        'created_at',
        'updated_at',
        'deleted_at'
    ];



    public function team()
    {

        return $this->belongsTo('CMV\Team');

    }


    public function developer()
    {
        return $this->belongsTo('CMV\User','developer_id');
    }

    public function projectManager()
    {
        return $this->belongsTo('CMV\User','project_manager_id');
    }

    public function toDos()
    {
        return $this->hasMany('ToDo');
    }

    public function messages()
    {
        return $this->hasMany('Message');
    }


}