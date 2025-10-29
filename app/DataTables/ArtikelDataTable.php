<?php

namespace App\DataTables;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\QueryDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Carbon\Carbon;

class ArtikelDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param mixed $query Results from query() method.
     */
    public function dataTable($query): QueryDataTable
    {
        // Get user's timezone from session (set by your layout script)
        $userTimezone = session('timezone', 'UTC');
        
        return (new QueryDataTable($query))
            ->addColumn('photo', function ($row) {
                return '<img class="photo-img"
                             data-id="'.$row->article_id.'"
                             src="'.asset("storage/".$row->photo).'"
                             style="max-width:120px; max-height:80px; object-fit:cover;" />';
            })
            ->editColumn('content', function ($row) {
                return $row->content;
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
            ->addColumn('action', function ($row) {
                return '
                    <a href="'.route('admin.artikel.edit', $row->article_id).'" class="dt-btn edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="'.route('admin.artikel.destroy', $row->article_id).'" method="POST" style="display:inline;">
                        '.csrf_field().method_field('DELETE').'
                        <button type="button" onclick="confirmDelete(this)" class="dt-btn delete">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>';
            })
            ->rawColumns(['photo', 'content', 'action'])
            ->setRowId('article_id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query()
    {
        // Pakai view article_overview
        return DB::table('article_overview');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('artikel-table')
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
            Column::make('article_id')->title('ID')->width(55)->addClass('columnID'),
            Column::make('photo')->title('Foto')->width(180),
            Column::make('title')->title('Judul')->width(130),
            Column::make('content')->title('Konten')->width(400)->addClass('wrap-text'),
            Column::make('author_name')->title('Penulis')->width(90),
            Column::make('total_likes')->title('Likes')->width(60),
            Column::make('total_views')->title('Views')->width(60),
            Column::make('total_comments')->title('Komentar')->width(75),
            Column::make('created_at')->title('Dibuat Tanggal')->width(120),
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
        return 'Artikel_' . date('YmdHis');
    }
}
