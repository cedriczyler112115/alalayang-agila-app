<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer'
        ]);

        $parentComment = null;
        if (!empty($validated['parent_id'])) {
            $parentComment = $announcement->comments()
                ->whereKey($validated['parent_id'])
                ->first();

            abort_if(!$parentComment, 422, 'The selected reply target is invalid.');
        }

        $comment = $announcement->comments()->create([
            'user_id' => auth()->id(),
            'parent_id' => $parentComment?->id,
            'content' => trim($validated['content']),
        ]);

        return redirect()
            ->route('announcements.show', $announcement)
            ->with('status', $parentComment ? 'Reply posted successfully!' : 'Comment posted successfully!')
            ->withFragment('comment-' . $comment->id);
    }
}
