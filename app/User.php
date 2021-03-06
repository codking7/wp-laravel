<?php

namespace CMV;

use CMV\Models\PM\Project;
use CMV\Services\TeamsService;
use Laravel\Cashier\Billable;
use Laravel\Spark\Repositories\TeamRepository;
use Laravel\Spark\Teams\CanJoinTeams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Cashier\Contracts\Billable as BillableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laravel\Spark\Auth\TwoFactor\Authenticatable as TwoFactorAuthenticatable;
use Laravel\Spark\Contracts\Auth\TwoFactor\Authenticatable as TwoFactorAuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Spark\Teams\Invitation;

class User extends Model implements AuthorizableContract,
                                    BillableContract,
                                    CanResetPasswordContract,
                                    TwoFactorAuthenticatableContract
{
    use Authorizable, Billable, CanResetPassword, TwoFactorAuthenticatable, CanJoinTeams, SoftDeletes;


    protected $columns = [
        'id',
        'name',
        'email',
        'password',
        'phone_country_code',
        'phone_number',
        'two_factor_options',
        'current_team_id',
        'stripe_active',
        'stripe_id',
        'stripe_subscription',
        'stripe_plan',
        'last_four',
        'extra_billing_info',
        'trial_ends_at',
        'subscription_ends_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'developer_key', //save the developers public key here  - most will be null
        'is_mastermind', //this will be a super admin mode
        'is_admin', //admin mode (for project managers / internal team)
        'is_developer', // (for developers)
        'is_sales_rep', // (for sales reps)
        'pipeline_user_id', //this is where we will store the pipeline API user id - most users will be NULL
        'bitbucket_username' //where we can store the developers bitbucket username - most will be null
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'first_name', 'last_name'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'using_two_factor_auth',
        'gravatar'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'two_factor_options',
        'stripe_id', 'stripe_subscription', 'last_four', 'extra_billing_info'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'trial_ends_at', 'subscription_ends_at','created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Adds user to the project if he is a member of CMV staff (eg admin/dev)
     * @param Project $project
     */
    public function joinProjectIfStaff(Project $project)
    {
        $isProjectStaff = $this->isProjectStaff($project);
        $isCMVStaff = $this->isCMVStaff();
        $teamId = $project->team_id;

        if ($isCMVStaff) {
            $belongsToTeam = $this->teams()->find($project->team_id);

            if ($isProjectStaff) {
                // devs needn't be owners
                $role = $this->isAdministrator() ? 'owner' : 'member';

                if (!$belongsToTeam) {
                    $this->joinTeamById($teamId, $role);
                    $this->current_team_id = $teamId;
                    $this->save();
                }

                if ($role == 'member') {
                    $teamsService = new TeamsService($this);
                    $teamsService->attachUserToProject($this, $project);
                }
            } else if (!$isProjectStaff && $belongsToTeam) {
                // e.g. the former dev of the project tries to view it
                $this->teams()->detach([$teamId]);
            }
        }
    }

    public function devProjects()
    {
        if (!$this->isDeveloper()) return [];

        return Project::where('developer_id', $this->id)
            ->get();
    }

    /**
     * @return bool
     */
    public function isCMVStaff()
    {
        return $this->isAdministrator() || $this->isDeveloper();
    }

    /**
     * @param Project $project
     * @return bool
     */
    public function isProjectStaff(Project $project)
    {
        return $this->isAdministrator()
            || ($this->isDeveloper() && $project->developer_id == $this->id);
    }

    /**
     * @return bool
     */
    public function isAdministrator()
    {
        return $this->is_admin || $this->is_mastermind;
    }

    public function isDeveloper()
    {
        return $this->is_developer;
    }

    public function isCurrentTeamOwner()
    {
        return ($this->current_team && ($this->current_team->pivot->role == 'owner'));
    }

    public function isCurrentTeamAdmin()
    {
        return $this->current_team &&
            ($this->current_team->pivot->role == 'admin' || $this->current_team->pivot->role == 'owner');
    }

    public function getGravatarImage($size = 256)
    {
        return "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $this->email ) ) ) . "?s=" . $size . "&d=&d=mm";
    }

    public function getGravatarAttribute()
    {
        return getGravatarImage($this->email);
    }

    public function projects()
    {
        /** @var Team $team */
        $team = $this->currentTeam();

        if ($team->pivot->role == 'member') {
            return $this->belongsToMany('CMV\Models\PM\Project','user_projects')
                ->where("projects.team_id", $this->current_team_id);
        } else if (array_search($team->pivot->role, ['admin', 'owner']) !== false) {
            return $team->projects()->where('project_type', Project::TYPE_PROJECT);
        }
    }

    public function conciergeSites()
    {
        /** @var Team $team */
        $team = $this->currentTeam();

        if ($team->pivot->role == 'member') {
            return $this->belongsToMany('CMV\Models\PM\Project','user_projects')
                ->where("projects.team_id", $this->current_team_id)
                ->where('project_type', Project::TYPE_CONCIERGE);
        } else if (array_search($team->pivot->role, ['admin', 'owner']) !== false) {
            return $team->projects()->where('project_type', Project::TYPE_CONCIERGE);
        }
    }

    public static function developers()
    {
        return static::where('is_developer', true)->get();
    }

    public static function projectManagers()
    {
        return static::where('is_admin', true)
            ->orWhere('is_mastermind', true)
            ->get();
    }

    public function getFirstName()
    {
        $name = explode(' ',$this->name);

        return $name[0];
    }

    public function isMastermind()
    {
        return $this->is_mastermind;
    }


    public function getFullName()
    {


        if(isset($this->name))
        {

            return $this->name;

        }

        return false;
    }

    public function teams()
    {
        return $this->belongsToMany('CMV\Team','user_teams')
            ->withPivot('team_id', 'user_id', 'role');
    }

    public function developerProjects()
    {

        return $this->hasMany('CMV\Models\PM\Project','developer_id');

    }

    public function assignedProjects()
    {

        return $this->hasMany('CMV\Models\PM\Project','project_manager_id');

    }

    public function messages()
    {

        return $this->hasMany('CMV\Models\PM\Message');

    }

    public function companies()
    {

        return $this->hasMany('CMV\Models\Prospector\Company','sales_rep_id','id');

    }

    public function contacts()
    {

        return $this->hasManyThrough('CMV\Models\Prospector\Contact','CMV\Models\Prospector\Company');

    }

    public function leads()
    {
        return $this->hasMany('CMV\Lead');
    }

    public function activities()
    {
        return $this->hasMany('CMV\Models\Prospector\Activity','sales_rep_id');
    }

    public function projectInvoices()
    {
        return $this->hasMany('CMV\Models\PM\Invoice','customer_id');
    }

    public function conciergeInvoices()
    {
        return $this->hasMany('CMV\Models\PM\Invoice','customer_id');
    }

    public function getTrailing28DayActivities($returnDateArray = null)
    {

        $fromDate = \Carbon\Carbon::now()->subDay()->subWeeks(4); // or ->format(..)
        $tillDate = \Carbon\Carbon::now()->subDay();


        $currentDate = $fromDate;

        while($currentDate->toDateString() < $tillDate->toDateString())
        {
            $dates[] = $currentDate->addDay()->toDateString();
        }

        if($returnDateArray == true)
        {   
            return json_encode($dates);
        }

        $data = $this->activities()
                ->selectRaw('date(created_at) as date, COUNT(*) as count')
                ->whereBetween( \DB::raw('date(created_at)'), [\Carbon\Carbon::now()->subDay()->subWeeks(4)->toDateString(), \Carbon\Carbon::now()->subDay()->toDateString()] )
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->lists('count', 'date');

       
        $output = [];
        foreach($dates as $date)
        {

            $foundValue = false;
            foreach($data as $name => $value)
            {
                if($date == $name)
                {
                    $output[] = $value;
                    $foundValue = true;
                }
            }

            if($foundValue === false)
            {
                $output[] = 0;
            }

        }       
        return json_encode($output);
    }

    public function scopeRandom($query)
    {
        return $query->orderBy(\DB::raw('RAND()'));
    }

    public function getStatusCountJson($type)
    {

        $data = $this->companies()->where('type',$type)->select('status', \DB::raw('count(*) as total'))->groupBy('status')->lists('total','status')->toArray();
            

        $output = [];
        foreach(\CMV\Models\Prospector\Company::$statuses as $status => $description)
        {

            $foundValue = false;
            foreach($data as $name => $value)
            {
                if($status == $name)
                {
                    $output[] = $value;
                    $foundValue = true;
                }
            }

            if($foundValue === false)
            {
                $output[] = 0;
            }

        }


        return json_encode($output);
    }
}
