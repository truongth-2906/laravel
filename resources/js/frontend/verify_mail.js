const verify = {
    init: () => {
        let MAX_LENGTH = 1;
        let KEY_BACKSPACE = 8;
        let KEY_ARROW_LEFT = 37;
        let KEY_ARROW_RIGHT = 39;
        let KEY_NUMBER_0 = 48;
        let KEY_NUMBER_9 = 57;
        let KEY_a = 65;
        let KEY_z = 90;
        let KEY_A = 96;
        let KEY_Z = 105;
        $('.digit-group').find('input').each(function () {
            $(this).attr('maxlength', MAX_LENGTH);
            $(this).on('keyup', function (e) {
                let parent = $($(this).parent());
                if (e.keyCode === KEY_BACKSPACE || e.keyCode === KEY_ARROW_LEFT) {
                    let prev = parent.find('input#' + $(this).data('previous'));

                    if (prev.length) {
                        $(prev).select();
                    }
                } else if ((e.keyCode >= KEY_NUMBER_0 && e.keyCode <= KEY_NUMBER_9)
                    || (e.keyCode >= KEY_a && e.keyCode <= KEY_z)
                    || (e.keyCode >= KEY_A && e.keyCode <= KEY_Z)
                    || e.keyCode === KEY_ARROW_RIGHT) {
                    let next = parent.find('input#' + $(this).data('next'));

                    if (next.length) {
                        $(next).select();
                    } else {
                        if (parent.data('autosubmit')) {
                            parent.submit();
                        }
                    }
                }
            });
        });
    }
}

$(document).ready(function () {
    verify.init();
});
