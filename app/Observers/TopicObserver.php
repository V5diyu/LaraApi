<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic) 
    {
        //XSS 过滤
        $topic->body = clean($topic->body,'user_topic_body');

        //生成话题摘录
    	$topic->excerpt = make_excerpt($topic->body);

        /*if (! $topic->slug) {
            $class_handler = new SlugTranslateHandler();
            $translate = $class_handler->translate($topic->title);
            $topic->slug = $translate;
            


            //$topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
            //app() 允许我们使用Laravel服务容器，用来生成实例
            //
        }*/
    	
    }

    public function deleted(Topic $topic)
    {
        \DB::table('replies')->where('topic_id', $topic->id)->delete();
    }
}