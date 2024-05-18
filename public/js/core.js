$.extend($.fn.dataTable.defaults, {
    serverSide: true,
    processing: true,
    ajax: {
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: function (d) {
            var data = $.extend({}, d, (typeof buildDatatableParam === 'function') ? buildDatatableParam() : {});
            return data;
        }
    },
    dom: '<"row"<"col-6"l><"col-6 text-right"f>>t<"row"<"col-6"i><"col-6 text-right"p>>r',
    responsive: true,
    autoWidth: false,
    lengthMenu: [
        [5, 10, 25, 50, -1],
        [5, 10, 25, 50, 'Semua'],
    ],
    language: {
        decimal: '',
        emptyTable: 'Data tidak tersedia',
        info: 'Menampilkan data _START_ sampai _END_ dari total _TOTAL_ data',
        infoEmpty: 'Menampilkan data kosong',
        infoFiltered: '',
        infoPostFix: '',
        thousands: '.',
        lengthMenu: 'Menampilkan _MENU_ data',
        loadingRecords: 'Memuat...',
        processing: 'Memuat...',
        search: 'Cari:',
        zeroRecords: 'Data tidak ditemukan',
        paginate: {
            first: 'Awal',
            last: 'Akhir',
            next: 'Lanjut',
            previous: 'Kembali'
        },
        aria: {
            'sortAscending': ': pengurutan diaktifkan',
            'sortDescending': ': pengurutan terbalik diaktifkan'
        }
    }
});

$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

$.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "No space please and don't leave it empty");

$.fn.formHandler = function (
    validator = {},
    action = function () {
        showMessage('You haven\'t register callback yet', 'error')
    }
) {
    this.validate({
        ...validator,
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
            element.closest('.input-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function (form) {
            action(form)
        }
    })
}

$("document").ready(function () {

    $(".select2").select2({
        minimumResultsForSearch: -1,
        theme: 'bootstrap4'
    });

    $(".select2-findable").select2({
        theme: 'bootstrap4'
    });

});

function showMessage(message, type = 'success', body = null) {
    let temp = null
    if (body !== null) {
        temp = message;
        message = body;
        body = temp;
    }

    switch (type) {
        case 'warning':
            toastr.warning(message, body)
            break;
        case 'error':
            toastr.error(message, body)
            break;
        case 'info':
            toastr.info(message, body)
            break;
        default:
            toastr.success(message, body)
            break;
    }
}

function disable(view) {
    view.attr('disabled', 'disabled')
}

function enable(view) {
    view.removeAttr('disabled')
}

let loadingContentHolder = ''

function loading(view, show) {
    if (show) {
        loadingContentHolder = view.html()
        view.html(`<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; display: block; shape-rendering: auto; animation-play-state: running; animation-delay: 0s;" width="18px" height="18px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
        <circle cx="50" cy="50" fill="none" stroke="#c3c3c3" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" style="animation-play-state: running; animation-delay: 0s;">
          <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1" style="animation-play-state: running; animation-delay: 0s;"></animateTransform>
        </circle></svg>`);
    } else {
        view.html(loadingContentHolder);
    }
}

function swap(origin, target, simultaneously = true) {
    let originObject = (typeof origin === 'string') ? $(origin) : origin
    let targetObject = (typeof target === 'string') ? $(target) : target
    if (simultaneously) {
        originObject.slideUp();
        targetObject.slideDown();
    } else {
        originObject.slideUp(function () {
            targetObject.slideDown();
        });
    }
}

function redirect(target) {
    if (!target.startsWith('/'))
        target = '/' + target;
    $(location).prop('href', APP_URL + target);
}

function url(target) {
    if (!target.startsWith('/'))
        target = '/' + target;
    return APP_URL + target;
}

$.fn.setOnActionClickListener = function (
    action
) {
    $(this).on('click', '.datatable-action', function () {
        const dataSource = $(this).parent().data('data');
        const data = JSON.parse(decodeURI(dataSource));
        const type = $(this).data('type');
        action(type, data)
    });
}

function tableAction(data, option = {}, label = 'Aksi') {
    return `<div class="container-action">
        <span class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
            ${label} <span class="caret"></span>
        </span>
        <div class="dropdown-menu" data-data="${encodeURI(JSON.stringify(data))}">
            ${option()}            
        </div>
    </div>`;
}

function actionOption(
    label,
    type
) {
    return `<a class="dropdown-item datatable-action" data-type="${type}" href="#">${label}</a>`;
}

function actionDivider() {
    return `<div class="dropdown-divider"></div>`;
}

function getHeaderToken() {
    return {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }
}