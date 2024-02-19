<?php

namespace App\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;

class Post extends Component
{
    // public function render()
    // {

    //     return view('livewire.post');
    // }
    public $title, $content, $postId, $slug, $status, $updatePost = false, $addPost = false;
    protected $rules = [
        'title' => 'required',
        'content' => 'required',
        'status' => 'required',
    ];
    public function resetFields()
    {
        $this->title = '';
        $this->content = '';
        $this->status = 1;
    }
    public function render()
    {
        $posts = \App\Models\Post::latest()->get();
        return view('livewire.post', compact('posts'));
    }
    public function create()
    {
        $this->resetFields();
        $this->addPost = true;
        $this->updatePost = false;
    }
    public function store()
    {
        $this->validate();
        try {
            \App\Models\Post::create([
                'title' => $this->title,
                'content' => $this->content,
                'status' => $this->status,
                'slug' => Str::slug($this->title),
            ]);

            session()->flash('success', 'Post Created Successfully!!');
            $this->resetFields();
            $this->addPost = false;
        } catch (\Exception $ex) {
            session()->flash('error', 'Something goes wrong!!');
        }
    }
    public function edit($id)
    {
        try {
            $post = \App\Models\Post::findOrFail($id);
            if (!$post) {
                session()->flash('error', 'Post not found');
            } else {
                $this->title = $post->title;
                $this->content = $post->content;
                $this->status = $post->status;
                $this->postId = $post->id;
                $this->updatePost = true;
                $this->addPost = false;
            }
        } catch (\Exception $ex) {
            session()->flash('error', 'Something goes wrong!!');
        }

    }

    public function update()
    {
        $this->validate();
        try {
            \App\Models\Post::whereId($this->postId)->update([
                'title' => $this->title,
                'content' => $this->content,
                'status' => $this->status,
                'slug' => Str::slug($this->title),
            ]);
            session()->flash('success', 'Post Updated Successfully!!');
            $this->resetFields();
            $this->updatePost = false;
        } catch (\Exception $ex) {
            session()->flash('error', 'Something goes wrong!!');
        }
    }

    public function cancel()
    {
        $this->addPost = false;
        $this->updatePost = false;
        $this->resetFields();
    }

    public function destroy($id)
    {
        try {
            \App\Models\Post::find($id)->delete();
            session()->flash('success', "Post Deleted Successfully!!");
        } catch (\Exception $e) {
            session()->flash('error', "Something goes wrong!!");
        }
    }

}
