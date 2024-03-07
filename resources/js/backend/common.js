const TYPE_IMAGE = ['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml', 'image/gif']
const MAX_WIDTH = '800';
const MAX_HEIGHT = '400';
const MAX_TEXTAREA = '1000';
const MAX_FILE_UPLOAD = 2000;
const NUMBER_CONVERT_BYTE = 1024;
const TYPE_IMAGE_UPLOAD = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'];
const DATA_TRANSFER = new DataTransfer();
let idFileDeleteArr = [];
let isPC = true;
const TOTAL_CHARACTERS = 1000;

export const downloadExampleCv = function (fileUrl, fileName) {
    let a = document.createElement("a");
    a.href = fileUrl;
    a.setAttribute("download", fileName);
    a.click();
}

export const removeHtmlRemainDevice = function () {
    isPC = $('.container-fluid.pc-device').css('display') === 'block';

    if (isPC) {
        $(`.container-fluid.mobile-device .table-datas`).html('')
    } else {
        $(`.container-fluid.pc-device .table-datas`).html('')
    }
}

export const loading = function (isLoading) {
    if (isLoading) {
        $('#loading').removeClass('d-none').addClass('d-flex');
    } else {
        $('#loading').removeClass('d-flex').addClass('d-none');
    }
}

export const scrollToError = function (errorSelector = '.errors') {
    const selector = $(`${errorSelector}[style!="display: none;"]`);
    const stickyBarHeight = $('form .position-sticky.sticky-bar').first().height() || 100;
    const sidebarHeight = $('#sidebar-mobile').height();

    if (
        selector.length > 0
    ) {
        $("html, body").animate(
            {
                scrollTop: selector.first().parent().offset().top - (stickyBarHeight + sidebarHeight),
            },
            1500
        );
    }
}

let progressUploadTemplate = function (index, element) {
    const html = `
        <div class="form-group-base status-upload-group file-uploaded" id="file-upload-${index}" data-index="${index}"">
            <div class="d-flex justify-content-between">
                <div class="project-type d-flex justify-content-center align-items-center mr-3">
                    <div class="pdf"></div>
                </div>
                <div class="project-info full-width">
                    <div class="d-flex justify-content-between mb-1">
                        <div class="w-100">
                            <input type="text" name="file_name[]" readonly class="project-filename text-file-project font-weight-500 name-file text-break w-80">
                            <div class="project-filesize text-file-project font-weight-400 size-file"></div>
                        </div>
                        <div class="status-upload">
                            <button type="button" class="btn status-upload-btn btn-delete-file"></button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="progress-bar full-width mr-2">
                            <div class="progress-bar-child show-progress-bar" style="width: 0;"></div>
                        </div>
                        <div class="progress-percent text-file-project font-weight-500 progress-value"></div>
                    </div>
                    <div class="edit-file-name-upload d-none"></div>
                </div>
            </div>
            <div class="text-danger errors file_upload_${index}_input_error_message" style="display: none;"></div>
        </div>
        <div class="text-danger error-file-name-upload d-none"></div>`;


    $(element).append(html);
}

function validateFileNameUpload() {
    let validate = true;
    $('.error-file-name-upload').text('');
    $('.project-info input.name-file').map(function (index) {
        const fileName = $(this).val().trim();
        if (fileName.length > 255) {
            validate = false;
            $('.error-file-name-upload').eq(index).removeClass('d-none').text('The file name must not be greater than 255 characters.');
        }
        if (!fileName) {
            validate = false;
            $('.error-file-name-upload').eq(index).removeClass('d-none').text('The file name field is required.');
        }
    });

    return validate;
}

function renderIndexFileUpload() {
    $('.list-file-uploaded .file-uploaded').removeAttr('id data-index');
    $('.list-file-uploaded .file-uploaded').map(function (index) {
        $(this).attr({'id': 'file-upload-' + index, 'data-index': index});
    });
}

