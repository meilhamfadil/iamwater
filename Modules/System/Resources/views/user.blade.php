@extends('master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Master Pengguna</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Pengguna</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-1">

        <div class="container-fluid">

            <div class="card" id="container-form" style="display: none">
                <div class="card-header">
                    <h3 class="card-title">Form Pengguna</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool close-form">
                            <i class="fas fa-times"></i></button>
                    </div>
                </div>
                <form action="{{ url('system/user/store') }}" id="form-user">
                    {{ csrf_field() }}
                    <input type="hidden" name="id">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Pengguna">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email Pengguna">
                        </div>
                        <div class="form-group">
                            <label>Hak Akses Pengguna</label>
                            <select name="role_id" class="form-control select2">
                                <option value="">Pilih Hak Akses</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <p class="m-0">Semua password akan disetting secara default.</p>
                            <small>Default Password : <span class="text-danger">password</span></small>
                        </div>
                        <button type="submit" class="btn btn-primary float-right">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="card" id="container-table">
                <div class="card-header">
                    <h3 class="card-title">Pengguna</h3>
                    @can('isSuperadmin')
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool add">
                                <i class="fas fa-plus"></i></button>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td class="text-center">No</td>
                                <td class="text-center">Nama</td>
                                <td class="text-center">Email</td>
                                <td class="text-center">Role</td>
                                <td class="text-center"></td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </section>
@endsection


@section('js')
    <script>
        let datatable;

        $('document').ready(function() {

            datatable = $('table').DataTable({
                ajax: {
                    url: "/system/user/datatable"
                },
                autoWidth: false,
                columns: [{
                        data: 'id',
                        width: '40px',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, index, row, meta) {
                            return `${meta.row + 1}`
                        }
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email',
                        render: function(data, index, row, meta) {
                            return `<a href="mailto:${data}">${data}</a>`;
                        }
                    },
                    {
                        data: 'role.name'
                    },
                    {
                        data: 'id',
                        width: '40px',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, index, row, meta) {
                            return tableAction(row, function() {
                                return actionOption('Ubah', 'edit') +
                                    actionOption('Hapus', 'remove') +
                                    actionDivider() +
                                    actionOption('Ganti Password', 'password');
                            });
                        }
                    }
                ]
            });

            $('table').setOnActionClickListener(function(type, data) {
                switch (type) {
                    case 'edit':
                        prepareEdit(data);
                        break;
                    case 'remove':
                        showDeleteConfirmation(data);
                        break;
                    case 'password':
                        showPasswordDialog(data);
                        break;
                }
            });

            $('.add').on('click', function() {
                swap('#container-table', '#container-form');
            });

            $('.close-form').on('click', function() {
                swap('#container-form', '#container-table');
            });

            $('input[name=name]').on('change keyup', function() {
                $('input[name=slug]').val($(this).val().replace(/\s/g, ''));
            });

            $('#form-user').formHandler({
                rules: {
                    name: {
                        required: true,
                    },
                    email: VALIDATOR.email,
                    role_id: {
                        required: true,
                    }
                },
                messages: {
                    name: {
                        required: 'Nama wajib diisi'
                    },
                    email: VALIDATOR_MESSAGES.email,
                    role_id: {
                        required: 'Hak akses wajib dipilih'
                    },
                },
            }, doStore)

        });

        function showDeleteConfirmation(data) {
            $.confirm({
                title: 'Hapus Data',
                content: `Anda yakin akan menghapus data ${data.name}?`,
                buttons: {
                    ya: {
                        text: "Hapus",
                        btnClass: 'btn-danger',
                        action: function() {
                            removeData(data.id)
                        }
                    },
                    tidak: {
                        text: "Tidak"
                    }
                }
            });
        }

        function removeData(id) {
            $.ajax({
                url: url('system/user/remove'),
                ...getHeaderToken(),
                data: {
                    id: id
                },
                type: DELETE,
                dataType: JSON_DATA,
                success: function(payload, message, xhr) {
                    showMessage(
                        payload.message,
                        (payload.code == 200) ? 'success' : 'error'
                    )
                },
                error: function(xhr, message, error) {
                    let payload = xhr.responseJSON
                    showMessage(payload.message, 'error')
                },
                complete: function(data) {
                    datatable.ajax.reload(null, false);
                }
            })
        }

        function prepareEdit(data) {
            $('input[name=id]').val(data.id);
            $('input[name=name]').val(data.name);
            $('input[name=email]').val(data.email);
            $('select[name=role_id]').select2('val', data.role_id.toString());
            swap('#container-table', '#container-form');
        }

        function doStore(form) {
            const submitButton = $('#form-user button[type=submit]');
            $.ajax({
                url: $(form).attr('action'),
                data: $(form).serializeObject(),
                type: POST,
                dataType: JSON_DATA,
                beforeSend: function() {
                    disable(submitButton)
                    loading(submitButton, true)
                },
                success: function(payload, message, xhr) {
                    showMessage(
                        payload.message,
                        (payload.code == 200) ? 'success' : 'error'
                    )
                    if (payload.code == 200) {
                        $('#form-user')[0].reset();
                        $('select[name=role_id]').select2('val', '');
                        swap('#container-form', '#container-table');
                    }
                },
                error: function(xhr, message, error) {
                    let payload = xhr.responseJSON
                    showMessage(payload.message, 'error')
                },
                complete: function(data) {
                    loading(submitButton, false)
                    enable(submitButton);
                    datatable.ajax.reload(null, false);
                }
            });
        }

        function showPasswordDialog(data) {
            const target = url('system/user/password');
            $.confirm({
                title: 'Ganti Password',
                content: `<form action="${target}" id="form-password">` +
                    `<input type="hidden" name="id" value="${data.id}"/>` +
                    `<div class="form-group">` +
                    `<input type="password" placeholder="Password Baru" name="password" class="form-control" />` +
                    `</div>` +
                    `<div class="form-group">` +
                    `<input type="password" placeholder="Ulangi Password" name="retype" class="form-control"/>` +
                    `</div>` +
                    `</form>`,
                buttons: {
                    ya: {
                        text: 'Ubah Password',
                        btnClass: 'btn-primary',
                        action: function() {
                            const valid = $('#form-password').valid();
                            if (valid)
                                $("#form-password").submit();
                            return valid;
                        }
                    },
                    batal: {
                        text: 'Batal'
                    }
                },
                onContentReady: function() {
                    $('#form-password').formHandler({
                        rules: {
                            password: {
                                required: true
                            },
                            retype: {
                                required: true,
                                equalTo: 'input[name=password]'
                            }
                        },
                        messages: {
                            password: {
                                required: "Password wajib diisi"
                            },
                            retype: {
                                required: "Password wajib diulangi",
                                equalTo: "Password harus sama"
                            }
                        }
                    }, changePassword)
                }
            });
        }

        function changePassword(form) {
            $.ajax({
                url: $(form).attr('action'),
                ...getHeaderToken(),
                data: $(form).serializeObject(),
                type: POST,
                dataType: JSON_DATA,
                success: function(payload, message, xhr) {
                    showMessage(
                        payload.message,
                        (payload.code == 200) ? 'success' : 'error'
                    )
                },
                error: function(xhr, message, error) {
                    let payload = xhr.responseJSON
                    showMessage(payload.message, 'error')
                },
                complete: function(data) {
                    datatable.ajax.reload(null, false);
                }
            });
        }
    </script>
@endsection
