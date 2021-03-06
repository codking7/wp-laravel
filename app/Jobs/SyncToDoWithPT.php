<?php

namespace CMV\Jobs;

use CMV\Jobs\Job;
use CMV\Models\PM\Message;
use CMV\Models\PM\ToDo;
use CMV\Services\MessagesService;
use CMV\Services\TodosService;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Pivotal;

class SyncToDoWithPT extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var ToDo
     */
    protected $todo;

    /**
     * Create a new job instance.
     */
    public function __construct(ToDo $todo)
    {
        $this->todo = $todo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $todo = $this->todo;
        $todo->load('files');

        $ref = $todo->reference;

        if (! $ref->pivotal_id) {
            $project = Pivotal::createProject($ref->slug);
            $ref->pivotal_id = $project->id;
            $ref->save();

            $todo->status = ToDo::STATUS_NEW;
            $todo->save();
        }

        if (!$todo->pivotal_story_id) {
            $content = $todo->content;
            if ($todo->files()->count()) {
                foreach ($todo->files as $file) {
                    $content .= "\n[{$file->name}]({$file->path})";
                }
            }
            $story = Pivotal::createStory($ref->pivotal_id, $todo->title, $content, $todo->type, [$todo->category]);
            $todo->pivotal_story_id = $story->id;
            $todo->save();
        } else {
            $story = Pivotal::getStory($ref->pivotal_id, $todo->pivotal_story_id);
            if ($story->current_state != $todo->status) {
                Pivotal::updateStory($ref->pivotal_id, $todo->pivotal_story_id, [
                    'current_state' => $todo->status
                ]);
            }
        }

        foreach ($todo->messages as $i => $message) {
            if (!$message->pivotal_comment_id && $i > 0) {
                $comment = Pivotal::createComment(
                    $ref->pivotal_id,
                    $todo->pivotal_story_id,
                    $message->content
                );

                $message->pivotal_comment_id = $comment->id;
            }
        }

        $messagesService = new MessagesService(TodosService::findActor($ref));
        $thread = $todo->thread;

        foreach (Pivotal::getComments($ref->pivotal_id, $todo->pivotal_story_id) as $comment) {
            if (!$todo->thread->messages()->where('pivotal_comment_id', $comment->id)->count()) {
                $message = $messagesService->postInThread($thread, $comment->text);
                $message->pivotal_comment_id = $comment->id;
                $message->save();
            }
        }
    }
}