let addListenersUpload = function (reader, file, index) {
    let value = 0;
    const processElement = $(`#file-upload-${index} .show-progress-bar`);
    const valueElement = $(`#file-upload-${index} .progress-value`);
    let size = '';

    reader.addEventListener('loadstart', function (e) {
        value = (e.loaded / e.total).toFixed(0);
        processElement.css("width", `${value}%`);
        valueElement.text(`${value}%`);
    });
    reader.addEventListener('load', function (e) {
        value = (e.loaded / e.total * 100).toFixed(0);
        processElement.css("width", `${value}%`);
        valueElement.text(`${value}%`);
    });
    reader.addEventListener('progress', function (e) {
        value = (e.loaded / e.total * 100).toFixed(0);
        processElement.css("width", `${value}%`);
        valueElement.text(`${value}%`);
    });
    reader.addEventListener('loadend', function (e) {
        value = (e.loaded / e.total * 100).toFixed(0);
        processElement.css("width", `${value}%`);
        valueElement.text(`${value}%`);
        $(`#file-upload-${index}`).addClass('successful');
    });
    $(`#file-upload-${index} .name-file`).val(file.name);

    if (file.size < NUMBER_CONVERT_BYTE * NUMBER_CONVERT_BYTE) {
        size = (file.size / NUMBER_CONVERT_BYTE).toFixed(1) + 'KB';
    } else {
        size = (file.size / NUMBER_CONVERT_BYTE / NUMBER_CONVERT_BYTE).toFixed(1) + 'MB';
    }

    $(`#file-upload-${index} .size-file`).text(size);
    setTimeout(() => {
        $(`#file-upload-${index}`).removeClass('successful');
        $(`#file-upload-${index} .progress-bar`).parent().removeClass('d-flex').addClass('d-none');
        $(`#file-upload-${index} .edit-file-name-upload`).removeClass('d-none');
    }, 4000);
}

let handleSelectedUpload = function (reader, element, index) {
    const selectedFile = document.getElementById(element).files[0];
    if (selectedFile) {
        addListenersUpload(reader, selectedFile, index);
        reader.readAsDataURL(selectedFile);
    }
}

let validateFileUpload = function (element) {
    let img = document.getElementById(element);
    let selectedFile = img.files[0];

    if (img.files.length > 0) {
        let error = $("#group-upload-files");
        error.find('.text-danger').addClass('d-none');

        if (!TYPE_IMAGE_UPLOAD.includes(selectedFile.type)) {
            error.find('.error-file-invalid').removeClass('d-none');
            return false;
        }

        if (selectedFile.size > MAX_FILE_UPLOAD * NUMBER_CONVERT_BYTE) {
            error.find('.error-exceed').removeClass('d-none');
            return false;
        }

        return true;
    }

    return false;
}

function validateInputFile(thisElement) {
    const img = document.getElementById('image-' + thisElement.id);
    let file = thisElement.files[0];
    let newImg = new Image();
    let error = $(".error-" + thisElement.id);

    newImg.src = window.URL.createObjectURL(thisElement.files[0]);
    error.addClass('d-none');

    if (!TYPE_IMAGE.includes(file.type)) {
        error.parent().find('.error-file-invalid').removeClass('d-none');
        return false;
    }

    newImg.onload = () => {
        if (newImg.width > MAX_WIDTH || newImg.height > MAX_HEIGHT) {
            error.parent().find('.error-size').removeClass('d-none');
            thisElement.value = '';
            img.src = '';
        } else {
            img.src = URL.createObjectURL(file);
        }
    }
}

function formatState(state) {
    if (!state.id) return state.text;
    const baseUrl = "images";
    const imageHtml = state.path ? '<div class="flag-icon d-flex justify-content-center align-items-center">' +
        '<img src="' + baseUrl + '/' + state.path + '.svg" class="img-flag"  alt=""/>' +
        '</div>' : '';

    return $(
        '<div class="full-width d-flex align-items-center">' +
        imageHtml +
        '<div class="font-weight-500 font-size-16 dropdown-title-color ml-2 center-select2">' + state.text + '</div>' +
        '</div>'
    );
}

