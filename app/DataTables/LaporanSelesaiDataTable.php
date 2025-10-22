<?php

namespace App\DataTables;

use App\Models\Report;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class LaporanSelesaiDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Report> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // Get user's timezone from session (set by your layout script)
        $userTimezone = session('timezone', 'UTC');
        return (new EloquentDataTable($query))
            ->addColumn('laporan_content', function ($row) use ($userTimezone) {
                $pengirim = $row->user ? e($row->user->name) : '<em>Anonim</em>';
                $headline = e($row->topic ?? '[Tanpa Judul]');
                
                // Handle date field - convert from UTC to user timezone
                if ($row->date) {
                    try {
                        $submittedDate = Carbon::parse($row->date, 'UTC')
                            ->setTimezone($userTimezone)
                            ->format('d M Y');
                    } catch (\Exception $e) {
                        $submittedDate = '-';
                    }
                } else {
                    $submittedDate = '-';
                }
                
                // Handle updated_at - convert from UTC to user timezone
                if ($row->updated_at) {
                    try {
                        $statusDate = Carbon::parse($row->updated_at, 'UTC')
                            ->setTimezone($userTimezone)
                            ->format('d M Y H:i');
                    } catch (\Exception $e) {
                        $statusDate = '-';
                    }
                } else {
                    $statusDate = '-';
                }

                switch ($row->status) {
                    case 'Selesai':
                        $badge = '<span class="badge bg-success">Selesai</span>';
                        break;
                    case 'Ditolak':
                        $badge = '<span class="badge bg-danger">Ditolak</span>';
                        break;
                    default:
                        $badge = '<span class="badge bg-secondary">'.e(ucfirst($row->status)).'</span>';
                }

                return '
                    <div class="laporan-card card-selectable p-3 border rounded shadow-sm mb-2"
                        data-id="'.$row->id.'"
                        style="cursor:pointer; line-height:1.4;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>' . $pengirim . '</strong><br>
                                <span class="text-muted">"'.$headline.'"</span><br>
                                <small class="text-muted">Dikirim: '.$submittedDate.'</small>
                            </div>
                            <div class="text-right">
                                '.$badge.'<br>
                                <small class="text-muted">'.$statusDate.'</small>
                            </div>
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['laporan_content']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Report>
     */
    public function query(Report $model): QueryBuilder
    {
          return $model->newQuery()
            ->whereIn('status', ['Selesai', 'Ditolak'])
            ->with('user:id,name')
            ->select(['id', 'topic', 'date', 'place', 'chronology', 'status', 'created_at', 'updated_at', 'sender_id']);

    }
    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('laporan-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
         return [
            Column::computed('laporan_content')
                ->title('Laporan')
                ->orderable(false)
                ->searchable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Laporan' . date('YmdHis');
    }
}
