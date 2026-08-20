@extends('index2')

@section('title', 'Helpdesk Category Master')

@section('content')
<style>
    .fh-card {
        border: 1px solid #d8e2f0;
        border-radius: 0;
        background: #fff;
        overflow: hidden;
    }

    .fh-card-header {
        background: #5b7df0;
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        padding: 8px 20px;
    }

    .fh-body {
        padding: 18px 20px;
    }

    .fh-sql {
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 6px;
        padding: 14px;
        white-space: pre-wrap;
        font-size: 13px;
    }
</style>

<div class="fh-card">
    <div class="fh-card-header">Helpdesk Module Category Master</div>
    <div class="fh-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
            <div>
                <strong>Category Insert</strong>
                <div class="text-muted small">Categories inserted here will appear in the create ticket category dropdown.</div>
            </div>
            <a href="{{ route('fresh-helpdesk.create') }}" class="btn btn-outline-primary">
                <i class="ti ti-ticket me-1"></i> Create Ticket
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        @unless ($tableExists)
            <div class="alert alert-warning">
                Category table is not available yet. Submit one category here and the table will be created automatically.
            </div>
        @endunless

        <form method="POST" action="{{ route('fresh-helpdesk.category-master.store') }}" class="row g-3 align-items-end mb-4">
            @csrf
            <div class="col-md-8">
                <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="category_name" class="form-control" maxlength="100" value="{{ old('category_name') }}" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-plus me-1"></i> Insert Category
                </button>
            </div>
        </form>

        <div class="table-responsive mb-4">
            <table class="table table-striped table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Category Name</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Created On</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->category_name }}</td>
                            <td>{{ $category->statusflag === 'Y' ? 'Active' : 'Inactive' }}</td>
                            <td>{{ $category->createdby ?: '-' }}</td>
                            <td>{{ $category->createdon ? \Carbon\Carbon::parse($category->createdon)->format('d/m/Y h:i A') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No categories inserted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <h6 class="fw-bold">Create Table Query</h6>
                <pre class="fh-sql">{{ $createTableSql }}</pre>
            </div>
            <div class="col-lg-6">
                <h6 class="fw-bold">Model Insert Query</h6>
                <pre class="fh-sql">{{ $insertSql }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection
