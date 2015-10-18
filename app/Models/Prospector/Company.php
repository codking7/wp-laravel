<?php

namespace CMV\Models\Prospector;

use Illuminate\Database\Eloquent\Model;


/**
* A Company object is a company that we are trying
* find contact info for, and then once we have that,
* we will start reaching out to them (via one of our sales reps).
* Each company has a specific status which you can see below.
*
* Each company is owned by one sales rep.
*
* Each company in the system is either a brand or an agency.
* 
*/
class Company extends Model
{
        
    protected $columns = [
        'id',
        'name',
        'type', // agency | brand
        'status',
        'sales_rep_id',
        'created_at',
        'updated_at'
    ];

    public static $types = ['agency', 'brand'];

    protected $fillable = ['name'];

    public static $statuses = [
        'Not Yet Contacted' => 'No prospect at this company has been contacted.',
        'Outreach' => 'This company has at least one prospect active in an outreach funnel.',
        'Positive Response' => 'This company has given us a positive reply to an outreach email, but we have not qualified them yet.',
        'Qualified Prospect' => 'You have qualified the company as being someone who could spend atleast $3,000 in the next year',
        'Pipeline' => 'The company will submit the first order over the next quarter',
        'Won' => 'This company has submitted and paid for their first order.',
        'Unqualified' => 'We spoke to this company, but have Unqualified them.',
        'Lost' => 'This company told us they are not interested in working with us.'

    ];


    public static function getJsonStatuses()
    {

        $output = [];
        foreach(self::$statuses as $status => $description)
        {
            $output[] = $status;
        }

        return json_encode($output);
    }

    public static function unassignedCompanies()
    {

        return Company::with('contacts')->whereNull('sales_rep_id')->paginate(15);

    }

    public function salesRep()
    {

        return $this->belongsTo('CMV\User','sales_rep_id');

    }

    public function contacts()
    {

        return $this->hasMany('Contact');

    }

    public function meta()
    {

        return $this->hasMany('CompanyMeta');

    }

    public function assignToSalesRepyByPipelineUserId($pipeline_user_id)
    {   

        $this->sales_rep_id = User::where('pipeline_user_id', $pipeline_user_id)->first()->id;
        $this->save();

    }

}
