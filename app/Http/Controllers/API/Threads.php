<?php
namespace CMV\Http\Controllers\API;

use CMV\Models\PM\Thread;
use Input, Validator, Auth;

class Threads extends Controller {

    /**
     * @Get("api/threads")
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
            return $this->respondWithFaildValidator($validator);
        }

        $threads = Thread::with('messages', 'messages.user')
            ->where('reference_type', $data['reference_type'])
            ->where('reference_id', $data['reference_id'])
            ->get();

        return $this->respondWithData($threads->toArray());
    }

    /**
     * @Post("api/threads")
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        $data = Input::all();

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($data, [
            'reference_type' => 'required_unless:thread_id|in:project,concierge_site',
            'reference_id' => 'required_unless:thread_id',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->respondWithFaildValidator($validator);
        }

        $data = Input::all();

        $thread = new Thread();
        $thread->reference_type = $data['reference_type'];
        $thread->referenct_id = $data['reference_id'];
        $thread->save();

        $thread->addMessage(Auth::user(), $data['content']);
    }

}