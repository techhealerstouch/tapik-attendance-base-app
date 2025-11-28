<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Models\Attendance;
use App\Models\Event;
use App\Exports\AttendancesExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceTable extends DataTableComponent
{
    protected $model = Attendance::class;

    protected $listeners = ['attendanceAdded' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('attendances.time_in', 'desc');
        $this->setPerPageAccepted([50, 100, 250, 500, 1000, -1]);
        $this->setColumnSelectEnabled();
    }

    public function columns(): array
    {
        return [
            Column::make(__('RFID'), "user.rfid_no")
                ->sortable()
                ->searchable(),
            Column::make(__('Name'), "user.name")
                ->sortable()
                ->searchable(),
            Column::make(__('Event'), "event_name")
                ->sortable()
                ->searchable(),
            Column::make(__('Time In'), "time_in")
                ->sortable()
                ->searchable(),
            Column::make(__('Status'), "status")
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        $events = Event::pluck('title', 'title')->toArray();
        return [
            SelectFilter::make(__('Event'))
                ->options(['' => 'All Events'] + $events)
                ->filter(function ($builder, $value) {
                    if ($value) {
                        $builder->where('event_name', $value);
                    }
                }),
        ];
    }

        public function exportExcel()
    {
        Log::info('test');
        $data = $this->query()->get(); // Fetch the filtered/sorted data
        return Excel::download(new AttendancesExport($data), 'attendances.xlsx');
    }

    public function query()
    {
        return Attendance::query()
            ->with(['user', 'event'])
            ->select('attendances.*');
    }
}
