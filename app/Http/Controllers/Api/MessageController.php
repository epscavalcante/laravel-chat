<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response as FacadesResponse;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user)
    {
        $userFromId = Auth::id();
        $userToId = $user;

        $messages = Message::where(
            function ($query) use ($userFromId, $userToId) {
                $query->where('from_user_id', $userFromId)
                    ->where('to_user_id', $userToId);
            }
        )->orWhere(
            function ($query) use ($userFromId, $userToId) {
                $query->where('from_user_id', $userToId)
                    ->where('to_user_id', $userFromId);
            }
        )->oldest('created_at')
        ->get();

        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message  = Message::create([
            'content' => $request->content,
            'to_user_id' => $request->to_user_id,
            'from_user_id' => Auth::id()
        ]);

        return response()->json($message, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
