<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task; //追加

class TasksController extends Controller
{
    // getでtasks/にアクセスされた場合の一覧表示処理
    public function index()
    {
        /**
        $tasks = Task::all();

        return view('tasks.index', [
            'tasks' => $tasks,
        ]);
        **/
        
        
        $data = [];
        if (\Auth::check()) {
            $user = \Auth::user();
            $tasks = $user->tasks()->orderBy('created_at','desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }
        
        return view('tasks.index' , $data);
        
        
    }

    // getでtasks/createにアクセスされた場合の「新規登録画面処理」
    public function create()
    {
        $tasks = new Task;
        
        return view('tasks.create' , [
            'task' => $tasks ,
            ]);
        
    }

    // podtでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:191',
            'status' => 'required|max:10'
        ]);
        
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
            
        ]);
        
        /**
        $task = new Task;
        $task->content = $request->content;
        $task->status = $request->status;
        $task->save();
        **/
        
        return redirect('/');
    }

    // getでtasks/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        $task = Task::find($id);
        
        if(\Auth::id() === $task->user_id) {
        return view ('tasks.show' , [
            'task' => $task,
        ]);   
        }
    
        return redirect('/');    
        
    }

    // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        $task = Task::find($id);
        if(\Auth::id() === $task->user_id) {
        return view('tasks.edit', [
            'task' => $task,
        ]);
        }
        
        return redirect('/');
    }

    // putまたはpatchでtasks/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'content' => 'required|max:191',
            'status' => 'required|max:10'
        ]);
        
        $task = Task::find($id);
        if(\Auth::id() === $task->user_id) {
            $task->content = $request->content;
            $task->status = $request->status;
            $task->save();
        }

        return redirect('/');
    }

    // deleteでtaskes/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        $task = Task::find($id);
        if(\Auth::id() === $task->user_id) {
            $task->delete();
        }
        
        return redirect('/');
    }
}
