<?php
namespace CMV\Http\Controllers\API;

use CMV\Jobs\SyncToDoWithPT;
use CMV\Models\PM\ToDo;
use CMV\Services\TodosService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Input, Validator, Auth, App;

/**
 * Class Todos
 * @package CMV\Http\Controllers\API
 */
class Todos extends Controller {

    use DispatchesJobs;

    /**
     * @var TodosService
     */
    protected $service;

    /**
     *
     */
    public function __construct()
    {
        $this->service = new TodosService(Auth::user());
    }

    /**
     * @Middleware("ref-access")
     * @Get("api/todos")
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = Input::all();

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($data, [
            'reference_type' => 'required|in:project,concierge_site',
            'reference_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->respondWithFailedValidator($validator);
        }

        $items = ToDo::where('reference_type', $data['reference_type'])
            ->where('reference_id', $data['reference_id'])
            ->orderBy('id', 'desc')
            ->with('createdBy', 'files')
            ->get();

        return $this->respondWithData($items->toArray());
    }

    /**
     * @Middleware("param-access")
     * @Get("api/todos/{todos}")
     * @Param $id
     */
    public function show($id)
    {
        $todo = ToDo::find($id);
        $todo->load('files');

        return $this->respondWithData($todo->toArray());
    }

    /**
     * @Middleware("ref-access")
     * @Post("api/todos")
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        $data = Input::all();

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($data, [
            'reference_type' => 'required|in:project,concierge_site',
            'reference_id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'category' => 'required|in:frontend,wordpress,other',
            'type' => 'required|in:bug,feature',
            'files' => 'array'
        ]);

        if ($validator->fails()) {
            return $this->respondWithFailedValidator($validator);
        }

        $reference = TodosService::getReference($data['reference_type'], $data['reference_id']);
        $todo = $this->service->createTodo($reference, $data);

        return $this->show($todo->id);
    }

    /**
     * @Middleware("param-access")
     * @Put("api/todos/{todos}/set-status")
     */
    public function updateStatus($id)
    {
        $data = Input::all();
        $validator = Validator::make($data, [
            'status' => 'required|in:started,accepted,rejected,delivered,finished'
        ]);

        if ($validator->fails()) {
            return $this->respondWithFailedValidator($validator);
        }

        $todo = ToDo::find($id);
        $this->service->setStatus($todo, $data['status']);
    }

    /**
     * @Middleware("param-access")
     * @Get("api/todos/{todos}/comments")
     * @param $id
     */
    public function comments($id)
    {
        $todo = ToDo::find($id);
        $comments = $todo->comments();

        return $this->respondWithData($comments);
    }

}