function formatStateCountry(state) {
    if (!state.id) return state.text;
    const image_path = $(state.element).attr('data-path');
    const imageHtml = image_path ? '<div class="flag-icon d-flex justify-content-center align-items-center">' +
        '<img src="' + image_path + '" class="img-flag"  alt=""/>' +
        '</div>' : '';

    return $(
        '<div class="full-width d-flex align-items-center">' +
        imageHtml +
        '<div class="font-weight-500 font-size-16 ml-2 center-select2">' + state.text + '</div>' +
        '</div>'
    );
}

export function formatResultCategory(state) {
    if (!state.id) return state.text;
    return $(
        `<div class="font-weight-500 font-size-16 color-inherit" data-select2-manual-id=${state.id} data-select2-color=${state.color} data-select2-border-color=${state.borderColor} data-select2-background-color=${state.backgroundColor}>` +
        state.text +
        '</div>'
    );
}

function submitFormEdit(element) {
    idFileDeleteArr.map(id => {
        $('#' + element).append(`<input type="hidden" name="portfolios_delete[]" value="${id}">`);
    });
    $('#' + element).submit();
}

function changeCheckAllCheckbox(ele) {
    let iptChecked = $(ele + ':checkbox:checked').length;
    $('.container-fluid .ipt-has-check').prop('checked', iptChecked !== 0)
}

Object.defineProperty(String.prototype, 'toCapitalize', {
    value: function() {
      return this.charAt(0).toUpperCase() + this.slice(1);
    },
    enumerable: false
});


export const getCharactersLeft = function () {
    let des = $(this);

    if (des.length) {
        let charactersLeft = TOTAL_CHARACTERS - des.val().length;

        if (charactersLeft > 0) {
            des.parent().find('.count-character').text(charactersLeft);
            des.parent().find('.text-danger').addClass('d-none');
        } else {
            des.parent().find('.count-character').text(0);
            des.parent().find('.error-max-des').removeClass('d-none');
        }
    }
}

