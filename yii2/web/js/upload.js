document.addEventListener('DOMContentLoaded', function () {
    if (typeof ss === 'undefined' || !window.UPLOAD_URL) {
        return;
    }

    var progressBox = document.getElementById('progressBox');

    var uploader = new ss.SimpleUpload({
        button: 'upload-btn',
        dropzone: 'dropzone',
        url: window.UPLOAD_URL,
        name: 'uploadfile',
        responseType: 'json',
        multiple: true,
        allowedExtensions: ['csv', 'json'],
        form: document.getElementById('import-form'),
        onSubmit: function (filename) {
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
        }
    });
});
