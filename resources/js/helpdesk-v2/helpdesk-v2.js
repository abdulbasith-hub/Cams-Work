document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-hdv2-submit-once]').forEach(function (button) {
        var form = button.closest('form');

        if (!form) {
            return;
        }

        form.addEventListener('submit', function () {
            button.disabled = true;
            button.textContent = 'Submitting...';
        });
    });

    function escapeHtml(value) {
        var div = document.createElement('div');
        div.textContent = value || '';

        return div.innerHTML;
    }

    function continueConfirmedAction(button) {
        var form = button.closest('form');
        var href = button.getAttribute('href');

        if (form) {
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit(button);
                return;
            }

            form.submit();
            return;
        }

        if (href) {
            window.location.href = href;
        }
    }

    function showCommonConfirm(message, onConfirm) {
        var title = 'Confirmation';
        var safeMessage = escapeHtml(message || 'Continue?');
        var processButton = document.getElementById('process_button');
        var cancelButton = document.getElementById('cancel_button');

        if (typeof passing_alert_value === 'function') {
            passing_alert_value(title, safeMessage, 'confirmation_alert', 'confirmation_alertmodal', 'alert_body', 'forward_alert');
        } else {
            var modalElement = document.getElementById('confirmation_alert');
            var titleElement = document.getElementById('confirmation_alertmodal');
            var bodyElement = document.getElementById('alert_body');

            if (!modalElement || !titleElement || !bodyElement || typeof bootstrap === 'undefined') {
                return;
            }

            titleElement.innerHTML = title;
            bodyElement.innerHTML = safeMessage;
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }

        if (!processButton) {
            return;
        }

        processButton.innerHTML = '<span class="lang" key="ok">OK</span>';

        if (cancelButton) {
            cancelButton.innerHTML = '<span class="lang" key="cancel">Cancel</span>';
        }

        processButton.onclick = function () {
            processButton.onclick = null;
            onConfirm();
        };
    }

    document.querySelectorAll('[data-hdv2-confirm]').forEach(function (button) {
        button.addEventListener('click', function (event) {
            var form = button.closest('form');
            var isSubmitButton = form && (button.type || '').toLowerCase() === 'submit';

            if (isSubmitButton && typeof form.checkValidity === 'function' && !form.checkValidity()) {
                return;
            }

            event.preventDefault();
            showCommonConfirm(button.getAttribute('data-hdv2-confirm') || 'Continue?', function () {
                continueConfirmedAction(button);
            });
        });
    });

    document.querySelectorAll('[data-hdv2-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var target = document.getElementById(button.getAttribute('data-hdv2-toggle'));

            if (!target) {
                return;
            }

            target.hidden = !target.hidden;
        });
    });

    function hdv2RowMatchesFilter(row, currentFilter) {
        var status = row ? row.getAttribute('data-hdv2-status') || '' : '';
        var taskStatus = row ? row.getAttribute('data-hdv2-task-status') || '' : '';
        var priority = row ? row.getAttribute('data-hdv2-priority') || '' : '';
        var pending = row ? row.getAttribute('data-hdv2-pending') || 'N' : 'N';
        var active = row ? row.getAttribute('data-hdv2-active') || 'N' : 'N';
        var resolved = row ? row.getAttribute('data-hdv2-resolved') || 'N' : 'N';
        var closed = row ? row.getAttribute('data-hdv2-closed') || 'N' : 'N';
        var reopened = row ? row.getAttribute('data-hdv2-reopened') || 'N' : 'N';
        var returned = row ? row.getAttribute('data-hdv2-returned') || 'N' : 'N';
        var isUrgent = priority === 'critical' || priority === 'urgent';

        if (taskStatus) {
            if (currentFilter === 'total') {
                return row.getAttribute('data-hdv2-task-total') !== 'N';
            }

            if (currentFilter === 'in_progress') {
                return row.getAttribute('data-hdv2-task-in-progress') === 'Y';
            }

            if (currentFilter === 'pending') {
                return row.getAttribute('data-hdv2-task-pending') === 'Y';
            }

            if (currentFilter === 'overdue') {
                return row.getAttribute('data-hdv2-task-overdue') === 'Y';
            }

            if (currentFilter === 'completed') {
                return row.getAttribute('data-hdv2-task-completed') === 'Y';
            }

            return currentFilter === taskStatus;
        }

        if (currentFilter === 'pending') {
            return pending === 'Y';
        }

        if (currentFilter === 'critical') {
            return isUrgent;
        }

        if (currentFilter === 'in_progress') {
            return active === 'Y' && returned !== 'Y' && !isUrgent;
        }

        if (currentFilter === 'urgent') {
            return active === 'Y' && returned !== 'Y' && isUrgent;
        }

        if (currentFilter === 'returned') {
            return active === 'Y' && returned === 'Y';
        }

        if (currentFilter === 'resolved') {
            return resolved === 'Y' || status === 'resolved';
        }

        if (currentFilter === 'resolved_closed') {
            return resolved === 'Y' || status === 'resolved' || closed === 'Y' || status === 'closed';
        }

        if (currentFilter.indexOf('priority:') === 0) {
            return priority === currentFilter.substring(9);
        }

        if (currentFilter.indexOf('status:') === 0) {
            return status === currentFilter.substring(7);
        }

        if (currentFilter === 'closed') {
            return closed === 'Y' || status === 'closed';
        }

        if (currentFilter === 'reopened') {
            return reopened === 'Y';
        }

        return true;
    }

    function hasDataTables() {
        return Boolean(window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable);
    }

    function refreshDashboardDataTables(root) {
        if (!hasDataTables()) {
            return;
        }

        (root || document).querySelectorAll('[data-hdv2-grid][data-hdv2-datatable] table').forEach(function (table) {
            if (window.jQuery.fn.DataTable.isDataTable(table)) {
                window.jQuery(table).DataTable().columns.adjust().draw(false);
            }
        });
    }

    document.querySelectorAll('[data-hdv2-dashboard-pane-switch]').forEach(function (button) {
        button.addEventListener('click', function () {
            var root = button.closest('.hdv2');
            var paneName = button.getAttribute('data-hdv2-dashboard-pane-switch') || 'tickets';

            if (!root) {
                return;
            }

            root.querySelectorAll('[data-hdv2-dashboard-pane-switch]').forEach(function (item) {
                var isActive = item === button;
                item.classList.toggle('is-active', isActive);
                item.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            root.querySelectorAll('[data-hdv2-dashboard-pane]').forEach(function (pane) {
                var isActive = pane.getAttribute('data-hdv2-dashboard-pane') === paneName;
                pane.classList.toggle('is-active', isActive);
                pane.hidden = !isActive;
            });

            refreshDashboardDataTables(root);
        });
    });

    document.querySelectorAll('[data-hdv2-exclusive-assign]').forEach(function (form) {
        var additionalLayer = form.querySelector('[data-hdv2-additional-layer]');
        var developer = form.querySelector('[data-hdv2-developer]');

        if (!additionalLayer || !developer) {
            return;
        }

        if (additionalLayer.disabled && developer.disabled) {
            return;
        }

        function syncAssignmentSelects() {
            var hasAdditionalLayer = additionalLayer.value !== '';
            var hasDeveloper = developer.value !== '';

            developer.disabled = hasAdditionalLayer;
            additionalLayer.disabled = hasDeveloper;
        }

        additionalLayer.addEventListener('change', syncAssignmentSelects);
        developer.addEventListener('change', syncAssignmentSelects);
        syncAssignmentSelects();
    });

    if (hasDataTables() && !window.hdv2DataTableFilterRegistered) {
        window.jQuery.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            var table = settings.nTable;
            var grid = table ? table.closest('[data-hdv2-grid][data-hdv2-datatable]') : null;
            var row = settings.aoData[dataIndex] ? settings.aoData[dataIndex].nTr : null;

            if (!grid) {
                return true;
            }

            if (grid.hasAttribute('data-hdv2-ajax-grid')) {
                return true;
            }

            return hdv2RowMatchesFilter(row, grid.getAttribute('data-hdv2-current-filter') || 'total');
        });

        window.hdv2DataTableFilterRegistered = true;
    }

    document.querySelectorAll('[data-hdv2-grid][data-hdv2-datatable]').forEach(function (grid) {
        if (!hasDataTables()) {
            return;
        }

        var gridName = grid.getAttribute('data-hdv2-grid-name') || 'dashboard';
        var table = grid.querySelector('table');
        var root = grid.closest('.hdv2');
        var filterLabel = grid.querySelector('[data-hdv2-filter-label]');
        function getFilterButtons() {
            return Array.prototype.slice.call(root ? root.querySelectorAll('[data-hdv2-dashboard-filter]') : []).filter(function (button) {
                return (button.getAttribute('data-hdv2-filter-target') || 'dashboard') === gridName;
            });
        }

        var filterButtons = getFilterButtons();
        var activeFilterButton = filterButtons.filter(function (button) {
            return button.classList.contains('is-active');
        })[0];
        var title = filterLabel ? filterLabel.textContent : 'Dashboard';
        var dataTableOptions;
        var dataTable;
        var ajaxUrl = grid.getAttribute('data-hdv2-ajax-url') || '';
        var ajaxType = grid.getAttribute('data-hdv2-ajax-type') || 'tickets';
        var ajaxTypeToken = grid.getAttribute('data-hdv2-ajax-type-token') || '';
        var columnDefs = [
            { targets: 'no-sort', orderable: false },
            { targets: 'no-export', searchable: false }
        ];

        if (!table || window.jQuery.fn.DataTable.isDataTable(table)) {
            return;
        }

        function applyWebsitePaginationStyle() {
            var wrapper = grid.querySelector('.dataTables_wrapper');
            var paginate;

            if (!wrapper) {
                return;
            }

            paginate = wrapper.querySelector('.dataTables_paginate');
            if (!paginate) {
                return;
            }

            paginate.classList.add('hdv2-site-pagination');
            paginate.querySelectorAll('.paginate_button').forEach(function (button) {
                button.setAttribute('role', 'button');
            });
        }

        grid.setAttribute('data-hdv2-current-filter', activeFilterButton
            ? activeFilterButton.getAttribute('data-hdv2-dashboard-filter') || 'total'
            : 'total');

        if (filterLabel && activeFilterButton) {
            filterLabel.textContent = activeFilterButton.getAttribute('data-hdv2-filter-title') || title;
        }

        if (ajaxType === 'tickets') {
            columnDefs.unshift(
                { targets: 0, className: 'hdv2-ticket-cell' },
                { targets: 1, className: 'hdv2-subject-cell' }
            );
        } else if (ajaxType === 'tasks') {
            columnDefs.unshift({ targets: 0, className: 'hdv2-task-name-cell' });
        }

        dataTableOptions = {
            dom: '<"hdv2-dt-top"<"hdv2-dt-buttons"B><"hdv2-dt-length"l><"hdv2-dt-search"f>>rt<"hdv2-dt-bottom"<"hdv2-dt-info"i><"hdv2-dt-pages"p>>',
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            lengthChange: true,
            searching: true,
            ordering: true,
            paging: true,
            pagingType: 'simple_numbers',
            info: true,
            scrollX: false,
            autoWidth: false,
            order: [],
            columnDefs: columnDefs,
            drawCallback: applyWebsitePaginationStyle,
            language: {
                emptyTable: ajaxType === 'tasks' ? 'No tasks found.' : 'No tickets found.'
            }
        };

        if (window.jQuery.fn.dataTable.Buttons) {
            dataTableOptions.buttons = [{
                extend: 'excelHtml5',
                text: '<i class="ti ti-download"></i> Download',
                className: 'btn btn-primary btn-sm',
                title: function () {
                    return filterLabel ? filterLabel.textContent : title;
                },
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            }];
        }

        dataTable = window.jQuery(table).DataTable(dataTableOptions);
        applyWebsitePaginationStyle();

        function normalizeAjaxRows(rows) {
            var columnCount = table && table.tHead && table.tHead.rows.length
                ? table.tHead.rows[0].cells.length
                : 0;

            return (rows || []).map(function (row) {
                var normalized;

                if (Array.isArray(row)) {
                    normalized = row.slice();
                } else if (row && typeof row === 'object') {
                    normalized = Object.keys(row)
                        .sort(function (left, right) {
                            return Number(left) - Number(right);
                        })
                        .map(function (key) {
                            return row[key];
                        });
                } else {
                    normalized = [row || ''];
                }

                while (normalized.length < columnCount) {
                    normalized.push('');
                }

                return columnCount ? normalized.slice(0, columnCount) : normalized;
            });
        }

        function adjustDataTableLayout() {
            dataTable.columns.adjust();
            applyWebsitePaginationStyle();

            window.setTimeout(function () {
                dataTable.columns.adjust();
                applyWebsitePaginationStyle();
            }, 0);
        }

        function updateAjaxRows(rows, totalCount) {
            var wrapper = grid.querySelector('.dataTables_wrapper');
            var searchInput = wrapper ? wrapper.querySelector('.dataTables_filter input') : null;

            grid.setAttribute('data-hdv2-ajax-total', String(totalCount));
            if (searchInput) {
                searchInput.value = '';
            }
            dataTable.search('');
            dataTable.order([]);
            dataTable.clear();
            dataTable.rows.add(rows);
            dataTable.draw();
            adjustDataTableLayout();
        }

        function loadAjaxRows(filter, encryptedFilter) {
            var url;
            var tableWrap = table.closest('.hdv2-table-wrap');

            if (!ajaxUrl) {
                dataTable.draw();
                dataTable.columns.adjust();
                return;
            }

            url = new URL(ajaxUrl, window.location.origin);
            if (encryptedFilter) {
                url.searchParams.set('f', encryptedFilter);
            } else {
                url.searchParams.set('filter', filter || grid.getAttribute('data-hdv2-current-filter') || 'total');
            }

            if (ajaxTypeToken) {
                url.searchParams.set('t', ajaxTypeToken);
            } else {
                url.searchParams.set('type', ajaxType);
            }

            grid.classList.add('is-loading');

            fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Dashboard request failed');
                    }

                    return response.json();
                })
                .then(function (payload) {
                    var rows = normalizeAjaxRows(payload.rows || []);
                    var totalCount = Number(payload.count);

                    if (Number.isNaN(totalCount)) {
                        totalCount = rows.length;
                    }

                    if (table.tHead) {
                        table.tHead.hidden = false;
                    }
                    grid.hidden = false;
                    grid.classList.add('is-active');
                    updateAjaxRows(rows, totalCount);
                    if (tableWrap) {
                        tableWrap.scrollLeft = 0;
                    }
                })
                .catch(function () {
                    grid.hidden = false;
                    grid.classList.add('is-active');
                    updateAjaxRows([], 0);
                })
                .finally(function () {
                    grid.classList.remove('is-loading');
                });
        }

        grid.addEventListener('hdv2:load-filter', function (event) {
            var detail = event.detail || {};
            var selectedFilter = detail.filter || grid.getAttribute('data-hdv2-current-filter') || 'total';
            var selectedFilterToken = detail.token || '';

            grid.setAttribute('data-hdv2-current-filter', selectedFilter);

            if (filterLabel) {
                filterLabel.textContent = detail.title || title;
            }

            loadAjaxRows(selectedFilter, selectedFilterToken);
        });

        if (ajaxUrl) {
            grid.setAttribute('data-hdv2-ajax-total', String(dataTable.rows().count()));
        }

        if (root) {
            root.addEventListener('click', function (event) {
                var button = event.target.closest('[data-hdv2-dashboard-filter]');
                var selectedFilter;
                var selectedFilterToken;

                if (!button || button.disabled || (button.getAttribute('data-hdv2-filter-target') || 'dashboard') !== gridName) {
                    return;
                }

                selectedFilter = button.getAttribute('data-hdv2-dashboard-filter') || 'total';
                selectedFilterToken = button.getAttribute('data-hdv2-dashboard-filter-token') || '';
                grid.setAttribute('data-hdv2-current-filter', selectedFilter);
                grid.hidden = false;
                grid.classList.add('is-active');

                getFilterButtons().forEach(function (item) {
                    item.classList.toggle('is-active', item === button);
                });

                if (filterLabel) {
                    filterLabel.textContent = button.getAttribute('data-hdv2-filter-title') || title;
                }

                loadAjaxRows(selectedFilter, selectedFilterToken);

                if (grid.getAttribute('data-hdv2-start-hidden') === 'true') {
                    grid.scrollIntoView({ block: 'start', behavior: 'smooth' });
                }
            });
        }

        if (ajaxUrl && grid.getAttribute('data-hdv2-autoload') === 'true') {
            loadAjaxRows(grid.getAttribute('data-hdv2-current-filter') || 'total', activeFilterButton ? activeFilterButton.getAttribute('data-hdv2-dashboard-filter-token') || '' : '');
        }
    });

    document.querySelectorAll('[data-hdv2-developer-filter]').forEach(function (panel) {
        var select = panel.querySelector('[data-hdv2-developer-filter-select]');
        var root = panel.closest('.hdv2');
        var developerGrid = root ? root.querySelector('[data-hdv2-grid-name="developer-dashboard"]') : null;
        var developerCardGrid = panel.querySelector('[data-hdv2-developer-stat-grid]');
        var developerCards = Array.prototype.slice.call(panel.querySelectorAll('[data-hdv2-developer-dashboard-card]'));
        var ajaxUrl = panel.getAttribute('data-hdv2-ajax-url') || '';
        var ajaxTypeToken = panel.getAttribute('data-hdv2-ajax-type-token') || '';
        var stageLabels = {
            total: 'Total Tickets',
            in_progress: 'In Progress',
            resolved: 'Resolved',
            returned: 'Returned Tickets'
        };
        var developerMap = {};

        function activeDeveloperCard() {
            return developerCards.filter(function (card) {
                return card.classList.contains('is-active');
            })[0] || developerCards[0] || null;
        }

        function setCardCount(card, value) {
            var count = card.querySelector('[data-hdv2-dashboard-count]');

            if (count) {
                count.textContent = String(Number(value || 0));
            }
        }

        function loadCard(card) {
            if (!card || !developerGrid) {
                return;
            }

            developerGrid.dispatchEvent(new CustomEvent('hdv2:load-filter', {
                detail: {
                    filter: card.getAttribute('data-hdv2-dashboard-filter') || 'total',
                    token: card.getAttribute('data-hdv2-dashboard-filter-token') || '',
                    title: card.getAttribute('data-hdv2-filter-title') || 'Tickets'
                }
            }));
        }

        function activateCard(card) {
            if (!card) {
                return;
            }

            developerCards.forEach(function (item) {
                item.classList.toggle('is-active', item === card);
            });
        }

        function resetDeveloperCards() {
            if (developerCardGrid) {
                developerCardGrid.hidden = true;
            }

            if (developerGrid) {
                developerGrid.hidden = true;
                developerGrid.classList.remove('is-active');
            }

            developerCards.forEach(function (card) {
                var stage = card.getAttribute('data-hdv2-dashboard-stage') || 'total';
                var label = stageLabels[stage] || 'Tickets';

                card.classList.remove('is-active');
                card.disabled = true;
                card.setAttribute('data-hdv2-dashboard-filter', '');
                card.setAttribute('data-hdv2-dashboard-filter-token', '');
                card.setAttribute('data-hdv2-filter-title', label);
                setCardCount(card, 0);
            });
        }

        function updateDeveloperCards(developer) {
            var currentStage = activeDeveloperCard()
                ? activeDeveloperCard().getAttribute('data-hdv2-dashboard-stage') || 'total'
                : 'total';
            var activeCard;

            if (!developer) {
                resetDeveloperCards();
                return;
            }

            if (developerCardGrid) {
                developerCardGrid.hidden = false;
            }

            developerCards.forEach(function (card) {
                var stage = card.getAttribute('data-hdv2-dashboard-stage') || 'total';
                var label = stageLabels[stage] || 'Tickets';

                card.disabled = false;
                card.setAttribute('data-hdv2-dashboard-filter', 'developer:' + developer.developer_userid + ':' + stage);
                card.setAttribute('data-hdv2-dashboard-filter-token', developer.filters && developer.filters[stage] ? developer.filters[stage] : '');
                card.setAttribute('data-hdv2-filter-title', developer.developer_name + ' - ' + label);
                setCardCount(card, developer[stage]);

                if (stage === currentStage) {
                    activeCard = card;
                }
            });

            activateCard(activeCard || developerCards[0]);
        }

        function renderDeveloperOptions(developers) {
            if (!select) {
                return;
            }

            developerMap = {};
            select.innerHTML = '';
            select.appendChild(new Option('All Developers', ''));

            developers.forEach(function (developer) {
                var developerUserId = String(developer.developer_userid || '');

                if (!developerUserId) {
                    return;
                }

                developerMap[developerUserId] = developer;
                select.appendChild(new Option((developer.developer_name || '-') + ' (' + Number(developer.total || 0) + ')', developerUserId));
            });

            select.disabled = false;
        }

        if (!ajaxUrl || !select) {
            return;
        }

        select.addEventListener('change', function () {
            var developer = developerMap[select.value] || null;

            updateDeveloperCards(developer);

            if (developer) {
                loadCard(activeDeveloperCard());
            }
        });

        var url = new URL(ajaxUrl, window.location.origin);
        if (ajaxTypeToken) {
            url.searchParams.set('t', ajaxTypeToken);
        } else {
            url.searchParams.set('type', 'developers');
        }

        fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Developer filter request failed');
                }

                return response.json();
            })
            .then(function (payload) {
                renderDeveloperOptions(payload.developers || []);
            })
            .catch(function () {
                select.innerHTML = '';
                select.appendChild(new Option('All Developers', ''));
                select.disabled = false;
            });
    });

    refreshDashboardDataTables(document);

    document.querySelectorAll('[data-hdv2-grid]').forEach(function (grid) {
        if (grid.hasAttribute('data-hdv2-datatable')) {
            return;
        }

        var gridName = grid.getAttribute('data-hdv2-grid-name') || 'default';
        var rows = Array.prototype.slice.call(grid.querySelectorAll('[data-hdv2-row]'));
        var emptyRow = grid.querySelector('[data-hdv2-empty-row]');
        var pageSize = grid.querySelector('[data-hdv2-page-size]');
        var pageInfo = grid.querySelector('[data-hdv2-page-info]');
        var pageButtons = grid.querySelector('[data-hdv2-page-buttons]');
        var filterLabel = grid.querySelector('[data-hdv2-filter-label]');
        var downloadButton = grid.querySelector('[data-hdv2-download-grid]');
        var sortButtons = Array.prototype.slice.call(grid.querySelectorAll('[data-hdv2-sort]'));
        var tableBody = rows.length ? rows[0].closest('tbody') : null;
        var root = grid.closest('.hdv2');
        var filterButtons = Array.prototype.slice.call(root ? root.querySelectorAll('[data-hdv2-dashboard-filter]') : []).filter(function (button) {
            return (button.getAttribute('data-hdv2-filter-target') || 'dashboard') === gridName;
        });
        var activeFilterButton = filterButtons.filter(function (button) {
            return button.classList.contains('is-active');
        })[0];
        var currentPage = 1;
        var currentFilter = activeFilterButton
            ? activeFilterButton.getAttribute('data-hdv2-dashboard-filter') || 'total'
            : 'total';
        var currentSort = {
            key: '',
            direction: 'asc'
        };

        if (!pageSize || !pageInfo || !pageButtons) {
            return;
        }

        function rowMatchesFilter(row) {
            var status = row.getAttribute('data-hdv2-status') || '';
            var taskStatus = row.getAttribute('data-hdv2-task-status') || '';
            var priority = row.getAttribute('data-hdv2-priority') || '';
            var pending = row.getAttribute('data-hdv2-pending') || 'N';
            var active = row.getAttribute('data-hdv2-active') || 'N';
            var resolved = row.getAttribute('data-hdv2-resolved') || 'N';
            var closed = row.getAttribute('data-hdv2-closed') || 'N';
            var reopened = row.getAttribute('data-hdv2-reopened') || 'N';
            var returned = row.getAttribute('data-hdv2-returned') || 'N';
            var isUrgent = priority === 'critical' || priority === 'urgent';

            if (taskStatus) {
                if (currentFilter === 'total') {
                    return row.getAttribute('data-hdv2-task-total') !== 'N';
                }

                if (currentFilter === 'in_progress') {
                    return row.getAttribute('data-hdv2-task-in-progress') === 'Y';
                }

                if (currentFilter === 'pending') {
                    return row.getAttribute('data-hdv2-task-pending') === 'Y';
                }

                if (currentFilter === 'overdue') {
                    return row.getAttribute('data-hdv2-task-overdue') === 'Y';
                }

                if (currentFilter === 'completed') {
                    return row.getAttribute('data-hdv2-task-completed') === 'Y';
                }

                return currentFilter === taskStatus;
            }

            if (currentFilter === 'pending') {
                return pending === 'Y';
            }

            if (currentFilter === 'critical') {
                return isUrgent;
            }

            if (currentFilter === 'in_progress') {
                return active === 'Y' && returned !== 'Y' && !isUrgent;
            }

            if (currentFilter === 'urgent') {
                return active === 'Y' && returned !== 'Y' && isUrgent;
            }

            if (currentFilter === 'returned') {
                return active === 'Y' && returned === 'Y';
            }

            if (currentFilter === 'resolved') {
                return resolved === 'Y' || status === 'resolved';
            }

            if (currentFilter === 'resolved_closed') {
                return resolved === 'Y' || status === 'resolved' || closed === 'Y' || status === 'closed';
            }

            if (currentFilter.indexOf('priority:') === 0) {
                return priority === currentFilter.substring(9);
            }

            if (currentFilter.indexOf('status:') === 0) {
                return status === currentFilter.substring(7);
            }

            if (currentFilter === 'closed') {
                return closed === 'Y' || status === 'closed';
            }

            if (currentFilter === 'reopened') {
                return reopened === 'Y';
            }

            return true;
        }

        function filteredRows() {
            return sortedRows(rows.filter(rowMatchesFilter));
        }

        function sortValue(row, key) {
            var cell = row.querySelector('[data-hdv2-sort-value="' + key + '"]');

            if (!cell) {
                return '';
            }

            var raw = cell.getAttribute('data-hdv2-sort-raw') || cell.textContent || '';

            if ((cell.getAttribute('data-hdv2-sort-type') || '') === 'number') {
                return Number(raw) || 0;
            }

            return String(raw).trim().toLowerCase();
        }

        function sortedRows(items) {
            if (!currentSort.key) {
                return items;
            }

            return items.slice().sort(function (left, right) {
                var leftValue = sortValue(left, currentSort.key);
                var rightValue = sortValue(right, currentSort.key);
                var comparison = 0;

                if (typeof leftValue === 'number' || typeof rightValue === 'number') {
                    comparison = leftValue - rightValue;
                } else {
                    comparison = String(leftValue).localeCompare(String(rightValue));
                }

                return currentSort.direction === 'desc' ? comparison * -1 : comparison;
            });
        }

        function updateSortButtons() {
            sortButtons.forEach(function (button) {
                var isActive = button.getAttribute('data-hdv2-sort') === currentSort.key;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-sort', isActive ? currentSort.direction : 'none');
                button.setAttribute('data-hdv2-sort-direction', isActive ? currentSort.direction : '');
            });
        }

        function csvValue(value) {
            var text = String(value || '').replace(/\s+/g, ' ').trim();

            return '"' + text.replace(/"/g, '""') + '"';
        }

        function downloadGrid() {
            var table = grid.querySelector('table');
            var headerCells = table ? Array.prototype.slice.call(table.querySelectorAll('thead th')) : [];
            var visibleRows = filteredRows();
            var title = filterLabel ? filterLabel.textContent : gridName;
            var fileName = (gridName + '-' + title).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'tickets';
            var csvRows = [];
            var link;
            var url;

            csvRows.push(headerCells.map(function (header) {
                return csvValue(header.textContent);
            }).join(','));

            visibleRows.forEach(function (row) {
                csvRows.push(Array.prototype.slice.call(row.children).map(function (cell) {
                    return csvValue(cell.innerText || cell.textContent);
                }).join(','));
            });

            url = URL.createObjectURL(new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' }));
            link = document.createElement('a');
            link.href = url;
            link.download = fileName + '.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function pageCount() {
            var size = parseInt(pageSize.value, 10) || 10;
            return Math.max(1, Math.ceil(filteredRows().length / size));
        }

        function addPagerButton(label, targetPage, disabled, active) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'hdv2-page-btn';
            button.textContent = label;
            button.disabled = disabled;
            button.setAttribute('aria-label', label);

            if (active) {
                button.classList.add('is-active');
                button.setAttribute('aria-current', 'page');
            }

            button.addEventListener('click', function () {
                currentPage = targetPage;
                renderGrid();
            });

            pageButtons.appendChild(button);
        }

        function visiblePages(totalPages) {
            var pages = [];
            var start = Math.max(1, currentPage - 2);
            var end = Math.min(totalPages, currentPage + 2);

            if (currentPage <= 3) {
                end = Math.min(totalPages, 5);
            }

            if (currentPage >= totalPages - 2) {
                start = Math.max(1, totalPages - 4);
            }

            for (var page = start; page <= end; page += 1) {
                pages.push(page);
            }

            return pages;
        }

        function renderGrid() {
            var size = parseInt(pageSize.value, 10) || 10;
            var visibleRows = filteredRows();
            var totalRows = visibleRows.length;
            var totalPages = pageCount();
            currentPage = Math.min(Math.max(currentPage, 1), totalPages);

            var startIndex = (currentPage - 1) * size;
            var endIndex = Math.min(startIndex + size, totalRows);

            if (tableBody) {
                visibleRows.forEach(function (row) {
                    tableBody.appendChild(row);
                });

                if (emptyRow) {
                    tableBody.appendChild(emptyRow);
                }
            }

            rows.forEach(function (row) {
                row.hidden = true;
            });

            visibleRows.forEach(function (row, index) {
                row.hidden = index < startIndex || index >= endIndex;
            });

            if (emptyRow) {
                emptyRow.hidden = totalRows > 0;
            }

            pageInfo.textContent = totalRows
                ? 'Showing ' + (startIndex + 1) + ' to ' + endIndex + ' of ' + totalRows + ' entries'
                : 'Showing 0 to 0 of 0 entries';

            pageButtons.innerHTML = '';
            updateSortButtons();

            addPagerButton('Previous', currentPage - 1, currentPage === 1 || totalRows === 0, false);

            visiblePages(totalPages).forEach(function (page) {
                addPagerButton(String(page), page, totalRows === 0, page === currentPage);
            });

            addPagerButton('Next', currentPage + 1, currentPage === totalPages || totalRows === 0, false);
        }

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                currentFilter = button.getAttribute('data-hdv2-dashboard-filter') || 'total';
                currentPage = 1;
                grid.hidden = false;

                filterButtons.forEach(function (item) {
                    item.classList.toggle('is-active', item === button);
                });

                if (filterLabel) {
                    filterLabel.textContent = button.getAttribute('data-hdv2-filter-title') || 'Tickets';
                }

                renderGrid();

                if (grid.getAttribute('data-hdv2-start-hidden') === 'true') {
                    grid.scrollIntoView({ block: 'start', behavior: 'smooth' });
                }
            });
        });

        pageSize.addEventListener('change', function () {
            currentPage = 1;
            renderGrid();
        });

        sortButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var sortKey = button.getAttribute('data-hdv2-sort') || '';

                if (!sortKey) {
                    return;
                }

                if (currentSort.key === sortKey) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.key = sortKey;
	                    currentSort.direction = sortKey === 'updated' || sortKey === 'created' || sortKey === 'age' || sortKey === 'returned_on' ? 'desc' : 'asc';
                }

                currentPage = 1;
                renderGrid();
            });
        });

        if (downloadButton) {
            downloadButton.addEventListener('click', downloadGrid);
        }

        if (filterLabel && activeFilterButton) {
            filterLabel.textContent = activeFilterButton.getAttribute('data-hdv2-filter-title') || 'Tickets';
        }

        renderGrid();
    });
});
