<?php

namespace App\DataTables;

use App\Models\Student as Siswa;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class SiswaDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Siswa> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // Get user's timezone from session (set by your layout script)
        $userTimezone = session('timezone', 'UTC');

        return (new EloquentDataTable($query))
            ->addColumn('class_name', fn($q) => $q->class->class_name ?? '-')
            ->addColumn('email', fn($q) => $q->user->email ?? '-')
            ->addColumn('photo', fn($q) =>
                $q->photo
                ? '<img class="photo-img" src="'.asset("storage/".$q->photo).'" />'
                : '<span class="text-muted">No photo</span>'
            )
            ->addColumn('created_at', function ($row) use ($userTimezone) {
                if (!$row->created_at) {
                    return '-';
                }
                
                // Parse UTC timestamp and convert to user's timezone
                $dateInUserTz = Carbon::parse($row->created_at, 'UTC')
                    ->setTimezone($userTimezone);
                
                // Format with timezone-aware date
                return $dateInUserTz->format('d-M-Y | H:i');
            })
            ->addColumn('action', function($q){
                return '
                    <a href="'.route('admin.siswa.edit', $q->id_student).'" class="dt-btn edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="'.route('admin.siswa.destroy', $q->id_student).'" method="POST" style="display:inline;">
                        '.csrf_field().method_field('DELETE').'
                        <button type="button" onclick="confirmDelete(this)" class="dt-btn delete">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>';
            })
            ->rawColumns(['photo', 'action'])
            ->setRowId('id_student');
        }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Siswa>
     */
    public function query(Siswa $model): QueryBuilder
    {
        return Siswa::with(['user', 'class']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('siswa-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->selectStyleSingle()
            ->parameters([
                'dom' => '<"d-none"l><"d-none"f>t<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
                'scrollX' => false,
                'autoWidth' => true,
                'responsive' => false,
            ])
            ->buttons([]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id_student')->title('ID Siswa')->width(40)->addClass('columnID'),
            Column::make('nisn')->title('NISN')->width(90),
            Column::make('photo')->title('Foto')->width(180),
            Column::make('student_name')->title('Nama')->width(180),
            Column::make('class_name')->title('Kelas')->width(50),
            Column::make('email')->title('Email')->width(210),
            Column::make('created_at')->title('Dibuat tanggal')->width(130),
            Column::computed('action')->title('Aksi')
            ->exportable(false)
            ->printable(false)
            ->width(170)
            ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Siswa_' . date('YmdHis');
    }
}
