@extends('layouts.app')

@push('styles')
    <style>
        /* Admin specific layout styling scoped under .admin-page-wrapper to avoid touching navbar/footer */
        .admin-page-wrapper {
            background: #ffffff;
            color: #111827; /* dark text for admin pages */
            min-height: calc(100vh - 120px);
            padding: 2rem 0;
        }

        .admin-page-wrapper .card {
            background: #ffffff;
            color: #111827;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 8px 24px rgba(2,6,23,0.04);
        }

        /* make specific admin icons/avatars a primary/blue color */
        .admin-page-wrapper .icon.icon-shape.bg-primary {
            background: var(--primary) !important;
            color: #fff !important;
        }

        /* Detail buttons should have dark text/icon on admin pages */
        .admin-page-wrapper .btn-detail,
        .admin-page-wrapper .btn-detail i,
        .admin-page-wrapper .btn-detail .bi,
        .admin-page-wrapper .btn-detail .fas {
            color: #111827 !important;
            fill: #111827 !important;
        }

        /* Make actions inside admin content show dark color while leaving navbar/footer intact */
        .admin-page-wrapper .btn,
        .admin-page-wrapper .card .btn,
        .admin-page-wrapper .table .btn,
        .admin-page-wrapper .btn-group .btn {
            color: #111827 !important;
            fill: #111827 !important;
        }

        /* Recent applicant icons (scoped) */
        .admin-page-wrapper .recent-applications-table .fa,
        .admin-page-wrapper .recent-applications-table .fas,
        .admin-page-wrapper .recent-applications-table .bi {
            color: #111827 !important;
            fill: #111827 !important;
        }

        /* make site footer icons white when on admin pages */
        .site-footer .bi,
        .site-footer i[class^="bi-"],
        .site-footer i[class*=" bi-"],
        .site-footer .fas,
        .site-footer .fa {
            color: #ffffff !important;
            fill: #ffffff !important;
        }
    </style>
@endpush

@section('content')
    <div class="admin-page-wrapper container-fluid px-4">
        @yield('admin-content')
    </div>
@endsection
