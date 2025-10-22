<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 

class TaskController extends Controller
{
    /**
     * Displays the form to create a new task, pre-filling the email if provided.
     */
    public function create($email = null)
    {
        // Pass the received email parameter to the view
        return view('task_form', ['customerEmail' => $email]);
    }

    /**
     * Processes the request and creates the task in GoHighLevel.
     */
    public function store(Request $request)
    {
        // 1. Validation of form fields
        $validated = $request->validate([
            'customerEmail' => 'required|email|max:255',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            // Ensure the date is a valid ISO 8601 format required by the API 
            'dueDate' => 'required|date_format:Y-m-d\TH:i:sP',
        ]);

        $email = $validated['customerEmail'];
        $api = env('MY_APP_ONE');
        $defaultAssignedTo = 'f7MZKs2m62NyRphpUKqb'; // Default User ID

        // --- 2. Search Contact in GoHighLevel ---
        try {
            $response = Http::withToken($api)
                ->get("https://rest.gohighlevel.com/v1/contacts/lookup?email=$email");

            $data = $response->json();

            if ($response->status() !== 200 || empty($data['contacts'])) {
                 $errorMessage = $data['message'] ?? 'Contact not found or there was an API error.';
                 // Redirect with error, keeping the email in the URL context
                 return redirect()->route('tasks.create', ['email' => $email])->with('error', $errorMessage);
            }

            $contact = $data['contacts'][0];
            $contactId = $contact['id'];
            $assignedTo = $contact['assignedTo'] ?? $defaultAssignedTo;

        } catch (\Exception $e) {
            return redirect()->route('tasks.create', ['email' => $email])->with('error', 'Connection error while searching for contact: ' . $e->getMessage());
        }


        // --- 3. Create Task in GoHighLevel ---
        $postData = [
            "title" => $validated['title'],
            "dueDate" => $validated['dueDate'],
            "description" => $validated['description'] . "\n\n— Created by Partner via Portal",
            "assignedTo" => $assignedTo,
            "status" => "incompleted"
        ];
        
        try {
            $taskResponse = Http::withToken($api)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://rest.gohighlevel.com/v1/contacts/$contactId/tasks/", $postData);
            
            if ($taskResponse->successful()) {
                // Redirect and maintain the email for success
                return redirect()->route('tasks.create', ['email' => $email])->with('success', 'The task request was successfully submitted!');
            } else {
                 $errorMessage = $taskResponse->json()['message'] ?? 'Unknown error when creating the task in the API.';
                 return redirect()->back()->with('error', 'Error creating task: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Connection error while creating the task.');
        }
    }
}