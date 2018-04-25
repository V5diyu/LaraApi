<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Auth;
use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\User;
use App\Models\Link;

class TopicsController extends Controller
{

	//实例化时，帖子权限，限制未登录用户发帖。__construct()中已做中间件认证
	
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request, Topic $topic, User $user, Link $link)
	{

		//$topics = Topic::paginate();
		$topics =  $topic->withOrder($request->order)->paginate(20);
        $active_users = $user->getActiveUsers();
        $links = $link->getAllCached();
		return view('topics.index', compact('topics','active_users', 'links'));
	}


	//使用laravel的隐形路由模型绑定功能
    public function show(Topic $topic)
    {
    	//URL 矫正
    	/*if (! empty($topic->slug) && $topic->slug != $request->slug) {
    		return redirect($topic->link(), 301);
    	}*/
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
		$categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request, Topic $topic)
	{

		$topic->fill($request->all());
		$topic->user_id = Auth::id();
		$topic->save();
		//$topic = Topic::create($request->all());
		return redirect()->route('topics.show', $topic->id)->with('success', '成功创建话题');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->route('topics.show', $topic->id)->with('success', '更新成功');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '删除成功');
	}


	//图片上传和头像上传是同一个接口，代码重用
	public function uploadImage (Request $request, ImageUploadHandler $uploader) 
	{
		//初始化返回数据，默认是失败的
		$data = [
			'success' 	=> false,
			'msg'		=> '上传失败!',
			'file_path' => ''
		];
		//判断是否有上传文件，并赋值给$file
		//
		if ( $file = $request->upload_file ) {
			//保存图片到本地
			$result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
			//图片保存成功的话
			if ($result) {
				$data['file_path'] 	= $result['path'];
				$data['msg']		= '上传成功';
				$data['success']	= true;
			}
		}

		return $data;
	}
}