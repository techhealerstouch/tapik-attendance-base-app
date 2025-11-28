@extends('layouts.sidebar')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-header">
                        <h4 class="card-title">Create Food Service</h4>
                        <p class="text-muted mb-0">Define a new food service option for your events (e.g., Breakfast, Lunch, Dinner, Snacks)</p>
                    </div>
                    <div class="card-body">
                        <!-- Info Alert -->
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <strong>Tip:</strong> Food services created here can be assigned to events and will be available for claiming by attendees during the event.
                            </div>
                        </div>

                        <form action="{{ route('food-services.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        Service Name *
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="The name of the food service (e.g., Breakfast, Lunch, Dinner, Coffee Break)"></i>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="e.g., Breakfast, Lunch, Dinner"
                                           required>
                                    <small class="form-text text-muted">Enter a clear, descriptive name for this meal or service</small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="order" class="form-label">
                                        Display Order
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Controls the order in which services appear (1 = first, 2 = second, etc.)"></i>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('order') is-invalid @enderror" 
                                           id="order" 
                                           name="order" 
                                           value="{{ old('order', 1) }}"
                                           placeholder="e.g., 1"
                                           min="0">
                                    <small class="form-text text-muted">Lower numbers appear first (e.g., Breakfast=1, Lunch=2, Dinner=3)</small>
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    Description
                                    <i class="bi bi-question-circle text-muted" 
                                       data-bs-toggle="tooltip" 
                                       title="Optional details about this food service"></i>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3"
                                          placeholder="e.g., Continental breakfast with coffee, tea, and pastries">{{ old('description') }}</textarea>
                                <small class="form-text text-muted">Add details about what's included, dietary options, or special notes</small>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_time" class="form-label">
                                        Default Start Time
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Default time when this service begins (can be overridden per event)"></i>
                                    </label>
                                    <input type="time" 
                                           class="form-control @error('start_time') is-invalid @enderror" 
                                           id="start_time" 
                                           name="start_time" 
                                           value="{{ old('start_time') }}">
                                    <small class="form-text text-muted">e.g., 07:00 for breakfast, 12:00 for lunch (optional)</small>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="end_time" class="form-label">
                                        Default End Time
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Default time when this service ends (can be overridden per event)"></i>
                                    </label>
                                    <input type="time" 
                                           class="form-control @error('end_time') is-invalid @enderror" 
                                           id="end_time" 
                                           name="end_time" 
                                           value="{{ old('end_time') }}">
                                    <small class="form-text text-muted">e.g., 09:00 for breakfast, 14:00 for lunch (optional)</small>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           role="switch"
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active Status</strong>
                                        <i class="bi bi-question-circle text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Only active services can be assigned to events"></i>
                                    </label>
                                    <br>
                                    <small class="form-text text-muted">Inactive services won't be available for selection in events</small>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Create Food Service
                                </button>
                                <a href="{{ route('food-services.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card rounded mt-3">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightbulb text-warning"></i> Examples
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3 mb-md-0">
                                    <h6 class="text-primary">Breakfast</h6>
                                    <p class="mb-1"><small><strong>Order:</strong> 1</small></p>
                                    <p class="mb-1"><small><strong>Time:</strong> 07:00 - 09:00</small></p>
                                    <p class="mb-0"><small><strong>Description:</strong> Continental breakfast with coffee and pastries</small></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3 mb-md-0">
                                    <h6 class="text-primary">Lunch</h6>
                                    <p class="mb-1"><small><strong>Order:</strong> 2</small></p>
                                    <p class="mb-1"><small><strong>Time:</strong> 12:00 - 14:00</small></p>
                                    <p class="mb-0"><small><strong>Description:</strong> Buffet lunch with vegetarian options</small></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h6 class="text-primary">Dinner</h6>
                                    <p class="mb-1"><small><strong>Order:</strong> 3</small></p>
                                    <p class="mb-1"><small><strong>Time:</strong> 18:00 - 20:00</small></p>
                                    <p class="mb-0"><small><strong>Description:</strong> Formal dinner with plated service</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('sidebar-scripts')
        <script>
            // Initialize Bootstrap tooltips
            $(document).ready(function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        </script>
    @endpush
@endsection