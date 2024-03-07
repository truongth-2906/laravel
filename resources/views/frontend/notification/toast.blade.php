<div class="position-fixed bottom-0 right-0 notify-toast-wrapper">
    <div class="suggestion-top"></div>
    <div id="notify-toast-container"></div>
    <div class="suggestion-bottom"></div>
</div>

<script type="text/template" data-template="notify-toast-template">
    <div id="notify-toast-${id}" class="toast hide notify-toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="${delay}" data-id="${id}">
        <div class="toast-header">
            <img src="${icon}" alt="">
            <button type="button" class="btn-action cursor-pointer hover-button except reading trigger" data-dismiss="toast" aria-label="Close">
                <img src="{{ asset('img/icon-times.svg') }}" alt="">
            </button>
        </div>
        <div class="toast-body">
            <div class="notify-summary-info">
                <div class="notify-summary-info__title">
                    ${title}
                </div>
                <div class="notify-summary-info__content">
                    ${content}
                </div>
                <div class="notify-summary-info__action">
                    ${actions}
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" data-template="notify-actions-template">
    <a href="${route}" class="d-flex except reading trigger">
        <span>
            ${title}
        </span>
        <img src="{{ asset('img/icon-arrow-right.svg') }}" alt="" class="ml-2">
    </a>
</script>