$(function () {
    getCharactersLeft.call($('.description-max-length').get());

    window.dragOverHandler = function (ev) {
        ev.preventDefault();
    }

    window.dropHandler = function (evt, input = null) {
        evt.preventDefault();
        let fileInput = document.getElementById('dropzone-photo');
        if (input) {
            fileInput = document.getElementById(input);
        }
        const dT = new DataTransfer();
        let file = evt.dataTransfer.files[0];
        if (evt.dataTransfer.items[0] && evt.dataTransfer.items[0].kind === 'file')
            file = evt.dataTransfer.items[0].getAsFile();
        dT.items.add(file);
        fileInput.files = dT.files;
        if (input === null) {
            changeHandler(fileInput);
        }
    }

    window.changeHandler = function (thisElement) {
        validateInputFile(thisElement)
    }

    removeHtmlRemainDevice();
    $(document).on('click', `.container-fluid .ipt-has-check`, function () {
        let isCheckAll = $(`.container-fluid .ipt-has-check`).is(':checked');
        $('.container-fluid input.ipt-check-account').prop('checked', isCheckAll);
    });

    $(document).on('change', `.container-fluid input.ipt-check-account`, () => {
        changeCheckAllCheckbox(`.container-fluid input.ipt-check-account`);
    });

    $('.dropzone-content-img').on('click', function () {
        $('#dropzone-photo').click();
    });

    $('.click-text-upload').on('click', function () {
        $('#dropzone-photo').click();
    });

    $('.dropzone-content-img-drop').on('click', function () {
        $('#dropzone-project').click();
    });

    $('.click-text-dropzone-upload').on('click', function () {
        $('#dropzone-project').click();
    });

    $("#form-select-country").select2({
        templateResult: formatStateCountry,
        templateSelection: formatStateCountry,
        dropdownParent: $('.form-select-country-container'),
        selectionCssClass: 'form-select-country',
        width: '100%',
        dropdownAutoWidth: true,
        containerCss: 'form-select-country-container',
        placeholder: "Please choose country",
    });

    $('#form-select-calling-code').select2({
        templateResult: formatStateCountry,
        templateSelection: formatStateCountry,
        dropdownParent: $('.input-phone-number-wrapper'),
        selectionCssClass: 'selection-calling-code',
        width: '100%',
        dropdownAutoWidth: true,
        placeholder: "Please choose calling code",
    });

    $("#form-select-region").select2({
        templateResult: formatResultCategory,
        templateSelection: formatResultCategory,
        dropdownParent: $('.form-select-region-container'),
        selectionCssClass: 'form-select-country',
        width: '100%',
        dropdownAutoWidth: true,
        containerCss: 'form-select-country-container',
        placeholder: "Please choose region",
    });

    $("#form-select-timezone").select2({
        templateResult: formatStateCountry,
        templateSelection: formatStateCountry,
        dropdownParent: $('.form-select-timezone-container'),
        selectionCssClass: 'form-select-timezone',
        width: '100%',
        dropdownAutoWidth: true,
        containerCss: 'form-select-country-container',
        placeholder: "Please choose timezone",
    });

    $("#form-select-company").select2({
        templateResult: formatResultCategory,
        templateSelection: formatResultCategory,
        dropdownParent: $('.form-select-company-container'),
        selectionCssClass: 'form-select-company',
        width: '100%',
        dropdownAutoWidth: true,
        containerCss: 'form-select-timezone-container',
        placeholder: "Please choose business",
    });

    $("#form-select-sector").select2({
        templateResult: formatResultCategory,
        templateSelection: formatResultCategory,
        dropdownParent: $('.form-select-sector-container'),
        selectionCssClass: 'form-select-country',
        width: '100%',
        dropdownAutoWidth: true,
        containerCss: 'form-select-country-container',
        placeholder: "Please choose business sector",
    });

    $("#form-select-categories").select2({
        templateSelection: formatResultCategory,
        dropdownParent: $('.form-select-categories-container'),
        selectionCssClass: 'form-select-timezone',
        width: '100%',
        dropdownAutoWidth: true,
        containerCss: 'form-select-timezone-container',
        placeholder: "Select rpa software",
    });

    $("#form-select-company-representative").select2({
        templateSelection: formatResultCategory,
        dropdownParent: $('.form-select-company-representative-container'),
        selectionCssClass: 'form-select-timezone',
        width: '100%',
        dropdownAutoWidth: true,
        containerCss: 'form-select-timezone-container',
        placeholder: "Please choose user",
    });

    $("#form-select-rpa-experience").select2({
        templateSelection: formatResultCategory,
        dropdownParent: $('.form-select-rpa-experience-container'),
        selectionCssClass: 'form-select-timezone',
        width: '100%',
        dropdownAutoWidth: true,
        containerCss: 'form-select-timezone-container',
        placeholder: "Select a rpa experience",
    });

    $("#form-select-hours").select2({
        templateSelection: formatResultCategory,
        dropdownParent: $('.form-select-hours-container'),
        selectionCssClass: 'form-select-timezone',
        dropdownAutoWidth: true,
        containerCss: 'form-select-timezone-container',
        placeholder: "Select hours",
    });

    $("#form-select-discount-type").select2({
        templateSelection: formatResultCategory,
        dropdownParent: $('.form-select-discount-type-container'),
        selectionCssClass: 'form-select-timezone',
        dropdownAutoWidth: true,
        containerCss: 'form-select-discount-type-container',
        placeholder: "Select types",
    });

    $('#form-select-categories').on('change', function (e) {
        const results = $('[data-select2-manual-id]');

        results.map(r => {
            const color = results[r].getAttribute('data-select2-color');
            const borderColor = results[r].getAttribute('data-select2-border-color');
            const backgroundColor = results[r].getAttribute('data-select2-background-color');
            results[r].parentElement.parentElement.style.color = color;
            results[r].parentElement.parentElement.style.borderColor = borderColor;
            results[r].parentElement.parentElement.style.backgroundColor = backgroundColor;
        })
    });

    $(document).on('keyup', 'textarea.form-input-group', () => {
        let textarea = $('textarea.form-input-group');
        let numberInput = MAX_TEXTAREA - textarea.val().length;
        textarea.parent().find('.text-danger').addClass('d-none');

        if (numberInput > 0) {
            textarea.parent().find('.count-character .number').text(numberInput);
            textarea.parent().find('.text-danger').addClass('d-none');
        } else {
            textarea.parent().find('.count-character .number').text(0);
            textarea.parent().find('.error-max-bio').removeClass('d-none');
        }
    });

    $(document).on('change', '#dropzone-project', function (evt) {
        const index = $('.list-file-uploaded .file-uploaded').length;
        let isValidate = validateFileUpload('dropzone-project');
        if (isValidate && evt.target.files.length > 0) {
            const reader = new FileReader();
            progressUploadTemplate(index, '.list-file-uploaded');
            handleSelectedUpload(reader, 'dropzone-project', index);
            const fileInput = document.getElementById('dropzone-project');
            let file = evt.target.files[0];
            DATA_TRANSFER.items.add(file);
            fileInput.files = DATA_TRANSFER.files;
        }
    });

    $(document).on('click', '.file-uploaded .btn-delete-file:not(".old-file-upload")', function () {
        const fileInput = document.getElementById('dropzone-project');
        const index = $('.file-uploaded .btn-delete-file:not(".old-file-upload")').index($(this));
        $(this).parents('.file-uploaded').remove();
        DATA_TRANSFER.items.remove(index);
        fileInput.files = DATA_TRANSFER.files;
        renderIndexFileUpload();
    });

    $(document).on('drop', '.list-file-uploaded', function (evt) {
        const fileInput = document.getElementById('dropzone-project');
        const index = $('.list-file-uploaded .file-uploaded').length;
        let isValidate = validateFileUpload('dropzone-project');
        if (isValidate && fileInput.files.length > 0) {
            const reader = new FileReader();
            progressUploadTemplate(index, '.list-file-uploaded');
            handleSelectedUpload(reader, 'dropzone-project', index);
            let file = fileInput.files[0];
            DATA_TRANSFER.items.add(file);
            fileInput.files = DATA_TRANSFER.files;
        }
    });

    $(document).on('click', '.btn-delete-file.old-file-upload', function () {
        $(this).parents('.file-uploaded').remove();
        idFileDeleteArr.push($(this).data('id'));
        renderIndexFileUpload();
    });

    $(document).on('click', '#button-save', function () {
        const element = $(this).parents('form').attr('id');
        if (validateFileNameUpload()) {
            submitFormEdit(element);
        }
    });

    window.addEventListener('resize', function () {
        let html = '';
        if ($('.container-fluid.pc-device').css('display') == 'block' && !isPC) {
            html = $(`.container-fluid.mobile-device .table-datas`).html()
            $(`.container-fluid.pc-device .table-datas`).html(html)
            $(`.container-fluid.mobile-device .table-datas`).html('')
        } else if ($('.container-fluid.mobile-device').css('display') == 'block' && isPC) {
            html = $(`.container-fluid.pc-device .table-datas`).html()
            $(`.container-fluid.mobile-device .table-datas`).html(html)
            $(`.container-fluid.pc-device .table-datas`).html('')
        }
        isPC = $('.container-fluid.pc-device').css('display') == 'block'
    });

    $(document).on('change', '#form-select-categories', function () {
        $('.total-categories-selected').text($('#form-select-categories option:selected').length);
    });

    $(document).on('click', '#download-example-cv', function () {
        downloadExampleCv($(this).attr('data-url-cv'), 'Automatorr CV Template for Freelancer.docx');
    })

    $(document).on('click', '.edit-file-name-upload', function () {
        const element = $(this).parent().find('input.name-file');
        if (element.prop('readonly')) {
            const nameOrigin = element.val();
            element.removeAttr('readonly').addClass('border-EAECF0').val('').val(nameOrigin).focus();
        } else {
            element.prop('readonly', true).removeClass('border-EAECF0');
        }
    });

    $(document).on('change', '.project-info input.name-file', function () {
        $(this).val($(this).val().trim());
    });

    $(document).on('click', 'input.has-download[name="file_name[]"][readonly]', function () {
        const action = $(this).data('action').trim() || '';
        if (action) {
            window.open(action, '_blank');
        } else {
            toastr.error('Download file failed.');
        }
    });

    $(document).on('keyup change paste', '.description-max-length', getCharactersLeft);
});
