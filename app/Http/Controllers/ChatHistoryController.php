<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ChatHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $histories = ChatHistory::latest()->paginate(15);
        return view('chat-history.index', compact('histories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('chat-history.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'thread_id' => 'required|string|max:255|unique:dbai.chat_history,thread_id',
            'messages' => 'required|json',
        ]);

        // Parse the JSON to validate it
        $messages = json_decode($validated['messages'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['messages' => 'Invalid JSON format'])->withInput();
        }

        $history = ChatHistory::create([
            'thread_id' => $validated['thread_id'],
            'messages' => $messages,
        ]);

        return redirect()
            ->route('chat-history.show', $history)
            ->with('status', 'Chat history created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ChatHistory $history)
    {
        return view('chat-history.show', compact('history'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatHistory $history)
    {
        return view('chat-history.edit', compact('history'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChatHistory $history)
    {
        $validated = $request->validate([
            'thread_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('dbai.chat_history', 'thread_id')->ignore($history->id)
            ],
            'messages' => 'required|json',
        ]);

        // Parse the JSON to validate it
        $messages = json_decode($validated['messages'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['messages' => 'Invalid JSON format'])->withInput();
        }

        $history->update([
            'thread_id' => $validated['thread_id'],
            'messages' => $messages,
        ]);

        return redirect()
            ->route('chat-history.show', $history)
            ->with('status', 'Chat history updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatHistory $history)
    {
        $history->delete();
        
        return redirect()
            ->route('chat-history.index')
            ->with('status', 'Chat history deleted successfully.');
    }
}
