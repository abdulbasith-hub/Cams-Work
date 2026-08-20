@extends('index2')
@section('content')
    @php
        $dataVerificationRouteSource = $dataVerificationFilters ?? [
            'deptcode' => $deptcode ?? '',
            'regioncode' => $regioncode ?? '',
            'distcode' => $distcode ?? '',
            'quartercode' => $quartercode ?? '',
            'planmappingid' => $planmappingid ?? '',
            'prioritycode' => $prioritycode ?? 'null',
        ];
        $dataVerificationRouteParams = [
            'deptcode' => $dataVerificationRouteSource['deptcode'] ?? '',
            'regioncode' => $dataVerificationRouteSource['regioncode'] ?? '',
            'distcode' => $dataVerificationRouteSource['distcode'] ?? '',
            'quartercode' => $dataVerificationRouteSource['quartercode'] ?? '',
            'planmappingid' => $dataVerificationRouteSource['planmappingid'] ?? '',
            'prioritycode' => ($dataVerificationRouteSource['prioritycode'] ?? '') !== ''
                ? $dataVerificationRouteSource['prioritycode']
                : 'null',
        ];

    @endphp

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-auto">
                <a href="{{ route('data.verification', $dataVerificationRouteParams) }}"
                    class="btn btn-primary px-4">
                    Verification
                </a>
            </div>
        </div>
    </div>
@endsection
