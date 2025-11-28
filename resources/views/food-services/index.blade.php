@extends('layouts.sidebar')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Food Services</h4>
                        </div>
                        <a href="{{ route('food-services.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Food Service
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="foodServicesTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Time Range</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($foodServices as $service)
                                        <tr>
                                            <td>{{ $service->order }}</td>
                                            <td>{{ $service->name }}</td>
                                            <td>{{ Str::limit($service->description, 50) }}</td>
                                            <td>
                                                @if($service->start_time && $service->end_time)
                                                    {{ $service->start_time }} - {{ $service->end_time }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($service->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('food-services.edit', $service->id) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('food-services.destroy', $service->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this food service?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No food services found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('sidebar-scripts')
        <script>
            $(document).ready(function() {
                $('#foodServicesTable').DataTable({
                    order: [[0, 'asc']],
                    pageLength: 25,
                });
            });
        </script>
    @endpush
@endsection