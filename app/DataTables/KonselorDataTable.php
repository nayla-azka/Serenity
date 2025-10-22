<?php

namespace App\DataTables;

use App\Models\Counselor as Konselor;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class KonselorDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Konselor> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // Get user's timezone from session (set by your layout script)
        $userTimezone = session('timezone', 'UTC');

        return (new EloquentDataTable($query))
           ->addColumn('photo', function($query){
                return '<img style="width:200px" src="'.asset("storage/".$query->photo).'" />';
            })
            ->addColumn('email', function ($query) {
                return $query->user->email ?? '-';
            })
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
            ->addColumn('action', function($query){
                return '
                    <a href="'.route('admin.konselor.edit', $query->id_counselor).'" class="dt dt-btn edit"><i class="fas fa-edit">Edit</i></a>
                    <form action="'.route('admin.konselor.destroy', $query->id_counselor).'" method="POST" style="display:inline;">
                        '.csrf_field().method_field('DELETE').'
                        <button type="button" onclick="confirmDelete(this)" class="dt dt-btn delete"><i class="fas fa-trash">Hapus</i></button>
                    </form>';
            })
            ->addColumn('description', function($row){
                return $row->description;
            })
            ->setRowClass(function ($row) {
                return 'wrap-text'; // optional, if you want per-row
            })
            ->rawColumns(['photo', 'action'])
            ->setRowId('id');


    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Konselor>
     */
    public function query(Konselor $model): QueryBuilder
    {
        return $model->newQuery()->with('user');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('konselor-table')
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
        Column::make('id_counselor')->title('ID Konselor')->width(59),
        Column::make('nip')->title('NIP')->width(95),
        Column::make('photo')->title('Foto')->width(300),
        Column::make('counselor_name')->title('Nama')->width(180),
        Column::make('kelas')->title('Menangani Kelas')->width(80),
        Column::make('contact')->title('Nomor Telepon')->width(110),
        Column::make('desc')->title('Bio')->addClass('wrap-text')->width(200),
        Column::make('email')->title('Email')->width(160),
        Column::make('created_at')->title('Dibuat tanggal')->width(100),
        Column::computed('action')
            ->title('Aksi')
            ->exportable(false)
            ->printable(false)
            ->width(165),
    ];
}

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Konselor_' . date('YmdHis');
    }
}
