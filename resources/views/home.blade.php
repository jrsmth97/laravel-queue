<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ str_replace('_', ' ', config('app.name')) }}</title>
    <script src="{{ asset('js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ url('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/vnotify.css') }}">
    <script defer src="{{ url('assets/js/vnotify.js') }}"></script>
    <script src="{{ url('assets/js/mini-project.js') }}"></script>
</head>
<body>
    <div class="file-drop-area">
        <span class="file-msg">Select file/drag and drop here</span>
        <span class="upload-btn"><div class="spinner-border spinner-border-custom d-none" role="status"></div>Upload File</span>
        <input class="file-input" type="file">
    </div>
    <div class="table-area">
        <table class="table table-dark table-striped h-auto mb-0">
            <thead>
                <tr>
                    <th scope="col">Time</th>
                    <th scope="col">File Name</th>
                    <th scope="col" colspan="2">
                        Status
                        <button class="btn btn-sm btn-light btn-clear-list">Clear List</button>
                    </th>
                </tr>
            </thead>
        </table>
        <div class="table-responsive">
            <table class="table table-dark table-striped">
                <tbody id="fileList">
                    <tr class="no-data">
                        <td></td>
                        <td></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        const miniProject = new MiniProject()
        miniProject.init()
    </script>
</body>
</html>