@extends('admin.layout')

@section('title', 'Audit & Security Logs')
@section('page-title', 'Tamper-Evident Security & Audit Ledger')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header Description -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-white">Immutable Administrative Audit Trail</h3>
            <p class="text-xs text-slate-400 mt-0.5">NIST SP 800-53 compliant access and document modification logs</p>
        </div>

        <form action="{{ route('admin.audit') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <select name="action" class="bg-slate-950 border border-slate-700 text-xs text-slate-300 rounded-xl px-3 py-1.5 focus:outline-none focus:border-emerald-500">
                <option value="">All Actions</option>
                <option value="upload_and_sync" {{ request('action') === 'upload_and_sync' ? 'selected' : '' }}>Upload & Sync</option>
                <option value="delete_document" {{ request('action') === 'delete_document' ? 'selected' : '' }}>Delete Document</option>
                <option value="manual_model_sync" {{ request('action') === 'manual_model_sync' ? 'selected' : '' }}>Manual Model Sync</option>
                <option value="admin_login" {{ request('action') === 'admin_login' ? 'selected' : '' }}>Admin Login</option>
                <option value="admin_logout" {{ request('action') === 'admin_logout' ? 'selected' : '' }}>Admin Logout</option>
            </select>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search email, IP, target..."
                   class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs px-3 py-1.5 rounded-xl border border-slate-700 transition-all">Filter</button>
            @if(request('action') || request('search'))
                <a href="{{ route('admin.audit') }}" class="text-xs text-slate-400 hover:text-white">Clear</a>
            @endif
        </form>
    </div>

    <!-- Audit Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 font-semibold pb-3">
                        <th class="pb-3 pl-2">Timestamp (UTC)</th>
                        <th class="pb-3">Action</th>
                        <th class="pb-3">Administrator</th>
                        <th class="pb-3">Target Resource</th>
                        <th class="pb-3">Client IP</th>
                        <th class="pb-3 text-right pr-2">Metadata Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono text-[11px]">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-slate-800/30 transition-all">
                            <td class="py-3 pl-2 text-slate-400">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="py-3">
                                @if(str_contains($log->action, 'upload') || str_contains($log->action, 'sync'))
                                    <span class="bg-emerald-950/60 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/30 font-semibold">
                                        {{ $log->action }}
                                    </span>
                                @elseif(str_contains($log->action, 'delete'))
                                    <span class="bg-rose-950/60 text-rose-400 px-2 py-0.5 rounded border border-rose-500/30 font-semibold">
                                        {{ $log->action }}
                                    </span>
                                @elseif(str_contains($log->action, 'login'))
                                    <span class="bg-teal-950/60 text-teal-400 px-2 py-0.5 rounded border border-teal-500/30 font-semibold">
                                        {{ $log->action }}
                                    </span>
                                @else
                                    <span class="bg-slate-800 text-slate-300 px-2 py-0.5 rounded border border-slate-700/60">
                                        {{ $log->action }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-slate-300 font-sans font-medium">{{ $log->user_email ?: 'System' }}</td>
                            <td class="py-3 text-slate-300">
                                <span class="bg-slate-800 px-1.5 py-0.5 rounded text-slate-300 border border-slate-700/60">
                                    {{ $log->target_type ? "{$log->target_type}:" : '' }}{{ $log->target_id ?: 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-400">{{ $log->ip_address ?: '127.0.0.1' }}</td>
                            <td class="py-3 text-right pr-2 text-slate-500 truncate max-w-xs" title="{{ json_encode($log->details) }}">
                                {{ $log->details ? json_encode($log->details) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-sans">
                                No audit records found matching your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 font-sans">
            {{ $auditLogs->links() }}
        </div>
    </div>
</div>
@endsection
