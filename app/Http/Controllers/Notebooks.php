<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notebook;

class Notebooks extends Controller
{
    public function showAll(Request $request)
    {
        $user_id = $request->user_id;
        //$notebooks = Notebook::where('user_id', $user_id)->get();
        $notebooks = Notebook::withCount('notes')->where('user_id', $user_id)->get();

        return response()->json($notebooks);
    }

    public function getOne(Request $request)
    {
        $validateData = $request->validate([
            'user_id' => 'required',
            'notebook_id' => 'required'
        ]);

        $id = $request->notebook_id;
        $notebook = Notebook::findOrFail($id);

        if($notebook->user_id != $request->user_id)
        {
            return response()->json([
                'status' => 'Error', 
                'detail' => 'Permission denied'
            ], 403);
        }

        return response()->json($notebook);
    }

    public function create(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required',
            'user_id' => 'required'
        ]);

        $notebook = new Notebook();
        $notebook->fill($request->all());

        if($notebook->save())
            return response()->json(['status'=> 'Ok', 'notebook' => $notebook]);
        else
            return response()->json(['status' => 'Error']);
    }

    public function update(Request $request)
    {
        $validateData = $request->validate([
            'user_id' => 'required',
            'notebook_id' => 'required'
        ]);

        $id = $request->notebook_id;
        $notebook = Notebook::findOrFail($id);

        if($notebook->user_id != $request->user_id)
        {
            return response()->json([
                'status' => 'Error', 
                'detail' => 'Permission denied'
            ], 403);
        }

        if($request->has('title'))
            $notebook->title = $request->title;
        if($request->has('description'))
            $notebook->description = $request->description;

        if($notebook->save())
            return response()->json(['status'=> 'Ok', 'notebook' => $notebook]);
        else
            return response()->json(['status' => 'Error']);
    }

    public function delete(Request $request)
    {
        $validateData = $request->validate([
            'user_id' => 'required',
            'notebook_id' => 'required'
        ]);

        $id = $request->notebook_id;

        $notebook = Notebook::findOrFail($id);

        if($notebook->user_id != $request->user_id)
        {
            return response()->json([
                'status' => 'Error', 
                'detail' => 'Permission denied'
            ], 403);
        }

        if($notebook->delete())
            return response()->json(['status'=> 'Ok', 'notebook' => $notebook]);
        else
            return response()->json(['status' => 'Error']);
    }
}
