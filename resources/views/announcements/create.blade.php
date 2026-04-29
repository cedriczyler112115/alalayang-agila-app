@extends('layouts.app')

@section('title', 'Create Announcement - Caragados EC')

@section('content')
<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

<div style="margin-top: 2rem;">
    <div style="display: flex; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('announcements.index') }}" class="btn btn-outline" style="margin-right: 1rem; padding: 0.5rem; border-radius: 50%;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; letter-spacing: -0.025em;">Create <span style="color: var(--accent);">Announcement</span></h1>
            <p style="color: var(--text-muted); font-size: 1.05rem;">Fill in the details to publish a new announcement.</p>
        </div>
    </div>

    <div class="card" style="padding: 2rem;">
        @if($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.9rem;">
                <ul style="margin-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('announcements.store') }}" method="POST" id="announcementForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="title">Announcement Title <span style="color: var(--danger);">*</span></label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" placeholder="Enter a descriptive title" required maxlength="255">
            </div>

            <div class="form-group">
                <label class="form-label" for="editor">Content <span style="color: var(--danger);">*</span></label>
                <textarea id="editor" name="content">{{ old('content') }}</textarea>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Provide detailed information for the members.</p>
            </div>

            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="published_at">Publication Date (Optional)</label>
                    <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="{{ old('published_at') }}">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Leave blank to publish immediately if status is "Published".</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status <span style="color: var(--danger);">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Save as Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 1.5rem; padding: 1rem; background-color: rgba(59, 130, 246, 0.05); border-radius: var(--radius-md); border: 1px solid rgba(59, 130, 246, 0.2);">
                @if(auth()->user()->lib_regional_position_id)
                    <div style="margin-bottom: 0;">
                        <h4 style="font-size: 0.95rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.75rem;">Announcement Scope</h4>
                        <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0.75rem;">
                            <input type="checkbox" name="post_as_global" value="1" {{ old('post_as_global') ? 'checked' : '' }} class="scope-cb" style="width: 1.25rem; height: 1.25rem; accent-color: var(--accent);">
                            <span style="font-weight: 600; color: var(--text-main);">Post as Global Announcement</span>
                        </label>
                        <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0.75rem;">
                            <input type="checkbox" name="post_as_regional" value="1" {{ old('post_as_regional') ? 'checked' : '' }} class="scope-cb" style="width: 1.25rem; height: 1.25rem; accent-color: var(--accent);">
                            <span style="font-weight: 600; color: var(--text-main);">Post as Regional Announcement</span>
                        </label>
                        <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0;">
                            <input type="checkbox" name="post_on_my_club" value="1" {{ old('post_on_my_club') ? 'checked' : '' }} class="scope-cb" style="width: 1.25rem; height: 1.25rem; accent-color: var(--accent);">
                            <span style="font-weight: 600; color: var(--text-main);">Post on my club</span>
                        </label>
                    </div>
                @else
                    <input type="hidden" name="post_on_my_club" value="1">
                    <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0;">
                        <input type="checkbox" checked disabled style="width: 1.25rem; height: 1.25rem; accent-color: var(--accent);">
                        <span style="font-weight: 600; color: var(--text-main);">Post on my club (Default)</span>
                    </label>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; margin-left: 1.75rem;">If checked, this announcement will only be shown to members of your club in the dashboard.</p>
                @endif
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <button type="submit" class="btn btn-primary" id="submitBtn" style="flex: 1; padding: 0.8rem;">
                    <span class="btn-text">Save Announcement</span>
                    <span class="btn-loader" style="display: none;">Saving...</span>
                </button>
                <a href="{{ route('announcements.index') }}" class="btn btn-outline" style="flex: 1; padding: 0.8rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo'],
            placeholder: 'Start typing the announcement content here...'
        })
        .then(editor => {
            const form = document.querySelector('#announcementForm');
            const submitBtn = document.querySelector('#submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoader = submitBtn.querySelector('.btn-loader');

            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoader.style.display = 'inline';
            });
        })
        .catch(error => {
            console.error(error);
        });

    // Make scope checkboxes mutually exclusive mimicking radio buttons precisely natively
    const scopeCbs = document.querySelectorAll('.scope-cb');
    scopeCbs.forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                scopeCbs.forEach(other => {
                    if (other !== this) other.checked = false;
                });
            }
        });
    });
</script>

<style>
    /* CKEditor Custom Styling to match UI */
    .ck-editor__editable_inline {
        min-height: 300px;
        background-color: var(--card-bg) !important;
        color: var(--text-main) !important;
        border-color: var(--border-color) !important;
        border-bottom-left-radius: var(--radius-md) !important;
        border-bottom-right-radius: var(--radius-md) !important;
    }
    .ck-toolbar {
        background-color: rgba(0,0,0,0.02) !important;
        border-color: var(--border-color) !important;
        border-top-left-radius: var(--radius-md) !important;
        border-top-right-radius: var(--radius-md) !important;
    }
    .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
        border-color: var(--border-color) !important;
    }
    .ck.ck-editor__main>.ck-editor__editable.ck-focused {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
</style>
@endsection