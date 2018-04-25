<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'user_id', 'category_id', 'excerpt', 'slug'];


    public function category () 
    {
    	return $this->belongsTo(Category::class);
    }

    public function user () 
    {
    	return $this->belongsTo(User::class);
    }


    public function scopeWithOrder ($query, $order) 
    {
    	//不同的排序，使用不同的排序规则
    	switch ($order) {
    		case 'recent':
    			$query = $this-> recent();
    			break;
    		
    		default:
    			$query = $this->recentReplied();
    			break;
    	}

    	//预加载防止 N+1 问题
    	return $query->with('user','category');
    }

    public function scopeRecentReplied($query) 
    {
    	//当话题有新回复时，
    	//此时自动触发框架对数据模型updated_at 的更新
    	return $query->orderBy('updated_at','desc');
    }

    public function scopeRecent ($query) 
    {
    	//按照创建时间排序
    	return $query->orderBy('created_at','desc');
    }

    public function link($params = [])
    {
        //return route('topics.show',array_merge([$this->id, $this->slug], $params));
        return route('topics.show',array_merge([$this->id], $params));
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

}



//关联，一个话题属于一个分类，一个话题有一个作者
//属于一对一的关系，使用belongsTo() 方法实现