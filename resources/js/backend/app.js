import 'alpinejs';
import flatpickr from "flatpickr";

window.$ = window.jQuery = require('jquery');
window.Swal = require('sweetalert2');
window.EmojiConvertor = require('emoji-js');
window.flatpickr = flatpickr;

// CoreUI
require('@coreui/coreui');

// Boilerplate
require('../plugins');

require('./sidebar');
require('./freelancer');
require('./job');
require('./common');
require('./employer');
require('../auth');
require('./message');
require('./voucher');
