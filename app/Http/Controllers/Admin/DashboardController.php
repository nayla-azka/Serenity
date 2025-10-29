<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\Visit;
use App\Models\Report;
use App\Models\Counselor;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\CommentReport;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get user's timezone from session or request
        $userTimezone = $request->session()->get('timezone', 'UTC');
        
        /*---------------------------------------
        ------------------Card Stats------------
        ---------------------------------------*/

        // Total Pengunjung (unique IP addresses)
        $totalVisitors = Visit::distinct('ip_address')->count('ip_address');

        // Current month visitors for growth calculation
        $thisMonthVisitors = Visit::whereMonth('visited_at', Carbon::now()->month)
            ->whereYear('visited_at', Carbon::now()->year)
            ->distinct('ip_address')
            ->count('ip_address');

        $lastMonthVisitors = Visit::whereMonth('visited_at', Carbon::now()->subMonth()->month)
            ->whereYear('visited_at', Carbon::now()->subMonth()->year)
            ->distinct('ip_address')
            ->count('ip_address');

        $visitorGrowth = $lastMonthVisitors > 0
            ? round((($thisMonthVisitors - $lastMonthVisitors) / $lastMonthVisitors) * 100, 1)
            : ($thisMonthVisitors > 0 ? 100 : 0);

        // --- Total Interaksi Artikel ---
        $totalInteractions = DB::table('v_articles')->count();

        $thisMonthInteractions = DB::table('v_articles')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonthInteractions = DB::table('v_articles')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $interactionGrowth = $lastMonthInteractions > 0
            ? round((($thisMonthInteractions - $lastMonthInteractions) / $lastMonthInteractions) * 100, 1)
            : ($thisMonthInteractions > 0 ? 100 : 0);

        // --- Total Laporan ---
        $totalReports = Report::count();

        $thisMonthReports = Report::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonthReports = Report::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $reportGrowth = $lastMonthReports > 0
            ? round((($thisMonthReports - $lastMonthReports) / $lastMonthReports) * 100, 1)
            : ($thisMonthReports > 0 ? 100 : 0);

        // --- Total Konseling Digital ---
        $totalChatsStarted = ChatSession::count();

        $thisMonthChats = ChatSession::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonthChats = ChatSession::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $chatGrowth = $lastMonthChats > 0
            ? round((($thisMonthChats - $lastMonthChats) / $lastMonthChats) * 100, 1)
            : ($thisMonthChats > 0 ? 100 : 0);

        /*---------------------------------------
        ------------------Chart Data------------
        ---------------------------------------*/

        // Get monthly data for current year
        $currentYear = Carbon::now()->year;
        
        // Visitors by month (unique IP per month)
        $visitorsByMonth = Visit::selectRaw('MONTH(visited_at) as month, COUNT(DISTINCT ip_address) as total')
            ->whereYear('visited_at', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Reports by month
        $reportsByMonth = Report::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Chat sessions by month
        $chatsByMonth = ChatSession::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Interactions by month
        $interactionsByMonth = DB::table('v_articles')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Format data for Chart.js (ensure all 12 months are present)
        $months = range(1, 12);
        $visitorsData = [];
        $reportsData = [];
        $chatsData = [];
        $interactionsData = [];

        foreach ($months as $month) {
            $visitorsData[]     = $visitorsByMonth[$month] ?? 0;
            $reportsData[]      = $reportsByMonth[$month] ?? 0;
            $chatsData[]        = $chatsByMonth[$month] ?? 0;
            $interactionsData[] = $interactionsByMonth[$month] ?? 0;
        }

        /*---------------------------------------
        ------------------Chat (Counselors)----
        ---------------------------------------*/

        $recentChats = [];
        
        $user = Auth::user();
        if ($user && ($user->role === 'counselor' || $user->role === 'konselor')) {
            $counselor = Counselor::where('user_id', $user->id)->first();
            
            if ($counselor) {
                $recentChats = ChatSession::with(['student.user', 'latestMessage'])
                    ->where('id_counselor', $counselor->id_counselor)
                    ->where('is_active', true)
                    ->whereHas('messages', function($query) {
                        $query->where('sender_type', 'student')
                              ->where('status', 'sent');
                    })
                    ->orderBy('updated_at', 'desc')
                    ->take(3)
                    ->get()
                    ->map(function($session) use ($userTimezone) {
                        $session->unread_count = ChatMessage::where('id_session', $session->id_session)
                            ->where('sender_type', 'student')
                            ->where('status', 'sent')
                            ->count();
                        
                        $latestMessage = ChatMessage::where('id_session', $session->id_session)
                            ->orderBy('sent_at', 'desc')
                            ->first();
                        
                        $session->latest_message_text = $latestMessage ? 
                            \Str::limit($latestMessage->message, 30) : 'No messages yet';
                        
                        // ADDED: Convert message time to user's timezone
                        if ($latestMessage) {
                            $messageTime = Carbon::parse($latestMessage->sent_at)
                                ->setTimezone($userTimezone);
                            
                            $session->latest_message_time = $messageTime->format('H:i');
                            $session->latest_message_date = $messageTime->toDateString();
                            
                            // Add relative time (e.g., "2 minutes ago")
                            $session->latest_message_relative = $messageTime->diffForHumans();
                        } else {
                            $session->latest_message_time = '';
                            $session->latest_message_date = '';
                            $session->latest_message_relative = '';
                        }
                        
                        return $session;
                    });
            }
        }

        /*---------------------------------------
        ------------------Reports (Recent)-----
        ---------------------------------------*/
        
        $laporan = DB::table('v_reports')
            ->orderByRaw("CASE 
                WHEN status = 'Menunggu' THEN 1
                WHEN status = 'Diproses' THEN 2
                ELSE 3
            END")
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($report) use ($userTimezone) {
                // Convert report timestamps to user's timezone
                if ($report->created_at) {
                    $createdTime = Carbon::parse($report->created_at)
                        ->setTimezone($userTimezone);
                    
                    $report->created_at_formatted = $createdTime->format('M d, Y H:i');
                    $report->created_at_relative = $createdTime->diffForHumans();
                }
                
                // Handle anonymous reporting
                // Check if is_anonymous flag exists and is true
                if (isset($report->is_anonymous) && $report->is_anonymous) {
                    $report->display_name = 'Anonim';
                } else {
                    // Use the name from the view, or fallback to 'Anonim' if null
                    $report->display_name = $report->name ?? 'Anonim';
                }
                
                return $report;
            });

        $pendingReports = collect();

        // For admin users, get pending comment reports
        if ($user && $user->role === 'admin') {
            $pendingReports = CommentReport::with(['reporter', 'reviewer', 'comment.user'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function($report) use ($userTimezone) {
                    // ADDED: Convert report timestamps to user's timezone
                    if ($report->created_at) {
                        $createdTime = Carbon::parse($report->created_at)
                            ->setTimezone($userTimezone);
                        
                        $report->created_at_formatted = $createdTime->format('M d, Y H:i');
                        $report->created_at_relative = $createdTime->diffForHumans();
                    }
                    
                    return $report;
                });
        }

        /*---------------------------------------
        ------------------Additional Stats-----
        ---------------------------------------*/

        // Calculate some additional useful metrics
        $todayVisitors = Visit::whereDate('visited_at', Carbon::today())
            ->distinct('ip_address')
            ->count();

        $activeChats = ChatSession::where('is_active', true)->count();

        $pendingReportsCount = Report::where('status', 'pending')->count();

        return view('admin.dashboard', [
            // Main stats
            'totalVisitors'     => $totalVisitors,
            'visitorGrowth'     => $visitorGrowth,
            'totalInteractions' => $totalInteractions,
            'interactionGrowth' => $interactionGrowth,
            'totalReports'      => $totalReports,
            'reportGrowth'      => $reportGrowth,
            'totalChatsStarted' => $totalChatsStarted,
            'chatGrowth'        => $chatGrowth,
            
            // Chart data
            'visitorsData'      => $visitorsData,
            'reportsData'       => $reportsData,
            'chatsData'         => $chatsData,
            'interactionsData'  => $interactionsData,
            
            // Additional data
            'laporan'           => $laporan,
            'recentChats'       => $recentChats,
            'pendingReports'    => $pendingReports,
            'todayVisitors'     => $todayVisitors,
            'activeChats'       => $activeChats,
            'pendingReportsCount' => $pendingReportsCount,
            
            // Monthly comparisons
            'thisMonthVisitors'    => $thisMonthVisitors,
            'thisMonthInteractions' => $thisMonthInteractions,
            'thisMonthReports'     => $thisMonthReports,
            'thisMonthChats'       => $thisMonthChats,
            
            // ADDED: User timezone for client-side use
            'userTimezone'         => $userTimezone,
        ]);
    }

    /**
     * Get dashboard data for specific period (for AJAX requests)
     */
    public function getDashboardData(Request $request)
    {
        $period = $request->get('period', 'month');
        $currentDate = Carbon::now();
        
        switch ($period) {
            case 'quarter':
                $startDate = $currentDate->copy()->subMonths(3);
                $endDate = $currentDate->copy();
                break;
            case 'year':
                $startDate = $currentDate->copy()->subYear();
                $endDate = $currentDate->copy();
                break;
            default: // month
                $startDate = $currentDate->copy()->startOfMonth();
                $endDate = $currentDate->copy()->endOfMonth();
        }

        // Get data for the specified period
        $visitors = Visit::whereBetween('visited_at', [$startDate, $endDate])
            ->distinct('ip_address')
            ->count();

        $interactions = DB::table('v_articles')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $reports = Report::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $chats = ChatSession::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Calculate growth compared to previous period
        $previousStart = $period === 'year' 
            ? $startDate->copy()->subYear() 
            : ($period === 'quarter' 
                ? $startDate->copy()->subMonths(3) 
                : $startDate->copy()->subMonth());
                
        $previousEnd = $period === 'year' 
            ? $endDate->copy()->subYear() 
            : ($period === 'quarter' 
                ? $endDate->copy()->subMonths(3) 
                : $endDate->copy()->subMonth());

        $previousVisitors = Visit::whereBetween('visited_at', [$previousStart, $previousEnd])
            ->distinct('ip_address')
            ->count();

        $previousInteractions = DB::table('v_articles')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        $previousReports = Report::whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        $previousChats = ChatSession::whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        // Calculate growth percentages
        $visitorGrowth = $previousVisitors > 0 
            ? round((($visitors - $previousVisitors) / $previousVisitors) * 100, 1)
            : ($visitors > 0 ? 100 : 0);

        $interactionGrowth = $previousInteractions > 0 
            ? round((($interactions - $previousInteractions) / $previousInteractions) * 100, 1)
            : ($interactions > 0 ? 100 : 0);

        $reportGrowth = $previousReports > 0 
            ? round((($reports - $previousReports) / $previousReports) * 100, 1)
            : ($reports > 0 ? 100 : 0);

        $chatGrowth = $previousChats > 0 
            ? round((($chats - $previousChats) / $previousChats) * 100, 1)
            : ($chats > 0 ? 100 : 0);

        return response()->json([
            'visitors' => [
                'total' => $visitors,
                'growth' => $visitorGrowth
            ],
            'interactions' => [
                'total' => $interactions,
                'growth' => $interactionGrowth
            ],
            'reports' => [
                'total' => $reports,
                'growth' => $reportGrowth
            ],
            'chats' => [
                'total' => $chats,
                'growth' => $chatGrowth
            ]
        ]);
    }
}