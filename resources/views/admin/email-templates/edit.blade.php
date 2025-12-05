@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Email Template
                    </h4>
                    <a href="{{ url('admin/email-templates') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Templates
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('email-templates.update', $emailTemplate) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <strong>Template Name</strong>
                                </label>
                                <input type="text" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', $emailTemplate->name) }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label">
                                    <strong>Email Subject</strong>
                                </label>
                                <input type="text" 
                                    class="form-control @error('subject') is-invalid @enderror" 
                                    id="subject" 
                                    name="subject" 
                                    value="{{ old('subject', $emailTemplate->subject) }}"
                                    required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">
                                <strong>Email Content</strong>
                            </label>
                            <textarea 
                                class="form-control @error('content') is-invalid @enderror" 
                                id="content" 
                                name="content" 
                                rows="20"
                                style="font-family: 'Courier New', monospace; font-size: 14px;"
                                required>{{ old('content', $emailTemplate->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Available Variables</h6>
                            <p class="mb-0">
                                You can use these variables in your template:<br>
                                <code>@{{ name @}}</code>, 
                                <code>@{{ eventName @}}</code>, 
                                <code>@{{ eventStart @}}</code>, 
                                <code>@{{ eventEnd @}}</code>, 
                                <code>@{{ eventAddress @}}</code>, 
                                <code>@{{ appName @}}</code>
                            </p>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    id="is_active" 
                                    name="is_active" 
                                    value="1"
                                    {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active Template</strong>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Template
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#previewModal">
                                <i class="bi bi-eye"></i> Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Current Blade Template</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>&lt;x-mail::message&gt;
&#123;!! $dynamicContent !!&#125;
&lt;/x-mail::message&gt;</code></pre>
                    <p class="text-muted mb-0">
                        <small><i class="bi bi-info-circle"></i> This is the blade template that renders your email content.</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Email Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> This is a preview with sample data
                </div>
                <div id="preview-content" class="border p-3 rounded bg-white">
                    {!! str_replace(
                        ['{{ name }}', '{{ eventName }}', '{{ eventStart }}', '{{ eventAddress }}', '{{ appName }}'],
                        ['<strong>John Doe</strong>', '<strong>Annual Conference 2024</strong>', '<strong>December 15, 2024</strong>', '<strong>Grand Hotel, Manila</strong>', '<strong>' . config('app.name') . '</strong>'],
                        $emailTemplate->content
                    ) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-update preview when content changes
    document.getElementById('content').addEventListener('input', function() {
        let content = this.value;
        content = content.replace(/\{\{ name \}\}/g, '<strong>John Doe</strong>');
        content = content.replace(/\{\{ eventName \}\}/g, '<strong>Annual Conference 2024</strong>');
        content = content.replace(/\{\{ eventStart \}\}/g, '<strong>December 15, 2024</strong>');
        content = content.replace(/\{\{ eventAddress \}\}/g, '<strong>Grand Hotel, Manila</strong>');
        content = content.replace(/\{\{ appName \}\}/g, '<strong>' + '{{ config("app.name") }}' + '</strong>');
        content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        content = content.replace(/\n/g, '<br>');
        
        document.getElementById('preview-content').innerHTML = content;
    });
</script>
@endpush
@endsection