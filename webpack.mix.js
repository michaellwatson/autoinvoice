const mix = require('laravel-mix');
mix.setPublicPath('./');

mix.styles([
    'public/css/bootstrap.min.css',
    'assets/plugins/bootstrap/css/bootstrap.min.css',
    'public/css/bootstrap-theme.min.css',
    'public/css/jquery.datetimepicker.min.css',
    'public/css/ladda-themeless.min.css',
    'public/css/font-awesome.css',
    'public/css/jquery-ui.css',
    'public/css/typeahead.js-bootstrap.css',
    'public/css/toastr.css',
    'public/css/dropzone.min.css',
    'public/css/jquery.datetimepicker.min.css',
	'node_modules/toastr/build/toastr.min.css',
    //'assets/css/style-dash.css',
    //'assets/css/style-cart.css',
    'assets/css/jquery.Jcrop.min.css',
    'assets/css/bootstrap-tokenfield.min.css',
    'assets/css/tokenfield-typeahead.min.css',
    'assets/css/theme/style.css',
    'assets/css/theme/colors/default.css',
    'assets/css/signature-pad.css',
    'assets/css/fileuploader/fileuploader.css',
    'assets/css/parts.css',
    'node_modules/lightbox2/dist/css/lightbox.min.css',
    'assets/css/slider.css',
    'node_modules/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
    'assets/css/custom_style.css',
], 'public/css/app.css').version().browserSync();

//mix.sass('resources/sass/app.scss', 'public/css');

mix.styles([
    'public/js/jquery.min.js',
    'assets/js/popper.min.js',
    'public/js/bootstrap.min.js',
    'public/js/toast.min.js',
    'public/js/moment.min.js',
    'public/js/prism.js',
    'public/js/spin.js',
    'public/js/ladda.js',
    'public/js/popper.min.js',
    'public/js/finance.js',
    'public/js/jquery.sticky.js',
    'public/js/toastr.js',
    'public/js/jquery.datetimepicker.full.min.js',
    'public/js/register.js',
    'public/js/jquery-ui.js',
    'vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js',
    'node_modules/toastr/build/toastr.min.js',
    'assets/js/jquery.Jcrop.min.js',
    'assets/js/jquery.ui.touch-punch.min.js',
    'assets/js/bootstrap-maxlength.min.js',
    'assets/js/signatures.js',
    'assets/js/bootstrap-tokenfield.min.js',
    'public/js/dropzone.min.js',
    'assets/plugins/flot/jquery.flot.js',
    'assets/plugins/flot.tooltip/js/jquery.flot.tooltip.min.js',
    'assets/plugins/flot/jquery.flot.time.js',
    'assets/js/fileuploader.js',
    'node_modules/lightbox2/dist/js/lightbox.min.js',
    'assets/js/bootstrap-slider.js',
    'node_modules/bootstrap-switch/dist/js/bootstrap-switch.min.js',
    'assets/js/crop_edit.js',

], 'public/js/app.js').version().browserSync();

mix.styles([

    'assets/js/script.js',
    'assets/js/messages.js',
    'assets/js/add_client.js',
    'assets/js/users.js',
    'assets/js/roles.js',
    'assets/js/jobs.js',
    'assets/js/personnel.js',
    'assets/js/prefill.js',
    'assets/js/mapping.js',
    'assets/js/pages/client.js',
    'assets/js/pages/job_wrapper.js',
    'assets/js/pages/link.js',
    'assets/js/pages/link_email.js',
    'assets/js/pages/users.js',
    'assets/js/staffs.js',
    'assets/js/pages/staffs.js',

    //'public/js/typeahead.bundle.min.js',
    'assets/js/theme/sticky-kit.min.js',
    'assets/js/theme/custom.min.js',
    //'assets/js/theme/flot-data.js',
    'assets/js/theme/jquery.slimscroll.js',
    'assets/js/theme/sidebarmenu.js',
    'assets/js/theme/waves.js',
    'assets/plugins/bootstrap/js/tether.min.js',
    //'assets/js/theme/flot-data.js',
    'assets/js/signature_pad.umd.js',
    'assets/js/signature_custom.js',
    'assets/js/csv_upload.js',
    //admin
     'assets/js/admin/edit_ds.js',
    

], 'public/js/app.me.js').version().browserSync();
//mix.js('resources/js/app.js', 'public/js')
   