<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // Display a listing of groups
    public function index()
    {
        $groups = Group::all();
        return view('panel.groups.index', ['groups' => $groups]);
    }

    // Show the form for creating a new group
    public function create()
    {

        $users = User::where('activate_status', 'activated')->get();
        return view('panel.groups.create', ['users' => $users]);
    }

    // Store a new group in the database
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'userId' => 'array', // Expecting an array of user IDs
            'userId.*' => 'exists:users,id', // Ensure each selected user exists in the users table
        ]);
    
        // Create the group
        $group = Group::create([
            'name' => $request->input('title'),
            'description' => $request->input('description'),
        ]);
    
        // If users are selected, update their group_id
        if ($request->has('userId')) {
            // Iterate through the array of user IDs and save each in the GroupUser table
            foreach ($request->input('userId') as $userId) {
                GroupUser::create([
                    'group_id' => $group->id,
                    'user_id' => $userId,
                ]);
            }
        }
    
    
        // Redirect with success message
        return redirect()->route('groups.index')->with('success', 'Group created successfully, and users assigned to the group!');
    }

    // Show a single group
    public function show(Group $group, $id)
    {
        return view('groups.show', compact('group'));
    }

    // Show the form for editing an existing group
    public function edit($id)
    {
        $users = User::where('activate_status', 'activated')->get();
        $usersInGroup = GroupUser::where('group_id', $id)->pluck('user_id'); 
        $groupInfo = Group::where('id', $id)->first();
        return view('panel.groups.edit', ['users' => $users, 'groupInfo' => $groupInfo, 'usersInGroup' => $usersInGroup]);
    }

    // Update a group in the database
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'userId' => 'array' // Ensure userId is an array
        ]);
    
        // Update the group information
        $group = Group::findOrFail($id);

        // Update the group information
        $group->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
    
        // Get the current user IDs in the group
        $currentUserIds = GroupUser::where('group_id', $id)->pluck('user_id')->toArray(); // Convert to array for easier comparison
    
        // Get the new user IDs from the request
        $newUserIds = $request->userId ?? []; // Defaults to an empty array if not present
    
        // Determine which users to remove (those in the current list but not in the new list)
        $usersToRemove = array_diff($currentUserIds, $newUserIds);
    
        // Remove users from the group
        if (!empty($usersToRemove)) {
            GroupUser::where('group_id', $id)
                ->whereIn('user_id', $usersToRemove)
                ->delete();
        }
    
        // (Optional) If you need to add new users that are selected but not in the current group, you can add this part:
        $usersToAdd = array_diff($newUserIds, $currentUserIds);
        foreach ($usersToAdd as $userId) {
            GroupUser::create([
                'group_id' => $id,
                'user_id' => $userId,
            ]);
        }
    
        return redirect()->route('groups.index')->with('success', 'Group updated successfully!');
    }
    
    
    

    // Delete a group
    public function destroy(Group $group)
    {
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully!');
    }
}
