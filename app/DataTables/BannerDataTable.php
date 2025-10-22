<?php

namespace App\DataTables;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class BannerDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Banner> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // Get user's timezone from session (set by your layout script)
        $userTimezone = session('timezone', 'UTC');

        return (new EloquentDataTable($query))
            ->addColumn('photo', function($query){
                return '<img class="photo-img" data-id="'.$query->id.'" src="'.asset("storage/".$query->photo).'" />';
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
                    <a href="'.route('admin.banner.edit', $query->id).'" class="dt-btn edit"><i class="fas fa-edit">Edit</i></a>
                    <form action="'.route('admin.banner.destroy', $query->id).'" method="POST" style="display:inline;">
                        '.csrf_field().method_field('DELETE').'
                        <button type="button" onclick="confirmDelete(this)" class="dt-btn delete"><i class="fas fa-trash">Hapus</i></button>
                    </form>';
            })
            ->rawColumns(['photo', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Banner>
     */
    public function query(Banner $model): QueryBuilder
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
            Column::make('id')->title('ID Banner')->width(55)->addClass('columnID'),
            Column::make('photo')->title('Foto')->width(180),
            Column::make('title')->title('Judul')->width(130),
            Column::make('desc')->title('Deskripsi')->width(400)->addClass('wrap-text'),
            Column::make('created_at')->title('Dibuat tanggal')->width(100),
            Column::computed('action')->title('Aksi')
            ->exportable(false)
            ->printable(false)
            ->width(170),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Banner_' . date('YmdHis');
    }
}
