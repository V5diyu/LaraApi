<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['name','description'];


    public function create () 
    {
    	$categories = Category::all();
    	return view('topics.create_and_edit',compact('topic', 'categories'));
    }

}
