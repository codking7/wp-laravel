<?php

namespace CMV\Models\PM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
* Each project in the system can have many project briefs.
* Project briefs each need to get approved by the client.
* We will potentially store revisions of these.
*/
class ProjectBrief extends Model
{ 
    use SoftDeletes;

    const TYPE_FRONTEND = 'frontend';
    const TYPE_WP = 'wordpress';
    const TYPE_OTHER = 'other';

    protected $columns = [
        'id',
        'text', //will most likely be some sort of json until I figure out actual data structure for the ProjectBriefs
        'project_id',
        'created_by_id',
        'approved_by_customer_id',
        'approved_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'projects' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo('CMV\Models\PM\Project');
   }

   public function createdByUser()
   {
        return $this->belongsTo('CMV\User','created_by_id');
   }

   public function approvedByUser()
   {
        return $this->belongsTo('CMV\User','approved_by_customer_id');
   }
}
