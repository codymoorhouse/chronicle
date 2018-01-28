<?php

namespace CodyMoorhouse\Secretary\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

/* Models */
use CodyMoorhouse\Secretary\Models\Note;

/* Requests */
use CodyMoorhouse\Secretary\Requests\Notes\DestroyRequest;
use CodyMoorhouse\Secretary\Requests\Notes\StoreRequest;
use CodyMoorhouse\Secretary\Requests\Notes\UpdateRequest;

class NotesController extends Controller
{
    /**
     * Destroy a note in the system.
     *
     * @param CodyMoorhouse\Secretary\Models\Note $note
     * @param CodyMoorhouse\Secretary\Requests\Media\DestroyRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note, DestroyRequest $request)
    {
        try {
            return DB::transaction(function() use ($note, $request) {

                /* Delete note comments */
                foreach ($note->comments as $comment) {
                    $comment->delete();
                }

                /* Delete note media */
                foreach ($note->media as $media) {
                    $media->delete();
                }

                $note->delete();

                return Response::json([
                    'message'   =>  'Note deleted successfully',
                ]);
            }, config('secretary.db_attempts'));
        } catch (Exception $e) {
            return Response::json([
                'notes'  =>  [$e]
            ]);
        }
    }

    /**
     * Store a new note into a section.
     *
     * @param CodyMoorhouse\Secretary\Requests\Notes\StoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            return DB::transaction(function() use ($request) {
                Note::create([
                    'description'       =>  $request->description,
                    'section_id'        =>  $request->section_id,
                    'section_ref_slug'  =>  $request->section_ref_slug,
                    'user_id'           =>  Auth::id()
                ]);

                return Response::json([
                    'message'   =>  'Note created successfully',
                ]);
            }, config('secretary.db_attempts'));
        } catch (Exception $e) {
            return Response::json([
                'notes'  =>  [$e]
            ]);
        }
    }

    /**
     * Update a note in the system.
     *
     * @param CodyMoorhouse\Secretary\Models\Note $note
     * @param CodyMoorhouse\Secretary\Requests\Notes\UpdateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Note $note, UpdateRequest $request)
    {
        try {
            return DB::transaction(function() use ($note, $request) {
                $note->update([
                    'description'   =>  $request->description
                ]);

                return Response::json([
                    'message'   =>  'Note updated successfully',
                ]);
            }, config('secretary.db_attempts'));
        } catch (Exception $e) {
            return Response::json([
                'notes'  =>  [$e]
            ]);
        }
    }
}
