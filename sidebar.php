<?php
// layout.php — Shared HTML header + sidebar navigation
// Usage: include at the top of every page after setting $page_title and $active_page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($page_title ?? 'BookStore IMS') ?> — BookStore IMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 230px;
            --sidebar-bg: #1a2332;
            --sidebar-accent: #3b82f6;
            --sidebar-text: #cbd5e1;
            --sidebar-hover: rgba(59,130,246,0.12);
        }
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--sidebar-bg); color: var(--sidebar-text);
            display: flex; flex-direction: column;
            z-index: 1000; overflow-y: auto;
        }
        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-brand h5 { color: #fff; font-weight: 700; margin: 0; font-size: 1rem; }
        .sidebar-brand small { color: #64748b; font-size: 0.72rem; }

        .sidebar-section { padding: 0.6rem 1rem 0.2rem; font-size: 0.67rem;
            text-transform: uppercase; letter-spacing: 0.08em; color: #475569; }

        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 0.55rem 1.25rem; color: var(--sidebar-text);
            text-decoration: none; font-size: 0.875rem; transition: all .15s;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar-link.active {
            background: var(--sidebar-hover); color: #fff;
            border-left-color: var(--sidebar-accent);
        }
        .sidebar-link i { font-size: 1rem; width: 18px; }

        /* ── Main Content ── */
        #main { margin-left: var(--sidebar-width); padding: 1.75rem; min-height: 100vh; }

        /* ── Cards ── */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); }
        .card-header { border-radius: 12px 12px 0 0 !important; }

        /* ── Status badges ── */
        .badge-available { background: #dcfce7; color: #166534; }
        .badge-borrowed  { background: #fef9c3; color: #854d0e; }
        .badge-overdue   { background: #fee2e2; color: #991b1b; }

        /* ── Tables ── */
        .table th { font-size: 0.78rem; text-transform: uppercase;
            letter-spacing: 0.04em; color: #64748b; border-top: none; }
        .table td { vertical-align: middle; font-size: 0.875rem; }

        /* ── Stat cards ── */
        .stat-card { border-radius: 12px; padding: 1.2rem 1.4rem;
            display: flex; align-items: center; gap: 1rem; }
        .stat-card .icon { width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .stat-card .value { font-size: 1.8rem; font-weight: 700; line-height: 1; }
        .stat-card .label { font-size: 0.78rem; color: #64748b; margin-top: 2px; }
    </style>
</head>
<body>

<!-- ══ Sidebar ══════════════════════════════════════════════ -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <h5>📚 BookStore IMS</h5>
        <small>Bookstore Inventory and Sales Management System</small>
    </div>

    <div class="sidebar-section mt-2">Main</div>
    <a href="http://localhost/LibrarySystem/dashboard.php"       class="sidebar-link <?= ($active_page??'')=='dashboard'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <div class="sidebar-section">Catalog</div>
    <a href="http://localhost/LibrarySystem/books.php"       class="sidebar-link <?= ($active_page??'')=='books'?'active':'' ?>"><i class="bi bi-book"></i> Books</a>
    <a href="http://localhost/LibrarySystem/authors.php"     class="sidebar-link <?= ($active_page??'')=='authors'?'active':'' ?>"><i class="bi bi-person-lines-fill"></i> Authors</a>
    <a href="http://localhost/LibrarySystem/categories.php"  class="sidebar-link <?= ($active_page??'')=='categories'?'active':'' ?>"><i class="bi bi-tags"></i> Categories</a>

    <div class="sidebar-section">Circulation</div>
    <a href="members.php"     class="sidebar-link <?= ($active_page??'')=='members'?'active':'' ?>"><i class="bi bi-people"></i> Members</a>
    <a href="borrow.php"      class="sidebar-link <?= ($active_page??'')=='borrow'?'active':'' ?>"><i class="bi bi-box-arrow-in-down"></i> Borrow Book</a>
    <a href="return.php"      class="sidebar-link <?= ($active_page??'')=='return'?'active':'' ?>"><i class="bi bi-box-arrow-up"></i> Return Book</a>
    <a href="fines.php"       class="sidebar-link <?= ($active_page??'')=='fines'?'active':'' ?>"><i class="bi bi-cash-coin"></i> Fines</a>

    <div class="sidebar-section">Gamification</div>
    <a href="leaderboard.php" class="sidebar-link <?= ($active_page??'')=='leaderboard'?'active':'' ?>"><i class="bi bi-trophy"></i> Leaderboard</a>
    <a href="badges.php"      class="sidebar-link <?= ($active_page??'')=='badges'?'active':'' ?>"><i class="bi bi-award"></i> Badges</a>

    <div class="sidebar-section">Analytics</div>
    <a href="reports.php"     class="sidebar-link <?= ($active_page??'')=='reports'?'active':'' ?>"><i class="bi bi-bar-chart-line"></i> Reports</a>
</nav>

<!-- ══ Main Content Area ════════════════════════════════════ -->
<div id="main">

