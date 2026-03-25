<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tenant Approval — OJT Hub</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          mint:   '#D4ECDD',
          teal:   '#345B63',
          deep:   '#152D35',
          abyss:  '#112031',
        },
        fontFamily: {
          display: ['Playfair Display', 'serif'],
          body:    ['Outfit', 'sans-serif'],
        },
      }
    }
  }
</script>
<style>
  body { font-family: 'Outfit', sans-serif; }

  .noise-bg::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events: none;
    z-index: 0;
    opacity: 0.5;
  }

  .card-enter {
    animation: cardIn .35s cubic-bezier(.22,.68,0,1.2) both;
  }

  @keyframes cardIn {
    from { opacity: 0; transform: translateY(18px) scale(.98); }
    to   { opacity: 1; transform: none; }
  }

  .row-hover {
    transition: background .15s, transform .12s;
  }
  .row-hover:hover {
    background: rgba(212,236,221,.04);
    transform: translateX(3px);
  }

  .badge-pending  { background: rgba(212,236,221,.12); color: #a8d5bc; border: 1px solid rgba(212,236,221,.2); }
  .badge-approved { background: rgba(45,212,160,.1);  color: #2dd4a0; border: 1px solid rgba(45,212,160,.2); }
  .badge-rejected { background: rgba(255,77,109,.1);  color: #ff7a94; border: 1px solid rgba(255,77,109,.2); }

  .btn-approve {
    background: linear-gradient(135deg, #345B63, #2a4a51);
    color: #D4ECDD;
    transition: all .2s;
  }
  .btn-approve:hover {
    background: linear-gradient(135deg, #3d6b74, #345B63);
    box-shadow: 0 0 20px rgba(52,91,99,.5);
    transform: translateY(-1px);
  }

  .btn-reject {
    background: transparent;
    color: #ff7a94;
    border: 1px solid rgba(255,77,109,.3);
    transition: all .2s;
  }
  .btn-reject:hover {
    background: rgba(255,77,109,.1);
    border-color: rgba(255,77,109,.6);
  }

  .modal-overlay {
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
  }

  .tab-active {
    color: #D4ECDD;
    border-bottom: 2px solid #345B63;
  }

  .tab-inactive {
    color: rgba(212,236,221,.4);
    border-bottom: 2px solid transparent;
  }

  .tab-inactive:hover {
    color: rgba(212,236,221,.7);
    border-bottom-color: rgba(52,91,99,.4);
  }

  .sidebar-link {
    transition: all .15s;
  }
  .sidebar-link:hover {
    background: rgba(212,236,221,.06);
    color: #D4ECDD;
  }
  .sidebar-link.active {
    background: rgba(52,91,99,.4);
    color: #D4ECDD;
    border-left: 2px solid #D4ECDD;
  }

  .search-input:focus {
    outline: none;
    border-color: rgba(52,91,99,.8);
    box-shadow: 0 0 0 3px rgba(52,91,99,.2);
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
  }
  .modal-anim { animation: fadeIn .2s ease; }
</style>
</head>
<body class="noise-bg bg-abyss text-mint min-h-screen flex">

<!-- Sidebar -->
<aside class="w-60 bg-[#0e1c28] border-r border-teal/20 flex flex-col fixed top-0 left-0 bottom-0 z-50">
  <div class="px-6 pt-7 pb-5 border-b border-teal/20">
    <div class="text-[10px] font-semibold tracking-widest uppercase text-teal mb-1">Super Admin</div>
    <div class="font-display text-2xl font-bold text-mint leading-tight">OJT<span class="text-teal">Hub</span></div>
  </div>

  <nav class="flex-1 px-3 py-5 space-y-1">
    <p class="text-[10px] uppercase tracking-widest text-mint/30 font-semibold px-3 pb-2">Overview</p>
    <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-mint/50 text-sm font-medium">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
      Dashboard
    </a>

    <p class="text-[10px] uppercase tracking-widest text-mint/30 font-semibold px-3 pb-2 pt-4">Management</p>
    <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-mint/50 text-sm font-medium">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      Tenants
    </a>
    <a href="#" class="sidebar-link active flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Approvals
      <span class="ml-auto bg-teal/60 text-mint text-[10px] font-bold px-1.5 py-0.5 rounded-full">3</span>
    </a>
  </nav>

  <div class="px-3 py-4 border-t border-teal/20">
    <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-teal/10">
      <div class="w-8 h-8 rounded-lg bg-teal flex items-center justify-center text-mint font-display font-bold text-sm flex-shrink-0">A</div>
      <div class="flex-1 min-w-0">
        <div class="text-sm font-medium text-mint truncate">Admin</div>
        <div class="text-[11px] text-mint/40">Super Admin</div>
      </div>
      <button class="text-mint/30 hover:text-red-400 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </button>
    </div>
  </div>
</aside>

<!-- Main -->
<div class="ml-60 flex-1 flex flex-col min-h-screen">

  <!-- Topbar -->
  <header class="sticky top-0 z-40 px-8 py-4 border-b border-teal/20 bg-abyss/80 backdrop-blur-sm flex items-center justify-between">
    <div>
      <h1 class="font-display text-xl font-bold text-mint">Tenant Approvals</h1>
      <p class="text-xs text-mint/40 mt-0.5">Review and manage institution signup requests</p>
    </div>
    <div class="flex items-center gap-3">
      <div class="relative">
        <svg class="w-4 h-4 text-mint/30 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" placeholder="Search registrations…" class="search-input bg-[#0e1c28] border border-teal/20 rounded-lg pl-9 pr-4 py-2 text-sm text-mint placeholder-mint/30 w-56 transition-all">
      </div>
    </div>
  </header>

  <main class="p-8 flex-1">

    <!-- Stats Row -->
    <div class="grid grid-cols-4 gap-4 mb-8">
      <div class="card-enter bg-[#0e1c28] border border-teal/20 rounded-xl p-5" style="animation-delay:.05s">
        <div class="text-[11px] uppercase tracking-widest text-mint/40 font-semibold mb-3">Pending</div>
        <div class="font-display text-4xl font-bold text-mint">3</div>
        <div class="text-xs text-mint/40 mt-1">Awaiting review</div>
      </div>
      <div class="card-enter bg-[#0e1c28] border border-teal/20 rounded-xl p-5" style="animation-delay:.1s">
        <div class="text-[11px] uppercase tracking-widest text-mint/40 font-semibold mb-3">Approved</div>
        <div class="font-display text-4xl font-bold text-[#2dd4a0]">18</div>
        <div class="text-xs text-mint/40 mt-1">This month</div>
      </div>
      <div class="card-enter bg-[#0e1c28] border border-teal/20 rounded-xl p-5" style="animation-delay:.15s">
        <div class="text-[11px] uppercase tracking-widest text-mint/40 font-semibold mb-3">Rejected</div>
        <div class="font-display text-4xl font-bold text-[#ff7a94]">2</div>
        <div class="text-xs text-mint/40 mt-1">This month</div>
      </div>
      <div class="card-enter bg-[#0e1c28] border border-teal/20 rounded-xl p-5" style="animation-delay:.2s">
        <div class="text-[11px] uppercase tracking-widest text-mint/40 font-semibold mb-3">Avg. Response</div>
        <div class="font-display text-4xl font-bold text-mint">4h</div>
        <div class="text-xs text-mint/40 mt-1">Review time</div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-0 border-b border-teal/20 mb-6">
      <button onclick="switchTab('pending')" id="tab-pending" class="tab-active px-5 py-3 text-sm font-medium transition-all">Pending <span class="ml-1.5 text-xs bg-teal/40 text-mint px-1.5 py-0.5 rounded-full">3</span></button>
      <button onclick="switchTab('approved')" id="tab-approved" class="tab-inactive px-5 py-3 text-sm font-medium transition-all">Approved</button>
      <button onclick="switchTab('rejected')" id="tab-rejected" class="tab-inactive px-5 py-3 text-sm font-medium transition-all">Rejected</button>
      <button onclick="switchTab('all')" id="tab-all" class="tab-inactive px-5 py-3 text-sm font-medium transition-all">All</button>
    </div>

    <!-- Table -->
    <div class="card-enter bg-[#0e1c28] border border-teal/20 rounded-2xl overflow-hidden" style="animation-delay:.25s">

      <!-- Pending Tab -->
      <div id="content-pending">
        <table class="w-full">
          <thead>
            <tr class="border-b border-teal/20">
              <th class="text-left text-[10px] uppercase tracking-widest text-mint/30 font-semibold px-6 py-4">Company</th>
              <th class="text-left text-[10px] uppercase tracking-widest text-mint/30 font-semibold px-4 py-4">Contact</th>
              <th class="text-left text-[10px] uppercase tracking-widest text-mint/30 font-semibold px-4 py-4">Subdomain</th>
              <th class="text-left text-[10px] uppercase tracking-widest text-mint/30 font-semibold px-4 py-4">Plan</th>
              <th class="text-left text-[10px] uppercase tracking-widest text-mint/30 font-semibold px-4 py-4">Submitted</th>
              <th class="text-left text-[10px] uppercase tracking-widest text-mint/30 font-semibold px-4 py-4">Status</th>
              <th class="text-right text-[10px] uppercase tracking-widest text-mint/30 font-semibold px-6 py-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Row 1 -->
            <tr class="row-hover border-b border-teal/10 cursor-pointer" onclick="openModal('Greenfield Academy','greenfieldacademy','maria@greenfieldacademy.com','Maria Santos','pro','2 hours ago')">
              <td class="px-6 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-teal/30 flex items-center justify-center font-display font-bold text-mint text-base flex-shrink-0">G</div>
                  <div>
                    <div class="text-sm font-semibold text-mint">Greenfield Academy</div>
                    <div class="text-xs text-mint/40">maria@greenfieldacademy.com</div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-5">
                <div class="text-sm text-mint/80">Maria Santos</div>
                <div class="text-xs text-mint/40">+63 917 234 5678</div>
              </td>
              <td class="px-4 py-5">
                <code class="text-xs bg-teal/10 border border-teal/20 text-teal px-2 py-1 rounded-md">greenfieldacademy</code>
              </td>
              <td class="px-4 py-5">
                <span class="text-xs font-semibold text-[#a09aff] bg-[#a09aff]/10 border border-[#a09aff]/20 px-2.5 py-1 rounded-full">Pro</span>
              </td>
              <td class="px-4 py-5 text-sm text-mint/40">2 hours ago</td>
              <td class="px-4 py-5">
                <span class="badge-pending text-xs font-medium px-2.5 py-1 rounded-full">Pending</span>
              </td>
              <td class="px-6 py-5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button onclick="event.stopPropagation(); approveRow(this, 'Greenfield Academy')" class="btn-approve text-xs font-semibold px-3.5 py-1.5 rounded-lg flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Approve
                  </button>
                  <button onclick="event.stopPropagation(); openRejectModal('Greenfield Academy')" class="btn-reject text-xs font-semibold px-3.5 py-1.5 rounded-lg flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reject
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 2 -->
            <tr class="row-hover border-b border-teal/10 cursor-pointer" onclick="openModal('Pacific Institute of Technology','pacificiot','chen@pacificiot.edu','James Chen','basic','1 day ago')">
              <td class="px-6 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-deep flex items-center justify-center font-display font-bold text-mint text-base flex-shrink-0">P</div>
                  <div>
                    <div class="text-sm font-semibold text-mint">Pacific Institute of Technology</div>
                    <div class="text-xs text-mint/40">chen@pacificiot.edu</div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-5">
                <div class="text-sm text-mint/80">James Chen</div>
                <div class="text-xs text-mint/40">+63 912 345 6789</div>
              </td>
              <td class="px-4 py-5">
                <code class="text-xs bg-teal/10 border border-teal/20 text-teal px-2 py-1 rounded-md">pacificiot</code>
              </td>
              <td class="px-4 py-5">
                <span class="text-xs font-semibold text-mint/60 bg-mint/5 border border-mint/15 px-2.5 py-1 rounded-full">Basic</span>
              </td>
              <td class="px-4 py-5 text-sm text-mint/40">1 day ago</td>
              <td class="px-4 py-5">
                <span class="badge-pending text-xs font-medium px-2.5 py-1 rounded-full">Pending</span>
              </td>
              <td class="px-6 py-5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button onclick="event.stopPropagation(); approveRow(this, 'Pacific Institute of Technology')" class="btn-approve text-xs font-semibold px-3.5 py-1.5 rounded-lg flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Approve
                  </button>
                  <button onclick="event.stopPropagation(); openRejectModal('Pacific Institute of Technology')" class="btn-reject text-xs font-semibold px-3.5 py-1.5 rounded-lg flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reject
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 3 -->
            <tr class="row-hover cursor-pointer" onclick="openModal('Horizon University','horizonuniv','admin@horizonuniv.ph','Lea Reyes','pro','3 days ago')">
              <td class="px-6 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-teal/20 flex items-center justify-center font-display font-bold text-mint text-base flex-shrink-0">H</div>
                  <div>
                    <div class="text-sm font-semibold text-mint">Horizon University</div>
                    <div class="text-xs text-mint/40">admin@horizonuniv.ph</div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-5">
                <div class="text-sm text-mint/80">Lea Reyes</div>
                <div class="text-xs text-mint/40">+63 918 765 4321</div>
              </td>
              <td class="px-4 py-5">
                <code class="text-xs bg-teal/10 border border-teal/20 text-teal px-2 py-1 rounded-md">horizonuniv</code>
              </td>
              <td class="px-4 py-5">
                <span class="text-xs font-semibold text-[#a09aff] bg-[#a09aff]/10 border border-[#a09aff]/20 px-2.5 py-1 rounded-full">Pro</span>
              </td>
              <td class="px-4 py-5 text-sm text-mint/40">3 days ago</td>
              <td class="px-4 py-5">
                <span class="badge-pending text-xs font-medium px-2.5 py-1 rounded-full">Pending</span>
              </td>
              <td class="px-6 py-5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button onclick="event.stopPropagation(); approveRow(this, 'Horizon University')" class="btn-approve text-xs font-semibold px-3.5 py-1.5 rounded-lg flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Approve
                  </button>
                  <button onclick="event.stopPropagation(); openRejectModal('Horizon University')" class="btn-reject text-xs font-semibold px-3.5 py-1.5 rounded-lg flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reject
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-teal/20">
          <span class="text-xs text-mint/30">Showing 3 of 3 pending registrations</span>
          <div class="flex items-center gap-1.5">
            <button class="w-8 h-8 rounded-lg border border-teal/20 flex items-center justify-center text-mint/30 text-sm hover:border-teal/50 transition-colors disabled:opacity-30" disabled>
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button class="w-8 h-8 rounded-lg bg-teal text-mint text-sm font-semibold">1</button>
            <button class="w-8 h-8 rounded-lg border border-teal/20 flex items-center justify-center text-mint/30 text-sm hover:border-teal/50 transition-colors">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty state for other tabs -->
      <div id="content-approved" class="hidden py-20 text-center">
        <div class="text-4xl mb-4 opacity-30">✓</div>
        <div class="font-display text-lg font-bold text-mint mb-1">18 Approved Tenants</div>
        <div class="text-sm text-mint/40">All approved registrations this month.</div>
      </div>
      <div id="content-rejected" class="hidden py-20 text-center">
        <div class="text-4xl mb-4 opacity-30">✗</div>
        <div class="font-display text-lg font-bold text-mint mb-1">2 Rejected Applications</div>
        <div class="text-sm text-mint/40">Rejected registrations are archived here.</div>
      </div>
      <div id="content-all" class="hidden py-20 text-center">
        <div class="text-4xl mb-4 opacity-30">📋</div>
        <div class="font-display text-lg font-bold text-mint mb-1">All 23 Registrations</div>
        <div class="text-sm text-mint/40">Full history across all statuses.</div>
      </div>

    </div>
  </main>
</div>

<!-- ── Detail Modal ── -->
<div id="detail-modal" class="modal-overlay fixed inset-0 z-[100] bg-black/60 hidden items-center justify-center" onclick="closeModal()">
  <div class="modal-anim bg-[#0e1c28] border border-teal/30 rounded-2xl w-full max-w-lg mx-4 overflow-hidden" onclick="event.stopPropagation()">
    <!-- Header -->
    <div class="px-7 pt-7 pb-5 border-b border-teal/20">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-[10px] uppercase tracking-widest text-mint/30 font-semibold mb-1">Registration Details</div>
          <h2 id="modal-company" class="font-display text-2xl font-bold text-mint"></h2>
        </div>
        <button onclick="closeModal()" class="text-mint/30 hover:text-mint transition-colors mt-1">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </div>

    <!-- Body -->
    <div class="px-7 py-5 space-y-0">
      <div class="flex py-3.5 border-b border-teal/10">
        <span class="w-40 text-xs text-mint/40 font-medium uppercase tracking-wider flex-shrink-0 pt-0.5">Subdomain</span>
        <code id="modal-subdomain" class="text-sm text-teal bg-teal/10 border border-teal/20 px-2.5 py-0.5 rounded-md font-mono"></code>
      </div>
      <div class="flex py-3.5 border-b border-teal/10">
        <span class="w-40 text-xs text-mint/40 font-medium uppercase tracking-wider flex-shrink-0 pt-0.5">Email</span>
        <span id="modal-email" class="text-sm text-mint/80"></span>
      </div>
      <div class="flex py-3.5 border-b border-teal/10">
        <span class="w-40 text-xs text-mint/40 font-medium uppercase tracking-wider flex-shrink-0 pt-0.5">Contact Person</span>
        <span id="modal-contact" class="text-sm text-mint/80"></span>
      </div>
      <div class="flex py-3.5 border-b border-teal/10">
        <span class="w-40 text-xs text-mint/40 font-medium uppercase tracking-wider flex-shrink-0 pt-0.5">Plan</span>
        <span id="modal-plan" class="text-sm font-semibold text-mint/80"></span>
      </div>
      <div class="flex py-3.5">
        <span class="w-40 text-xs text-mint/40 font-medium uppercase tracking-wider flex-shrink-0 pt-0.5">Submitted</span>
        <span id="modal-submitted" class="text-sm text-mint/80"></span>
      </div>
    </div>

    <!-- Actions -->
    <div class="px-7 py-5 border-t border-teal/20 flex justify-end gap-3">
      <button onclick="closeModal()" class="text-sm font-medium text-mint/40 hover:text-mint px-4 py-2 rounded-lg border border-teal/20 hover:border-teal/40 transition-all">
        Close
      </button>
      <button onclick="closeModal(); openRejectModal(document.getElementById('modal-company').textContent)" class="btn-reject text-sm font-semibold px-5 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        Reject
      </button>
      <button onclick="closeModal(); showApproveSuccess(document.getElementById('modal-company').textContent)" class="btn-approve text-sm font-semibold px-5 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Approve Tenant
      </button>
    </div>
  </div>
</div>

<!-- ── Reject Modal ── -->
<div id="reject-modal" class="modal-overlay fixed inset-0 z-[100] bg-black/60 hidden items-center justify-center" onclick="closeRejectModal()">
  <div class="modal-anim bg-[#0e1c28] border border-[#ff4d6d]/30 rounded-2xl w-full max-w-md mx-4 overflow-hidden" onclick="event.stopPropagation()">
    <div class="px-7 pt-7 pb-5 border-b border-[#ff4d6d]/15">
      <div class="flex items-center gap-3 mb-1">
        <div class="w-8 h-8 rounded-full bg-[#ff4d6d]/15 flex items-center justify-center">
          <svg class="w-4 h-4 text-[#ff7a94]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h2 class="font-display text-xl font-bold text-mint">Reject Registration</h2>
      </div>
      <p class="text-sm text-mint/40 mt-2">Rejecting <span id="reject-company-name" class="text-mint/70 font-medium"></span>. This action will notify the applicant.</p>
    </div>

    <div class="px-7 py-5">
      <label class="block text-xs uppercase tracking-widest text-mint/40 font-semibold mb-2">Reason (optional)</label>
      <textarea id="reject-reason" rows="4" placeholder="Explain why this registration was rejected…" class="w-full bg-abyss border border-teal/20 rounded-xl text-sm text-mint placeholder-mint/20 px-4 py-3 resize-none focus:outline-none focus:border-[#ff4d6d]/50 transition-colors"></textarea>
    </div>

    <div class="px-7 pb-6 flex justify-end gap-3">
      <button onclick="closeRejectModal()" class="text-sm font-medium text-mint/40 hover:text-mint px-4 py-2 rounded-lg border border-teal/20 hover:border-teal/40 transition-all">
        Cancel
      </button>
      <button onclick="confirmReject()" class="text-sm font-semibold px-5 py-2 rounded-lg bg-[#ff4d6d]/15 text-[#ff7a94] border border-[#ff4d6d]/30 hover:bg-[#ff4d6d]/25 transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        Confirm Rejection
      </button>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="fixed bottom-6 right-6 z-[200] transition-all duration-300 opacity-0 translate-y-2 pointer-events-none">
  <div id="toast-inner" class="flex items-center gap-3 px-5 py-3.5 rounded-xl border shadow-2xl text-sm font-medium"></div>
</div>

<script>
  // Tab switching
  function switchTab(tab) {
    const tabs = ['pending', 'approved', 'rejected', 'all'];
    tabs.forEach(t => {
      document.getElementById('tab-' + t).className = t === tab ? 'tab-active px-5 py-3 text-sm font-medium transition-all' : 'tab-inactive px-5 py-3 text-sm font-medium transition-all';
      document.getElementById('content-' + t).classList.toggle('hidden', t !== tab);
    });
  }

  // Detail modal
  function openModal(company, subdomain, email, contact, plan, submitted) {
    document.getElementById('modal-company').textContent = company;
    document.getElementById('modal-subdomain').textContent = subdomain + '.yourapp.com';
    document.getElementById('modal-email').textContent = email;
    document.getElementById('modal-contact').textContent = contact;
    document.getElementById('modal-plan').textContent = plan.charAt(0).toUpperCase() + plan.slice(1);
    document.getElementById('modal-submitted').textContent = submitted;
    const m = document.getElementById('detail-modal');
    m.classList.remove('hidden');
    m.classList.add('flex');
  }

  function closeModal() {
    const m = document.getElementById('detail-modal');
    m.classList.add('hidden');
    m.classList.remove('flex');
  }

  // Reject modal
  let rejectingCompany = '';
  function openRejectModal(company) {
    rejectingCompany = company;
    document.getElementById('reject-company-name').textContent = company;
    document.getElementById('reject-reason').value = '';
    const m = document.getElementById('reject-modal');
    m.classList.remove('hidden');
    m.classList.add('flex');
  }

  function closeRejectModal() {
    const m = document.getElementById('reject-modal');
    m.classList.add('hidden');
    m.classList.remove('flex');
  }

  function confirmReject() {
    closeRejectModal();
    showToast(`"${rejectingCompany}" registration rejected.`, 'error');
  }

  // Approve
  function approveRow(btn, company) {
    showApproveSuccess(company);
  }

  function showApproveSuccess(company) {
    showToast(`"${company}" approved and provisioned successfully!`, 'success');
  }

  // Toast
  function showToast(msg, type) {
    const toast = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    if (type === 'success') {
      inner.className = 'flex items-center gap-3 px-5 py-3.5 rounded-xl border shadow-2xl text-sm font-medium bg-[#0e1c28] border-[#2dd4a0]/30 text-[#2dd4a0]';
      inner.innerHTML = `<svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>${msg}`;
    } else {
      inner.className = 'flex items-center gap-3 px-5 py-3.5 rounded-xl border shadow-2xl text-sm font-medium bg-[#0e1c28] border-[#ff4d6d]/30 text-[#ff7a94]';
      inner.innerHTML = `<svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>${msg}`;
    }
    toast.classList.remove('opacity-0', 'translate-y-2');
    toast.classList.add('opacity-100', 'translate-y-0');
    setTimeout(() => {
      toast.classList.add('opacity-0', 'translate-y-2');
      toast.classList.remove('opacity-100', 'translate-y-0');
    }, 3500);
  }
</script>
</body>
</html>