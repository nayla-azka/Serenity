<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class UserDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // Get user's timezone from session (set by your layout script)
        $userTimezone = session('timezone', 'UTC');

        return (new EloquentDataTable($query))
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
                    <a href="'.route('admin.user.edit', $query->id).'" class="dt dt-btn edit"><i class="fas fa-edit">Edit</i></a>
                    <form action="'.route('admin.user.destroy', $query->id).'" method="POST" style="display:inline;">
                        '.csrf_field().method_field('DELETE').'
                        <button type="button" onclick="confirmDelete(this)" class="dt dt-btn delete"><i class="fas fa-trash">Hapus</i></button>
                    </form>';
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('banner-table')
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
            Column::make('id')->title('ID')->width(20),
            Column::make('name')->title('Nama')->width(230),
            Column::make('email')->title('Email')->width(230),
            Column::make('role')->title('Peran')->width(50),
            Column::make('created_at')->title('Dibuat Tanggal')->width(100),
            Column::computed('action')
                ->title('Aksi')
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
        return 'User_' . date('YmdHis');
    }
}
