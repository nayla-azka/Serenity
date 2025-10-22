<?php

namespace App\DataTables;

use App\Models\CommentReport;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class ReportDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<CommentReport> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // Get user's timezone from session (set by your layout script)
        $userTimezone = session('timezone', 'UTC');

        return (new EloquentDataTable($query))
            ->addColumn('checkbox', fn ($row) =>
                '<input type="checkbox" class="select-row" value="'.$row->report_id.'">'
            )
            ->addColumn('comment_content', function ($row) use ($userTimezone) {
                $target = $row->target();

                // Kalau comment hilang (hard delete)
                if (!$target) {
                    return '
                        <div style="line-height:1.4;">
                            <span class="badge badge-danger">Komentar sudah dihapus permanen</span><br>
                            <em>[Tidak ada konten]</em>
                        </div>
                    ';
                }

                $user = $row->targetUser()->name ?? 'Unknown';
                $text = $target->comment_text ?? '[tidak ada teks]';
                // Handle date field - convert from UTC to user timezone
                if ($row->created_at) {
                    try {
                        $date = Carbon::parse($row->created_at, 'UTC')
                            ->setTimezone($userTimezone)
                            ->format('d M Y H:i');
                    } catch (\Exception $e) {
                        $date = '-';
                    }
                } else {
                    $date = '-';
                }
                $articleTitle = $target->article->title ?? 'Artikel sudah dihapus';
                $articleId = $target->article_id ?? null;
                $targetId = $target->comment_id;

                $url = $articleId
                    ? route('public.artikel_show', $articleId) . '#target-' . $targetId
                    : '#';

                $badge = $target->is_removed
                    ? '<span class="badge badge-danger text-black">Sudah dihapus</span>'
                    : '';

                return '
                    <div style="line-height:1.4; position:relative;">
                        <strong>' . e($user) . '</strong>
                        <span class="text-muted">(' . $date . ')</span> ' . $badge . '<br>
                        "' . e(\Illuminate\Support\Str::limit($text, 60)) . '"
                        <div class="d-flex justify-content-end mt-2">
                            <div class="text-end mt-2 border rounded p-2 bg-light position-relative dt dt-btn create">
                                <a href="#" class="ml-1 text-primary preview-comment"
                                    data-comment="'.e($text).'"
                                    data-article="'.e($articleTitle).'"
                                    data-user="'.e($user).'"
                                    data-date="'.$date.'">
                                    <i class="fas fa-eye me-1"></i>
                                </a>
                            </div>
                            <div class="text-end mt-2 border rounded p-2 bg-light position-relative dt dt-btn create">
                                <a href="'.$url.'" target="_blank" class="ml-1 text-blue-600">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('reporter', fn ($row) =>
                $row->reporter->name ?? 'Unknown'
            )
           ->editColumn('status', function ($row) {
                $badges = [
                    'Pending' => '<span class="badge bg-warning">Pending</span>',
                    'Diterima' => '<span class="badge bg-success">Diterima</span>',
                    'Ditolak' => '<span class="badge bg-danger">Ditolak</span>',
                ];
                return $badges[$row->status] ?? '<span class="badge badge-secondary">' . e(ucfirst($row->status)) . '</span>';
            })
            ->addColumn('review_info', function ($row) use ($userTimezone) {
                if ($row->isReviewed()) {
                    $reviewerName = $row->reviewer->name ?? 'Unknown';
                    // Handle date field - convert from UTC to user timezone
                if ($row->reviewed_at) {
                    try {
                        $reviewDate = Carbon::parse($row->created_at, 'UTC')
                            ->setTimezone($userTimezone)
                            ->format('d M Y H:i');
                    } catch (\Exception $e) {
                        $reviewDate = '-';
                    }
                } else {
                    $reviewDate = '-';
                }
                    $adminNotes = $row->admin_notes ? '<br><small class="text-muted">"' . e(\Illuminate\Support\Str::limit($row->admin_notes, 50)) . '"</small>' : '';

                    return '
                        <div style="line-height:1.3;">
                            <small class="text-success">
                                <i class="fas fa-user-check"></i> ' . e($reviewerName) . '<br>
                                <i class="fas fa-clock"></i> ' . $reviewDate . '
                            </small>
                            ' . $adminNotes . '
                        </div>
                    ';
                }
                return '<span class="text-muted"><i class="fas fa-clock"></i> Belum ditinjau</span>';
            })
            ->addColumn('created_at', function($row){
                return formatDateTz($row->created_at);
            })
            ->addColumn('action', function ($row) {
                $actions = '';

                // Status update buttons
                if ($row->status === 'Pending') {
                    $actions .= '
                        <button class="dt dt-btn terima btn-sm update-status"
                                data-id="'.$row->report_id.'"
                                data-status="Diterima"
                                title="Terima laporan"
                                style="height: 30px; width: 75px;">
                            <i class="fas fa-check me-1"></i> Terima
                        </button>
                        <button class="dt dt-btn delete btn-sm update-status"
                                data-id="'.$row->report_id.'"
                                data-status="Ditolak"
                                title="Tolak laporan"
                                style="height: 30px; width: 75px;">
                            <i class="fas fa-times me-1"></i>Tolak
                        </button>
                    ';
                } else {
                    $actions .= '
                        <button class="dt dt-btn pending btn-sm update-status"
                                data-id="'.$row->report_id.'"
                                data-status="Pending"
                                title="Kembalikan ke Pending"
                                style="height: 29px; width: 90px;">
                            <i class="fas fa-undo"></i> Pending
                        </button>
                    ';
                }

                // Add review/notes button
                $actions .= '
                    <button class="dt dt-btn edit btn-sm ml-1 view-details"
                            data-id="'.$row->report_id.'"
                            data-reason="'.e($row->reason).'"
                            data-notes="'.e($row->admin_notes ?? '').'"
                            data-status="'.$row->status.'"
                            title="Lihat detail / Tambah catatan">
                        <i class="fas fa-eye"></i>
                    </button>
                ';

                return $actions;
            })
            ->rawColumns(['checkbox', 'comment_content', 'status', 'review_info', 'action'])
            ->setRowId('report_id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<CommentReport>
     */
    public function query(CommentReport $model): QueryBuilder
    {
        $query = $model->newQuery()->with([
            'comment.user',
            'comment.article',
            'comment.replies.user',
            'reporter',
            'reviewer' // Add reviewer relationship
        ]);

        if (request()->has('status') && request('status') !== 'all') {
            $query->where('status', request('status'));
        }

        return $query->
        selectRaw('comment_reports.*,
            CASE
                WHEN status = "Pending" THEN 1
                WHEN status = "Diterima" THEN 2
                WHEN status = "Ditolak" THEN 3
                ELSE 4
            END as status_order
        ')
        ->orderBy('status_order')      // pending → diterima → ditolak
        ->orderBy('created_at', 'desc'); // newest first in each group
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
        ->setTableId('report-table')
        ->columns($this->getColumns())
        ->Ajax([
            'data' => 'function(d) {
                d.status = $("#report-tabs .nav-link.active").data("status");
            }'
        ])
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
            Column::computed('checkbox')
                ->title('<input type="checkbox" id="select-all">')
                ->exportable(false)
                ->printable(false)
                ->width(10)
                ->addClass('text-center'),

            Column::make('comment_content')->title('Komentar')->width(300),
            Column::make('reporter')->title('Pelapor')->width(100),
            Column::make('reason')->title('Alasan')->width(200),
            Column::make('status')->title('Status')->width(70)->addClass('text-center')->orderable(true)->searchable(true),
            Column::computed('review_info')->title('Review Info')->width(150),
            Column::make('created_at')->title('Tanggal')->width(100),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(210)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Report_' . date('YmdHis');
    }
}
