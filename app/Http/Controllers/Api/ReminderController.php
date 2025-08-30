<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;
use App\Http\Resources\ReminderResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response; // Import Response facade

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $reminders = Reminder::all();
        return response()->json(ReminderResource::collection($reminders));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointmentID' => 'required|integer',
            'reminderDateTime' => 'required|date',
            'reminderType' => 'required|string|max:255',
            'message' => 'required|string',
            'sent' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $reminder = Reminder::create($request->all());
        return response()->json(new ReminderResource($reminder), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reminder  $reminder
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Reminder $reminder)
    {
        return response()->json(new ReminderResource($reminder));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reminder  $reminder
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Reminder $reminder)
    {
        $validator = Validator::make($request->all(), [
            'appointmentID' => 'integer',
            'reminderDateTime' => 'date',
            'reminderType' => 'string|max:255',
            'message' => 'string',
            'sent' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $reminder->update($request->all());
        return response()->json(new ReminderResource($reminder), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reminder  $reminder
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Reminder $reminder)
    {
        $reminder->delete();
        return response()->json(null, 204);
    }
}
