@extends('master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Master Hak Akses</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Hak Akses</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-1">

        <div class="container-fluid">

            <div class="card" id="container-form" style="display: none">
                <div class="card-header">
                    <h3 class="card-title">Form Hak Akses</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool close-form">
                            <i class="fas fa-times"></i></button>
                    </div>
                </div>
                <form action="{{ url('system/role/store') }}" id="form-role">
                    {{ csrf_field() }}
                    <input type="hidden" name="id">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Akses">
                        </div>
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" name="slug" class="form-control" placeholder="Slug" readonly>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary float-right">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="card" id="container-table">
                <div class="card-header">
                    <h3 class="card-title">Hak Akses</h3>
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
                                <td class="text-center">Slug</td>
                                <td class="text-center">Deskripsi</td>
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
                    url: "/system/role/datatable"
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
                        data: 'slug'
                    },
                    {
                        data: 'description'
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
                                    actionDivider();
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

            $('#form-role').formHandler({
                rules: {
                    name: {
                        required: true,
                    },
                    slug: {
                        required: true,
                    }
                },
                messages: {
                    name: {
                        required: 'Nama wajib diisi'
                    },
                    slug: {
                        required: 'Slug wajib diisi'
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
                url: url('system/role/remove'),
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
            $('input[name=slug]').val(data.slug);
            $('textarea[name=description]').val(data.description);
            swap('#container-table', '#container-form');
        }

        function doStore(form) {
            const submitButton = $('#form-role button[type=submit]');
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
                        $('#form-role')[0].reset();
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
    </script>
@endsection
