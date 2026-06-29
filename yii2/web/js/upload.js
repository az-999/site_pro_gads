document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('import-form');
    if (!form || typeof ss === 'undefined') {
        console.error('Import upload: SimpleAjaxUploader не загружен');
        return;
    }

    var uploadUrl = form.getAttribute('data-upload-url');
    if (!uploadUrl) {
        console.error('Import upload: URL не задан');
        return;
    }

    var progressBox = document.getElementById('progressBox');
    var csrfName = form.getAttribute('data-csrf-name');
    var csrfToken = form.getAttribute('data-csrf-token');

    var uploader = new ss.SimpleUpload({
        button: 'upload-btn',
        dropzone: 'dropzone',
        url: uploadUrl,
        name: 'uploadfile',
        responseType: 'json',
        multiple: true,
        allowedExtensions: ['csv', 'json'],
        onSubmit: function (filename) {
            var data = {
                source_type: document.getElementById('source_type').value
            };
            if (csrfName && csrfToken) {
                data[csrfName] = csrfToken;
            }
            this.setData(data);

            var wrapper = document.createElement('div');
            var fileSize = document.createElement('div');
            var progress = document.createElement('div');
            var bar = document.createElement('div');

            wrapper.className = 'wrapper';
            progress.className = 'progress';
            bar.className = 'progress-bar progress-bar-striped progress-bar-animated';
            bar.style.width = '0%';
            fileSize.className = 'size';

            wrapper.innerHTML = '<div class="name">' + filename + '</div>';
            progress.appendChild(bar);
            wrapper.appendChild(fileSize);
            wrapper.appendChild(progress);
            progressBox.appendChild(wrapper);

            this.setProgressBar(bar);
            this.setFileSizeBox(fileSize);
            this.setProgressContainer(wrapper);
        },
        onComplete: function (filename, response) {
            if (!response || !response.success) {
                alert((filename || 'Файл') + ': ' + (response && response.msg ? response.msg : 'ошибка загрузки'));
                return false;
            }
            var alertEl = document.createElement('div');
            alertEl.className = 'alert alert-success mt-2';
            alertEl.textContent = filename + ' — импортировано ' + response.rows + ' строк.';
            progressBox.appendChild(alertEl);
            setTimeout(function () { window.location.reload(); }, 1500);
        },
        onExtError: function (filename, extension) {
            alert('Недопустимый формат: .' + extension + '. Нужен CSV или JSON.');
        },
        onError: function (filename, type, status, statusText, response) {
            alert('Ошибка загрузки ' + filename + ': ' + (statusText || type));
        }
    });
});
