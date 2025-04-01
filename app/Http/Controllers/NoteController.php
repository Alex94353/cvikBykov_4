<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Note;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    
    public function index()
    {
        $notes = Note::with(['user', 'categories'])->orderBy('updated_at', 'desc')->get();

        return response()->json($notes);
    }



    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string|min:5|max:255',
                'body' => 'required|string',
                'categories' => 'array|max:3',
                'categories.*' => 'exists:categories, id'
            ]);

            $note = Note::create([
                'user_id' => $validated['user_id'],
                'title' => $validated ['title'],
                'body' => $validated ['body']
            ]);

            if (!empty($validated ['categories'])) {
                $note->categories()->sync($validated ['categories']);
            }

            $note->load(['user', 'categories']);

            return response()->json([
                'message' => 'Poznámka bola vytvorená',
                'note' => $note
            ], Response:: HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Chyba pri validácii poznámky',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neočakávaná chyba pri vytváraní poznámky',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show($id)
    {

        $note = Note::find($id);

        if (!$note) {
            return response()->json(['message' => 'Poznámka nebola nájdená'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($note);
    }



    public function update(Request $request, $id)
    {

        try{
            $validated = $request->validate([
                'title' => 'required|string|min:5|max:255',
                'body' => 'required|string',
                'categories' => 'array|max:3',
                'categories.*' => 'exists:categories, id'
            ]);

            $note = Note::find($id);

            if(!$note){
                return response()->json(['message'=>'Poznámka nebola nájdená'],Response::HTTP_NOT_FOUND);
            }

            $note->update($validated);

            if(isset($validated['categories'])){
                $note->categories()->sync($validated['categories']);
            }

            $note->load(['user','categories']);

            return response()->json([
                'message' => 'Poznámka bola aktualizovaná',
                'note' => $note
            ], Response:: HTTP_OK);
        }  catch (ValidationException $e) {
            return response()->json([
                'message' => 'Chyba pri validácii poznámky',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neočakávaná chyba pri aktualizacii poznámky',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        $note = Note::find($id);

        if (!$note) {
            return response()->json(['message' => 'Poznámka nebola nájdená'], Response::HTTP_NOT_FOUND);
        }

        $note->delete();

        return response()->json(['message' => 'Poznámka bola vymazaná']);
    }

    /**
     * Vlastné metódy
     */

    // Získanie poznámok s menami používateľov
    public function notesWithUsers()
    {
        $notes = DB::table('notes')
            ->join('users', 'notes.user_id', '=', 'users.id')
            ->select('notes.*', 'users.name as user_name')
            ->get();

        return response()->json($notes);
    }

    // Počet poznámok pre každého používateľa
    public function usersWithNoteCount()
    {
        $users = DB::table('users')
            ->select('users.id', 'users.name')
            ->selectSub(function ($query) {
                $query->from('notes')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('notes.user_id', 'users.id');
            }, 'note_count')
            ->get();

        return response()->json($users);
    }

    // Fulltextové vyhľadávanie v poznámkach
    public function searchNotes(Request $request) {
        $query = $request->query('q');

        if (empty($query)) {
            return response()->json(['message' => 'Musíte zadať dopyt na vyhľadávanie'], Response::HTTP_BAD_REQUEST);
        }

        $notes = Note::searchByTitleOrBody($query);

        if ($notes->isEmpty()) {
            return response()->json(['message' => 'Žiadne poznámky sa nenašli'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($notes);
    }


    //Počet poznámok podľa používateľa
    public function usersWithNotesCount()
    {
        $users = DB::table('notes')
            ->join('users', 'notes.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', DB::raw('COUNT(notes.id) as note_count'))
            ->groupBy('users.id', 'users.name')
            ->having('note_count', '>', 1)
            ->orderByDesc('note_count')
            ->get();

        return response()->json($users);
    }

    //Najdlhšia a najkratšia poznámka
    public function longestAndShortestNote()
    {
        $longest = DB::table('notes')
            ->select('id', 'title', 'body', DB::raw('LENGTH(body) as length'))
            ->orderByDesc('length')
            ->first();

        $shortest = DB::table('notes')
            ->select('id', 'title', 'body', DB::raw('LENGTH(body) as length'))
            ->orderBy('length')
            ->first();

        return response()->json([
            'longest' => $longest,
            'shortest' => $shortest
        ]);
    }

    //Počet poznámok za posledných 7 dní
    public function notesLastWeek()
    {
        $count = DB::table('notes')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return response()->json(['last_week_notes' => $count]);
    }

}
