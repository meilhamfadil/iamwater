const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css')

mix.scripts([
    'node_modules/admin-lte/plugins/jquery/jquery.js',
    'node_modules/admin-lte/dist/js/adminlte.js',
    'node_modules/admin-lte/plugins/moment/moment.min.js',
    'node_modules/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js',
    'node_modules/admin-lte/plugins/datatables/jquery.dataTables.js',
    'node_modules/admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.js',
    'node_modules/admin-lte/plugins/datatables-responsive/js/dataTables.responsive.js',
    'node_modules/admin-lte/plugins/datatables-responsive/js/responsive.bootstrap4.js',
    'node_modules/admin-lte/plugins/select2/js/select2.full.js',
    'node_modules/admin-lte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.js',
    'node_modules/admin-lte/plugins/inputmask/jquery.inputmask.js',
    'node_modules/admin-lte/plugins/daterangepicker/daterangepicker.js',
    'node_modules/admin-lte/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js',
    'node_modules/admin-lte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.js',
    'node_modules/admin-lte/plugins/bootstrap-switch/js/bootstrap-switch.js',
    'node_modules/admin-lte/plugins/toastr/toastr.min.js',
    'node_modules/admin-lte/plugins/jquery-validation/jquery.validate.min.js',
    'node_modules/admin-lte/plugins/jquery-validation/additional-methods.min.js',
    'node_modules/jquery-confirm/dist/jquery-confirm.min.js',
    'node_modules/sortablejs/Sortable.js'
], 'public/js/plugin.js')
    .sourceMaps();

mix.styles([
    'node_modules/admin-lte/plugins/fontawesome-free/css/all.css',
    'node_modules/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
    'node_modules/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
    'node_modules/admin-lte/plugins/sweetalert2/sweetalert2.css',
    'node_modules/admin-lte/plugins/toastr/toastr.css',
    'node_modules/admin-lte/plugins/daterangepicker/daterangepicker.css',
    'node_modules/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.css',
    'node_modules/admin-lte/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css',
    'node_modules/admin-lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.css',
    'node_modules/admin-lte/plugins/select2/css/select2.css',
    'node_modules/admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.css',
    'node_modules/admin-lte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.css',
    'node_modules/jquery-confirm/dist/jquery-confirm.min.css',
    'node_modules/admin-lte/dist/css/adminlte.css',
], 'public/css/plugin.css')
    .sourceMaps();

mix.copy(
    'node_modules/admin-lte/dist/css/adminlte.css.map',
    'public/css/adminlte.css.map'
).copy(
    'node_modules/admin-lte/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js.map',
    'public/js/bootstrap-colorpicker.js.map'
).copy(
    'node_modules/admin-lte/plugins/toastr/toastr.js.map',
    'public/js/toastr.js.map'
);