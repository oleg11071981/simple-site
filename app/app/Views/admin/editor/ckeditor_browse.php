<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выбор файла из галереи</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Заголовок и переключатель вида */
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        h1 {
            font-size: 20px;
            color: #2c3e50;
        }

        .view-toggle {
            display: flex;
            gap: 10px;
            background: white;
            padding: 4px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .view-btn {
            padding: 6px 16px;
            border: none;
            background: none;
            cursor: pointer;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .view-btn.active {
            background: #007bff;
            color: white;
        }

        /* Фильтры */
        .filters {
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-group label {
            font-size: 13px;
            color: #495057;
            font-weight: 500;
        }

        .filter-select, .filter-input {
            padding: 6px 10px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 13px;
            background: white;
        }

        .filter-input {
            width: 200px;
        }

        .filter-input:focus {
            outline: none;
            border-color: #007bff;
        }

        /* Плиточная сетка */
        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Элемент плитки */
        .file-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 2px solid transparent;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .file-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .file-card.selected {
            border-color: #007bff;
            background: #e7f1ff;
        }

        .file-preview {
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 140px;
            object-fit: cover;
        }

        .file-preview .file-icon {
            font-size: 64px;
        }

        .file-name {
            font-size: 13px;
            font-weight: 500;
            color: #333;
            text-align: center;
            word-break: break-all;
            margin-bottom: 5px;
        }

        .file-details {
            font-size: 11px;
            color: #6c757d;
            text-align: center;
        }

        /* Табличный вид (список) */
        .files-table-view {
            background: white;
            border-radius: 12px;
            overflow-x: auto;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .files-table-view table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }

        .files-table-view th {
            background: #f8f9fa;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
        }

        .files-table-view td {
            padding: 10px 16px;
            border-bottom: 1px solid #e9ecef;
        }

        .files-table-view tr:hover {
            background: #f8f9fa;
            cursor: pointer;
        }

        .files-table-view tr.selected {
            background: #e7f1ff;
        }

        .table-preview {
            width: 40px;
            text-align: center;
        }

        /* Пагинация */
        .load-more {
            text-align: center;
            margin-top: 20px;
        }

        .load-more button {
            background: #007bff;
            color: white;
            padding: 8px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .load-more button:hover {
            background: #0056b3;
        }

        /* Кнопки действий */
        .actions {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 20px;
        }

        .btn-insert {
            background: #28a745;
            color: white;
            padding: 10px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-insert:hover {
            background: #218838;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 10px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .empty {
            text-align: center;
            padding: 50px;
            color: #6c757d;
            background: white;
            border-radius: 12px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .files-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 12px;
            }
            .file-preview {
                height: 120px;
            }
            .file-preview .file-icon {
                font-size: 48px;
            }
            .filters {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-group {
                justify-content: space-between;
            }
            .filter-input {
                width: 100%;
            }
            .header-bar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <h1>📁 Выбор файла из галереи</h1>
        <div class="view-toggle">
            <button class="view-btn active" data-view="grid">📱 Плитка</button>
            <button class="view-btn" data-view="list">📋 Список</button>
        </div>
    </div>

    <div class="filters">
        <div class="filter-group">
            <label>Тип:</label>
            <select id="fileType" class="filter-select">
                <option value="all">Все файлы</option>
                <option value="image">🖼️ Изображения</option>
                <option value="document">📄 Документы</option>
            </select>
        </div>
        <div class="filter-group">
            <label>🔍 Поиск:</label>
            <input type="text" id="searchInput" class="filter-input" placeholder="Название файла...">
        </div>
    </div>

    <!-- Плиточный вид -->
    <div id="filesGrid" class="files-grid">
        <div class="loading">⏳ Загрузка файлов...</div>
    </div>

    <!-- Табличный вид -->
    <div id="filesTableView" class="files-table-view">
        <table>
            <thead>
            <tr>
                <th style="width: 60px"></th>
                <th>Название</th>
                <th style="width: 80px">Тип</th>
                <th style="width: 80px">Размер</th>
            </tr>
            </thead>
            <tbody id="filesTableBody"></tbody>
        </table>
    </div>

    <div class="load-more" id="loadMoreBtn" style="display: none;">
        <button onclick="loadMore()">Загрузить ещё</button>
    </div>

    <div class="actions">
        <button class="btn-cancel" onclick="window.close()">Отмена</button>
        <button class="btn-insert" onclick="insertFile()">Вставить</button>
    </div>
</div>

<script>
    let selectedFile = null;
    let selectedFileUrl = null;
    let currentPage = 1;
    let isLoading = false;
    let hasMore = true;
    let currentSearch = '';
    let currentView = 'grid';

    // Определяем тип из URL параметра
    const urlParams = new URLSearchParams(window.location.search);
    let currentFilter = urlParams.get('type') || 'all';

    // Обновляем селект
    const fileTypeSelect = document.getElementById('fileType');
    if (fileTypeSelect) {
        fileTypeSelect.value = currentFilter;
    }

    // Переключение вида
    function setView(view) {
        currentView = view;
        const gridView = document.getElementById('filesGrid');
        const tableView = document.getElementById('filesTableView');
        const btns = document.querySelectorAll('.view-btn');

        btns.forEach(btn => {
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        if (view === 'grid') {
            gridView.style.display = 'grid';
            tableView.style.display = 'none';
        } else {
            gridView.style.display = 'none';
            tableView.style.display = 'block';
        }
    }

    async function loadFiles() {
        if (isLoading) return;
        isLoading = true;

        try {
            const response = await fetch(`/admin-panel/editor/get-files?page=${currentPage}&type=${currentFilter}&search=${encodeURIComponent(currentSearch)}`);
            const data = await response.json();

            const grid = document.getElementById('filesGrid');
            const tableBody = document.getElementById('filesTableBody');

            if (currentPage === 1) {
                grid.innerHTML = '';
                tableBody.innerHTML = '';
            }

            if (data.files.length === 0 && currentPage === 1) {
                grid.innerHTML = '<div class="empty">📁 Файлы не найдены<br><small>Загрузите файлы через раздел "Файловый менеджер"</small></div>';
                tableBody.innerHTML = '<tr><td colspan="4" class="empty">Файлы не найдены</td></tr>';
                document.getElementById('loadMoreBtn').style.display = 'none';
                isLoading = false;
                return;
            }

            data.files.forEach(file => {
                // Для плитки
                const card = document.createElement('div');
                card.className = 'file-card';
                card.dataset.url = file.url;
                card.dataset.id = file.id;
                card.onclick = () => selectFile(card, file.url);

                let previewHtml = '';
                if (file.file_type === 'image') {
                    previewHtml = `<img src="${file.url}" alt="${escapeHtml(file.name)}" onerror="this.parentElement.innerHTML='<div class=\\'file-icon\\'>🖼️</div>'">`;
                } else {
                    const icon = getFileIcon(file.file_ext);
                    previewHtml = `<div class="file-icon">${icon}</div>`;
                }

                card.innerHTML = `
                        <div class="file-preview">${previewHtml}</div>
                        <div class="file-name">${escapeHtml(file.name)}</div>
                        <div class="file-details">${file.size_formatted} • ${file.file_ext.toUpperCase()}</div>
                    `;
                grid.appendChild(card);

                // Для таблицы
                const row = document.createElement('tr');
                row.dataset.url = file.url;
                row.onclick = () => selectFile(row, file.url);
                row.innerHTML = `
                        <td class="table-preview">${file.file_type === 'image' ? '🖼️' : getFileIcon(file.file_ext)}</td>
                        <td>${escapeHtml(file.name)}</td>
                        <td>${file.file_ext.toUpperCase()}</td>
                        <td>${file.size_formatted}</td>
                    `;
                tableBody.appendChild(row);
            });

            hasMore = data.has_more;
            document.getElementById('loadMoreBtn').style.display = hasMore ? 'block' : 'none';
        } catch (error) {
            console.error('Ошибка загрузки:', error);
            document.getElementById('filesGrid').innerHTML = '<div class="empty">❌ Ошибка загрузки файлов</div>';
        }

        isLoading = false;
    }

    function getFileIcon(ext) {
        const icons = {
            'jpg': '🖼️', 'jpeg': '🖼️', 'png': '🖼️', 'gif': '🖼️',
            'pdf': '📄', 'doc': '📝', 'docx': '📝', 'xls': '📊',
            'xlsx': '📊', 'zip': '📦', 'rar': '📦', 'txt': '📃',
            'default': '📁'
        };
        return icons[ext] || icons.default;
    }

    function selectFile(element, url) {
        document.querySelectorAll('.file-card, .files-table-view tr').forEach(el => {
            el.classList.remove('selected');
        });
        element.classList.add('selected');
        selectedFileUrl = url;
    }

    function insertFile() {
        if (selectedFileUrl) {
            const funcNum = urlParams.get('CKEditorFuncNum');
            const fieldName = urlParams.get('field');

            // Если это вызов для поля формы (не CKEditor)
            if (fieldName && window.opener && window.opener.setSelectedFile) {
                // Получаем ID файла из выбранного элемента
                const selectedCard = document.querySelector('.file-card.selected');
                const fileId = selectedCard ? selectedCard.dataset.id : '';
                const fileUrl = selectedFileUrl;
                const fileName = fileUrl.split('/').pop();

                window.opener.setSelectedFile(fileId, fileName, fileUrl);
                window.close();
            }
            // Если это вызов из CKEditor
            else if (funcNum && window.opener && window.opener.CKEDITOR) {
                window.opener.CKEDITOR.tools.callFunction(funcNum, selectedFileUrl);
                window.close();
            } else {
                alert('Файл выбран: ' + selectedFileUrl);
                window.close();
            }
        } else {
            alert('Пожалуйста, выберите файл');
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function loadMore() {
        currentPage++;
        loadFiles();
    }

    function resetAndLoad() {
        currentPage = 1;
        hasMore = true;
        loadFiles();
    }

    // Обработчики
    document.getElementById('fileType').addEventListener('change', (e) => {
        currentFilter = e.target.value;
        resetAndLoad();
    });

    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = e.target.value;
            resetAndLoad();
        }, 500);
    });

    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => setView(btn.dataset.view));
    });

    // Загружаем файлы
    setView('grid');
    loadFiles();
</script>
</body>
</html>