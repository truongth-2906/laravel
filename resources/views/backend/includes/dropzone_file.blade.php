<div id="group-upload-files" class="form-content form-group-photo d-flex flex-column list-file-uploaded">
    <div
        class="form-group-base dropzone-content dropzone-content-project d-flex flex-column justify-content-between align-items-center mb-2"
        ondrop="dropHandler(event, '{{ TYPE_DROP_PROJECT }}');" ondragover="dragOverHandler(event);">
        <div
            class="dropzone-content-img-container dropzone-content-img-drop d-flex justify-content-center align-items-center">
            <img src="{{asset('img/upload-icon.svg')}}" alt="">
        </div>
        <div class="description-color font-size-14 font-weight-400">
            <span class="primary-color font-weight-600 click-text-dropzone-upload">Click to upload</span>
            or drag and drop
        </div>
        <div class="description-color font-size-14 font-weight-400">
            .DOC, .DOCX or PDF (max. 2 MB per document)
        </div>
    </div>
    @error('file_upload.*')
    <div class="text-danger font-14">{{ $message }}</div>
    @enderror
    @error('file_name.*')
    <div class="text-danger font-14">{{ $message }}</div>
    @enderror
    <div class="text-danger font-14 d-none error-file-invalid">
        @lang('File upload format is not correct.')
    </div>
    <div class="text-danger font-14 d-none error-exceed">
        @lang('Upload files must not exceed 2MB.')
    </div>
    @if(isset($file_uploaded))
        @foreach($file_uploaded as $key => $file)
            <div class="form-group-base status-upload-group d-flex justify-content-between file-uploaded"
                 id="file-upload-{{ $key }}">
                <div class="project-type d-flex justify-content-center align-items-center mr-3">
                    <div class="pdf"></div>
                </div>
                <div class="project-info full-width">
                    <div class="d-flex justify-content-between mb-1">
                        <div class="w-100">
                            <input type="text" readonly name="file_name[]" data-action="{{ route('frontend.download', ['filename' => $file->file]) }}"
                                class="project-filename text-file-project font-weight-500 name-file text-break w-80 has-download" value="{{ $file->name }}">
                            <div class="project-filesize text-file-project font-weight-400 size-file"></div>
                        </div>
                        <div class="status-upload">
                            <button type="button" class="btn status-upload-btn btn-delete-file old-file-upload"
                                    data-id="{{ $file->id }}"></button>
                        </div>
                    </div>
                    <div class="d-none justify-content-between align-items-center">
                        <div class="progress-bar full-width mr-2" style="background: #2200A5">
                            <div class="progress-bar-child show-progress-bar" style="width: 0;"></div>
                        </div>
                        <div class="progress-percent text-file-project font-weight-500 progress-value"></div>
                    </div>
                    <div class="edit-file-name-upload" data-id="{{ $file->name }}"></div>
                </div>
            </div>
            <div class="text-danger error-file-name-upload d-none"></div>
        @endforeach
    @endif
</div>
<input id="dropzone-project" class="d-none" multiple type="file" name="file_upload[]"/>
