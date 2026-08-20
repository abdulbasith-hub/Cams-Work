@extends('index2')

@section('title', ($item ? 'Edit' : 'New') . ' content · DGA Portal')

@section('content')
    @php
        $existingContent = $item->content ?? [];
        $isSingleton = $config['singleton'] ?? false;

        // On a fresh singleton (no draft/pending/approved row exists yet), start
        // from what's currently live on the public page instead of a blank form.
        $prefill = ($item === null && $isSingleton && is_array($referenceContent))
            ? $referenceContent
            : $existingContent;

        if ($sectionKey === 'gallery') {
            $prefill['alt'] = $existingContent['alt'] ?? '';
        }
        if ($sectionKey === 'downloads') {
            $prefill['link'] = ($existingContent['source'] ?? 'link') === 'link' ? ($existingContent['href'] ?? '') : '';
        }
        if ($sectionKey === 'regions') {
            $prefill['places'] = implode(', ', $prefill['places'] ?? []);
        }
        $structureMapData = [];
        if ($sectionKey === 'structure_map') {
            $structureMapData = $prefill ?: ($existingContent ?: []);
            $prefill['json'] = json_encode($structureMapData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        $resolveAsset = function ($path) {
            if (empty($path)) {
                return '';
            }
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
                return $path;
            }
            return asset($path);
        };
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="page-title mb-0">{{ $item ? 'Edit' : 'New' }} content — {{ $config['label'] }}</h1>
        <a href="{{ route('portal.content.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    @if ($item && $item->status === 'rejected' && $item->remarks)
        <div class="alert alert-danger">Rejected: {{ $item->remarks }}</div>
    @endif
    @if ($item && $item->status === 'approved')
        <div class="alert alert-info">This item is live. Saving will create a new draft copy that replaces it once approved, so the public site keeps showing the current version until then.</div>
    @endif

    @if (!$isSingleton && !empty($referenceContent))
        <div class="card mb-3">
            <div class="card-header">Currently on the site (reference — for context only, not editable here)</div>
            <div class="card-body">
                <pre class="mb-0" style="white-space:pre-wrap;max-height:220px;overflow:auto;">{{ json_encode($referenceContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    @endif

    <form id="portalContentForm" enctype="multipart/form-data">
        <input type="hidden" name="page_key" value="{{ $pageKey }}">
        <input type="hidden" name="page_scope" value="{{ $pageScope }}">
        <input type="hidden" name="section_key" value="{{ $sectionKey }}">

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Internal title (for your reference in the list, not shown publicly)</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $item->title ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Display order</label>
                        <input type="number" name="display_order" class="form-control" value="{{ old('display_order', $item->display_order ?? 0) }}">
                    </div>
                </div>

                @foreach ($config['fields'] as $key => $field)
                    <div class="mb-3" id="field-group-{{ $key }}">
                        <label class="form-label">{{ $field['label'] }}</label>

                        @if ($field['type'] === 'text')
                            <input type="text" name="{{ $key }}" id="field-{{ $key }}" class="form-control" value="{{ old($key, $prefill[$key] ?? '') }}">
                        @elseif ($field['type'] === 'date')
                            <input type="date" name="{{ $key }}" id="field-{{ $key }}" class="form-control" value="{{ old($key, $prefill[$key] ?? '') }}">
                        @elseif ($field['type'] === 'select')
                            <select name="{{ $key }}" id="field-{{ $key }}" class="form-select">
                                @foreach ($field['options'] as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" @selected(old($key, $prefill[$key] ?? '') === $optionValue)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        @elseif ($field['type'] === 'parent_select')
                            <select name="{{ $key }}" id="field-{{ $key }}" class="form-select">
                                <option value="">(top level)</option>
                                @foreach ($siblingItems ?? [] as $sibling)
                                    <option value="{{ $sibling->id }}" @selected((string) old($key, $prefill[$key] ?? '') === (string) $sibling->id)>{{ $sibling->content['label'] ?? ('#' . $sibling->id) }}</option>
                                @endforeach
                            </select>
                        @elseif ($field['type'] === 'target_select')
                            <select name="{{ $key }}" id="field-{{ $key }}" class="form-select">
                                @foreach ($navTargetOptions ?? [] as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" @selected(old($key, $prefill[$key] ?? '') === $optionValue)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        @elseif ($field['type'] === 'textarea')
                            <textarea name="{{ $key }}" id="field-{{ $key }}" class="form-control" rows="4">{{ old($key, $prefill[$key] ?? '') }}</textarea>
                        @elseif ($field['type'] === 'json' && $sectionKey === 'structure_map')
                            <textarea name="{{ $key }}" id="field-{{ $key }}" class="d-none">{{ old($key, $prefill[$key] ?? '') }}</textarea>

                            <style>
                                .smflow { display: flex; flex-direction: column; align-items: center; }
                                .smflow-box { position: relative; border: 2px solid #93c5fd; border-radius: 12px; background: #eff6ff; padding: .6rem 2rem .6rem .9rem; width: 100%; max-width: 320px; margin: 0 auto; }
                                .smflow-box.smflow-root { background: #fef3c7; border-color: #f59e0b; }
                                .smflow-box.smflow-lead { background: linear-gradient(135deg, #0b5fa5, #027c86); border-color: #0b5fa5; }
                                .smflow-box.smflow-lead .smflow-title-input, .smflow-box.smflow-lead .smflow-detail-input { color: #fff; }
                                .smflow-box.smflow-lead .smflow-title-input::placeholder, .smflow-box.smflow-lead .smflow-detail-input::placeholder { color: rgba(255,255,255,.7); }
                                .smflow-title-input { display: block; width: 100%; border: none; background: transparent; font-weight: 700; text-align: center; padding: 0; }
                                .smflow-detail-input { display: block; width: 100%; border: none; background: transparent; text-align: center; font-size: .78rem; color: #475569; resize: vertical; padding: 0; margin-top: .2rem; }
                                .smflow-title-input:focus, .smflow-detail-input:focus { outline: 1px dashed rgba(11,95,165,.6); }
                                .smflow-arrow { color: #94a3b8; font-size: 1.3rem; line-height: 1; margin: .15rem 0; text-align: center; }
                                .smflow-branches { display: flex; gap: 2rem; align-items: flex-start; justify-content: center; flex-wrap: wrap; width: 100%; margin-top: .1rem; }
                                .smflow-branch { display: flex; flex-direction: column; align-items: center; flex: 1; min-width: 260px; }
                                .smflow-branch-label { font-weight: 600; font-size: .85rem; color: #0b5fa5; margin-bottom: .4rem; }
                                .smflow-branch .sm-node:not(:last-of-type)::after,
                                .smflow-common .sm-node:not(:last-of-type)::after { content: '↓'; display: block; text-align: center; color: #94a3b8; font-size: 1.1rem; margin: .15rem 0; }
                                .smflow-common { display: flex; flex-direction: column; align-items: center; width: 100%; margin-top: .1rem; }
                                .smflow-remove { position: absolute; top: 2px; right: 4px; border: none; background: transparent; color: #ef4444; font-weight: 700; cursor: pointer; line-height: 1; font-size: 1.1rem; padding: .2rem .4rem; }
                                .smflow-add-btn { margin-top: .5rem; }
                                .sm-people-list { display: none; }
                            </style>

                            <div id="smEditor" class="smflow">
                                <div class="smflow-box smflow-root">
                                    <input type="text" class="smflow-title-input" id="smRootTitle" placeholder="Root title">
                                    <textarea class="smflow-detail-input" id="smRootDetail" placeholder="Detail" rows="1"></textarea>
                                </div>
                                <div class="smflow-arrow">↓</div>

                                <div class="smflow-box smflow-lead sm-people-card">
                                    <div class="smflow-box-body">
                                        <div class="sm-node-fields">
                                            <input type="text" class="smflow-title-input sm-f" data-f="title" placeholder="Lead title">
                                            <textarea class="smflow-detail-input sm-f" data-f="detail" placeholder="Detail" rows="1"></textarea>
                                        </div>
                                        <div class="sm-people-list"></div>
                                    </div>
                                </div>
                                <div class="smflow-arrow">↓</div>

                                <div class="smflow-branches">
                                    <div class="smflow-branch">
                                        <div class="smflow-branch-label">Left branch</div>
                                        <div id="smLeftNodes" class="w-100"></div>
                                        <button type="button" class="btn btn-sm btn-primary smflow-add-btn sm-add-node" data-target="smLeftNodes" data-people="1">+ Add to left</button>
                                    </div>
                                    <div class="smflow-branch">
                                        <div class="smflow-branch-label">Right branch</div>
                                        <div id="smRightNodes" class="w-100"></div>
                                        <button type="button" class="btn btn-sm btn-primary smflow-add-btn sm-add-node" data-target="smRightNodes" data-people="1">+ Add to right</button>
                                    </div>
                                </div>
                                <div class="smflow-arrow">↓</div>

                                <div class="smflow-branch-label">Common — bottom row</div>
                                <div id="smCommonNodes" class="smflow-common"></div>
                                <button type="button" class="btn btn-sm btn-primary smflow-add-btn sm-add-node" data-target="smCommonNodes" data-people="0">+ Add to common</button>
                            </div>
                        @elseif ($field['type'] === 'json')
                            <textarea name="{{ $key }}" id="field-{{ $key }}" class="form-control" rows="16" style="font-family:monospace;font-size:.85rem;">{{ old($key, $prefill[$key] ?? '') }}</textarea>
                        @elseif ($field['type'] === 'file')
                            <input type="file" name="{{ $key }}" id="field-{{ $key }}" class="form-control">
                            @if ($sectionKey === 'gallery' && $key === 'image' && !empty($existingContent['src']))
                                <div class="mt-2"><img src="{{ $resolveAsset($existingContent['src']) }}" style="max-width:140px;border-radius:8px" alt=""></div>
                            @endif
                            @if ($sectionKey === 'downloads' && $key === 'file' && ($existingContent['source'] ?? '') === 'upload' && !empty($existingContent['href']))
                                <small class="text-muted d-block mt-1">Current file: <a href="{{ $resolveAsset($existingContent['href']) }}" target="_blank">view</a></small>
                            @endif
                        @endif

                        @if (!empty($field['options']) && $field['type'] !== 'select')
                            <small class="text-muted">{{ implode(', ', array_keys($field['options'])) }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save as draft</button>
            </div>
        </div>
    </form>

    <script>
        const smInitial = @json($structureMapData ?? []);

        function smEsc(v) {
            return String(v || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function smPersonHtml(p) {
            p = p || {};
            return '<div class="border rounded p-2 mb-2 sm-person">' +
                '<div class="mb-1">' +
                '<span class="small text-muted">Person (not editable)</span>' +
                '</div>' +
                '<div class="row g-1">' +
                '<div class="col-md-2"><input class="form-control form-control-sm sm-f" data-f="sl" placeholder="Sl No" value="' + smEsc(p.sl) + '" readonly></div>' +
                '<div class="col-md-5"><input class="form-control form-control-sm sm-f" data-f="name" placeholder="Name" value="' + smEsc(p.name) + '" readonly></div>' +
                '<div class="col-md-5"><input class="form-control form-control-sm sm-f" data-f="designation" placeholder="Designation" value="' + smEsc(p.designation) + '" readonly></div>' +
                '</div>' +
                '<div class="row g-1 mt-1">' +
                '<div class="col-md-6"><input class="form-control form-control-sm sm-f" data-f="phone" placeholder="Phone" value="' + smEsc(p.phone) + '" readonly></div>' +
                '<div class="col-md-6"><input class="form-control form-control-sm sm-f" data-f="email" placeholder="Email" value="' + smEsc(p.email) + '" readonly></div>' +
                '</div>' +
                '<div class="row g-1 mt-1">' +
                '<div class="col-md-12"><input class="form-control form-control-sm sm-f" data-f="address" placeholder="Address" value="' + smEsc(p.address) + '" readonly></div>' +
                '</div>' +
                '</div>';
        }

        function smNodeHtml(withPeople) {
            let html = '<div class="smflow-box sm-node" data-people="' + (withPeople ? 1 : 0) + '">' +
                '<button type="button" class="smflow-remove sm-remove-node" title="Remove">&times;</button>' +
                '<div class="smflow-box-body">' +
                '<div class="sm-node-fields">' +
                '<input type="text" class="smflow-title-input sm-f" data-f="title" placeholder="Title">' +
                '<textarea class="smflow-detail-input sm-f" data-f="detail" placeholder="Detail" rows="1"></textarea>' +
                '</div>';
            if (withPeople) {
                html += '<div class="sm-people-list"></div>';
            }
            html += '</div></div>';
            return html;
        }

        function smFillNodeFields($node, node) {
            $node.children('.smflow-box-body').children('.sm-node-fields').find('.sm-f').each(function () {
                $(this).val(node[$(this).data('f')] || '');
            });
        }

        function smAddPerson($list, person) {
            $list.append(smPersonHtml(person));
        }

        function smAddNode($container, node, withPeople) {
            node = node || {};
            const $node = $(smNodeHtml(withPeople));
            $container.append($node);
            smFillNodeFields($node, node);
            if (withPeople && Array.isArray(node.people)) {
                const $list = $node.children('.smflow-box-body').children('.sm-people-list');
                node.people.forEach(function (p) { smAddPerson($list, p); });
            }
        }

        function smCollectPerson($el) {
            const p = {};
            $el.find('.sm-f').each(function () {
                const v = $(this).val();
                if (v) p[$(this).data('f')] = v;
            });
            return p;
        }

        function smCollectNode($node) {
            const node = {};
            $node.children('.smflow-box-body').children('.sm-node-fields').find('.sm-f').each(function () {
                node[$(this).data('f')] = $(this).val();
            });
            if (Number($node.data('people')) === 1) {
                const people = [];
                $node.children('.smflow-box-body').children('.sm-people-list').children('.sm-person').each(function () {
                    people.push(smCollectPerson($(this)));
                });
                if (people.length) node.people = people;
            }
            return node;
        }

        function smPopulate() {
            $('#smRootTitle').val(smInitial.root ? smInitial.root.title : '');
            $('#smRootDetail').val(smInitial.root ? smInitial.root.detail : '');

            const $leadCard = $('.sm-people-card');
            smFillNodeFields($leadCard, smInitial.lead || {});
            if (smInitial.lead && Array.isArray(smInitial.lead.people)) {
                const $list = $leadCard.children('.smflow-box-body').children('.sm-people-list');
                smInitial.lead.people.forEach(function (p) { smAddPerson($list, p); });
            }

            (smInitial.left || []).forEach(function (n) { smAddNode($('#smLeftNodes'), n, true); });
            (smInitial.right || []).forEach(function (n) { smAddNode($('#smRightNodes'), n, true); });
            (smInitial.common || []).forEach(function (n) { smAddNode($('#smCommonNodes'), n, false); });
        }

        function smCollect() {
            const leftNodes = [], rightNodes = [], commonNodes = [];
            $('#smLeftNodes > .sm-node').each(function () { leftNodes.push(smCollectNode($(this))); });
            $('#smRightNodes > .sm-node').each(function () { rightNodes.push(smCollectNode($(this))); });
            $('#smCommonNodes > .sm-node').each(function () { commonNodes.push(smCollectNode($(this))); });

            const $leadCard = $('.sm-people-card');
            const lead = {};
            $leadCard.children('.smflow-box-body').children('.sm-node-fields').find('.sm-f').each(function () {
                lead[$(this).data('f')] = $(this).val();
            });
            const leadPeople = [];
            $leadCard.children('.smflow-box-body').children('.sm-people-list').children('.sm-person').each(function () {
                leadPeople.push(smCollectPerson($(this)));
            });
            if (leadPeople.length) lead.people = leadPeople;

            return {
                root: { title: $('#smRootTitle').val(), detail: $('#smRootDetail').val() },
                lead: lead,
                left: leftNodes,
                right: rightNodes,
                common: commonNodes,
            };
        }

        $(document).on('click', '.sm-add-node', function () {
            const target = $(this).data('target');
            const withPeople = Number($(this).data('people')) === 1;
            smAddNode($('#' + target), {}, withPeople);
        });
        $(document).on('click', '.sm-remove-node', function () {
            if (confirm('Remove this node?')) $(this).closest('.sm-node').remove();
        });
        $(document).on('click', '.sm-add-person', function () {
            const $list = $(this).closest('.smflow-box-body').children('.sm-people-list');
            smAddPerson($list, {});
        });
        $(document).on('click', '.sm-remove-person', function () {
            $(this).closest('.sm-person').remove();
        });

        function portalToggleDownloadSource() {
            const source = $('#field-source').val();
            $('#field-group-file').toggle(source === 'upload');
            $('#field-group-link').toggle(source === 'link');
        }

        $(function () {
            if ($('#smEditor').length) {
                smPopulate();
            }

            if ($('#field-source').length) {
                portalToggleDownloadSource();
                $('#field-source').on('change', portalToggleDownloadSource);
            }

            $('#portalContentForm').on('submit', function (event) {
                event.preventDefault();

                if ($('#smEditor').length) {
                    const data = smCollect();
                    if (!data.root.title || !data.root.detail) {
                        alert('Root needs a title and detail.');
                        return;
                    }
                    if (!data.lead.title || !data.lead.detail) {
                        alert('Lead needs a title and detail.');
                        return;
                    }
                    if (!data.left.length) {
                        alert('Add at least one node to the Left branch.');
                        return;
                    }
                    for (const branch of ['left', 'right', 'common']) {
                        for (const node of data[branch]) {
                            if (!node.title || !node.detail) {
                                alert('Every node in "' + branch + '" needs a title and detail.');
                                return;
                            }
                        }
                    }
                    $('#field-json').val(JSON.stringify(data));
                } else if ($('#field-json').length) {
                    try {
                        JSON.parse($('#field-json').val());
                    } catch (e) {
                        alert('The structure map JSON is not valid: ' + e.message);
                        return;
                    }
                }

                const formData = new FormData(this);
                formData.append('_token', '{{ csrf_token() }}');

                const url = {!! $item ? "'" . route('portal.content.update', $item->id) . "'" : "'" . route('portal.content.store') . "'" !!};

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if (res.success) {
                            alert(res.message);
                            window.location.href = res.redirect_url || "{{ route('portal.content.index') }}";
                        } else {
                            alert(res.message || 'Please check the form.');
                        }
                    },
                    error: function (xhr) {
                        const res = xhr.responseJSON || {};
                        let message = res.message || 'Failed to save.';
                        if (res.errors) {
                            message += '\n' + Object.values(res.errors).flat().join('\n');
                        }
                        alert(message);
                    }
                });
            });
        });
    </script>
@endsection
