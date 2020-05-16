<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Note;
use App\Notebook;

class Notes extends Controller
{
    public function getByID(Request $request)
    {
        $validateData = $request->validate([
            'note_id' => 'required'
        ]);

        $note = Note::findOrFail($request->note_id);
        $user = $this->getNoteOwner($note);

        // IF not the owner of this notebook
        if($user->id != $request->user_id)
            return response()->json(['status' => 'Error', 'detail' => 'Permission denied'], 403);

        return response()->json($note);
    }

    public function getByNotebook(Request $request)
    {
        $validateData = $request->validate([
            'notebook_id' => 'required'
        ]);

        $notebook = Notebook::findOrFail($request->notebook_id);

        if($notebook->user_id != $request->user_id)
            return response()->json(['status' => 'Error', 'detail' => 'Permission denied'], 403);

        return response()->json($notebook->notes);
    }

    public function create(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'notebook_id' => 'required'
        ]);

        $notebook = Notebook::findOrFail($request->notebook_id);

        // IF not the owner of this notebook
        if($notebook->user_id != $request->user_id)
            return response()->json(['status' => 'Error', 'detail' => 'Permission denied'], 403);

        $note = new Note();
        $note->fill($request->all());

        if($note->save())
            return response()->json(['status' => 'Ok', 'note' => $note]);
        else 
            return response()->json(['status' => 'Error']);
    }

    public function update(Request $request)
    {
        $validateData = $request->validate([
            'note_id' => 'required'
        ]);
        
        $note = Note::findOrFail($request->note_id);
        $user = $this->getNoteOwner($note);

        if($user->id != $request->user_id)
            return response()->json(['status' => 'Error', 'detail' => 'Permission denied'], 403);

        $note->fill($request->all());

        if($note->save())
            return response()->json(['status' => 'Ok', 'note' => $note]);
        else 
            return response()->json(['status' => 'Error']);
    }

    public function delete(Request $request)
    {
        $validateData = $request->validate([
            'note_id' => 'required'
        ]);

        $note = Note::findOrFail($request->note_id);

        $user = $this->getNoteOwner($note);

        if($user->id != $request->user_id)
            return response()->json(['status' => 'Error', 'detail' => 'Permission denied'], 403);

        if($note->delete())
            return response()->json(['status' => 'Ok']);
        else 
            return response()->json(['status' => 'Error']);
    }

    private function getNoteOwner($note)
    {
        $user = $note->notebook->user;

        return $user;
    }
}
