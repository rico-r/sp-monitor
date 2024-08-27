<html>
<head>
    <title>PDF Viewer</title>
    <script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
</head>
<body>
    <div>
        <canvas id="pdf-canvas"></canvas>
    </div>
    <script>
        var url = "{{ asset('storage/'.$file) }}";
        var pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://mozilla.github.io/pdf.js/build/pdf.worker.js';

        var loadingTask = pdfjsLib.getDocument(url);
        loadingTask.promise.then(function(pdf) {
            pdf.getPage(1).then(function(page) {
                var scale = 1.5;
                var viewport = page.getViewport({scale: scale});

                var canvas = document.getElementById('pdf-canvas');
                var context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                var renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                page.render(renderContext);
            });
        }).catch(function(error) {
            console.error('Error loading PDF: ', error);
            alert('Could not load PDF file. Please try again later.');
        });
    </script>
</body>
</html>
