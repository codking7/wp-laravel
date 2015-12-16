<?php
namespace CMV\Misc;

use CMV\Models\PM\ProjectBrief;
use CMV\User, CMV\Models\PM\Project;

class ACL {

    const ACTION_CREATE = 'create';
    const ACTION_READ   = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    /**
     * @var \CMV\User
     */
    protected $user;

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @param $model
     * @param string $action
     * @return bool
     */
    public function check($model, $action = null)
    {
        $modelName = lcfirst(last(explode('\\', get_class($model))));

        return $this->$modelName($model, $action);
    }

    /**
     * @param Project $project
     * @param $action
     * @return bool
     */
    protected function project(Project $project, $action = 'read')
    {
        if (! $project->exists) {
            switch ($action) {
                case self::ACTION_READ:
                    return is_null($this->user);
                case self::ACTION_CREATE:
                    return true;
                default:
                    return false;
            }
        }

        if (!$this->user) return false;

        if ($this->user->is_mastermind || $this->user->is_admin) {
            return true;
        }

        switch ($action) {
            case static::ACTION_DELETE:
                return $project->team->owner_id == $this->user->id;
                break;
            default:
                return $project->team && $project->team->users()->find($this->user->id);
                break;
        }
    }

    /**
     * @param ProjectBrief $brief
     * @param $action
     * @return bool
     */
    protected function projectBrief(ProjectBrief $brief, $action)
    {
        if (!$this->user) return false;

        if (!$brief->exists) {
            switch ($action) {
                case self::ACTION_READ:
                    return true;
                case self::ACTION_CREATE:
                    return $this->user->isMastermind() || $this->user->isAdministrator();
                default:
                    return false;
            }
        }
        switch ($action) {
            case 'approve':
            case 'send-to-client':
            case self::ACTION_UPDATE:
            case self::ACTION_DELETE:
                return $this->user->isMastermind() || $this->user->isAdministrator();
            case 'request-changes':
            case self::ACTION_READ:
                return (bool) $brief->approved_by_admin_id;
            default:
                return false;
        }
    }
}