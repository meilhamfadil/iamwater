@extends('master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Master Menu</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Blank Page</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-1">

        <div class="container-fluid">

            <div class="card" id="container-form" style="display: none">
                <div class="card-header">
                    <h3 class="card-title">Form Menu</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool close-form">
                            <i class="fas fa-times"></i></button>
                    </div>
                </div>
                <form action="{{ url('system/menu/store') }}" id="form-menu">
                    {{ csrf_field() }}
                    <input type="hidden" name="id">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Label</label>
                            <input type="text" name="name" class="form-control" placeholder="Label Menu">
                        </div>
                        <div class="form-group">
                            <label>Tipe:</label>
                            <select name="type" class="form-control select2" style="width: 100%;">
                                <option value="menu">Menu</option>
                                <option value="label">Label</option>
                            </select>
                        </div>
                        <div class="form-group" id="form-target">
                            <label>Target:</label>
                            <select name="target" class="form-control select2" style="width: 100%;">
                                <option value="_self">Tab Terbuka</option>
                                <option value="_blank">Tab Baru</option>
                            </select>
                        </div>
                        <div class="form-group" id="form-icon">
                            <label>Icon</label>
                            <input type="text" name="icon" class="form-control" placeholder="Font Awesome Icon Class">
                        </div>
                        <div class="form-group" id="form-link">
                            <label>Link</label>
                            <br>
                            <div class="form-check d-inline mr-2">
                                <input type="radio" name="link_type" value="endpoint">&nbsp;Endpoint
                            </div>
                            <div class="form-check d-inline mr-2">
                                <input type="radio" name="link_type" value="link">&nbsp;Link
                            </div>
                            <div class="form-check d-inline mr-2">
                                <input type="radio" name="link_type" value="feature">&nbsp;Feature
                            </div>
                            <div class="mb-2"></div>
                            <div class="input-group mb-3" style="display: none" id="endpoint">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">/</span>
                                </div>
                                <input type="text" name="endpoint" class="form-control" placeholder="Endpoint">
                            </div>
                            <div class="input-group mb-3" style="display: none" id="link">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fab fa-edge"></i>
                                    </span>
                                </div>
                                <input type="text" name="link" class="form-control" placeholder="Url">
                            </div>
                            <div id="feature" style="display: none;">
                                <select name="feature" class="form-control select2" style="width: 100%;">
                                    @foreach ($features as $feature)
                                        <option>{{ $feature }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-right">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="card" id="container-filter" style="display: none">
                <div class="card-header">
                    <h3 class="card-title">Filter</h3>
                </div>
                <div class="card-body">
                    <form id="form-filter">
                        <div class="container">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Tipe:</label>
                                        <select name="datatable[type]" class="form-control select2" style="width: 100%;">
                                            <option value="">Semua</option>
                                            <option value="label">Label</option>
                                            <option value="menu">Menu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Parent:</label>
                                        <select name="datatable[parent]" class="form-control select2-parent"
                                            style="width: 100%;"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card" id="container-table">
                <div class="card-header">
                    <h3 class="card-title">Menu</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool filter">
                            <i class="fas fa-filter"></i></button>
                        <button type="button" class="btn btn-tool add">
                            <i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td class="text-center">No</td>
                                <td class="text-center">Nama</td>
                                <td class="text-center">Parent Menu</td>
                                <td class="text-center">Link / Target</td>
                                <td class="text-center">Type</td>
                                <td class="text-center">Status</td>
                                <td class="text-center">Aksi</td>
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
        const roles = JSON.parse('{!! $roles !!}');
        let datatable;
        $('document').ready(function() {
            datatable = $('table').DataTable({
                ajax: {
                    url: "/system/menu/datatable"
                },
                columns: [{
                        data: 'id',
                        render: function(data, index, row, meta) {
                            return `${meta.row + 1}`
                        }
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'parent',
                        render: function(data, index, row, meta) {
                            return (data === null) ? '-' : data.name;
                        }
                    },
                    {
                        data: 'link',
                        render: function(data, index, row, meta) {
                            return (data === null) ? '#' : data;
                        }
                    },
                    {
                        data: 'type',
                        width: '40px',
                        className: 'text-center'
                    },
                    {
                        data: 'pid',
                        width: '40px',
                        className: 'text-center',
                        render: function(data, index, row, meta) {
                            return data == -1 ? 'Unmapped' : 'Mapped'
                        }
                    },
                    {
                        data: 'id',
                        width: '40px',
                        render: function(data, index, row, meta) {
                            return tableAction(row, function() {
                                return actionOption('Ubah', 'edit') +
                                    actionOption('Hapus', 'remove') +
                                    actionDivider() +
                                    actionOption('Hak Akses', 'role');
                            });
                        }
                    }
                ],
                rowCallback: function(row, data, index) {
                    $('td:eq(0)', row).css('width', '15px');
                    $('td:eq(0)', row).css('text-align', 'center');
                }
            });

            $('table').setOnActionClickListener(function(type, data) {
                switch (type) {
                    case 'edit':
                        $('input[name=id]').val(data.id)
                        $('input[name=name]').val(data.name)
                        $('input[name=icon]').val(data.icon)
                        if (data.link == null) {
                            $('#form-target').slideUp()
                            $('#form-icon').slideUp()
                            $('#form-link').slideUp()
                        } else {
                            $('#form-target').slideDown()
                            $('#form-icon').slideDown()
                            $('#form-link').slideDown()
                        }

                        if (data.link != null && data.link.includes('http')) {
                            $('input[name=link]').val(data.link)
                            $('input[value=link]').attr('checked', 'checked')
                            $('#link').show();
                            $('#endpoint').hide();
                            $('#feature').hide();
                        } else if (data.link != null && data.link.startsWith('/')) {
                            $('input[name=endpoint]').val(data.link)
                            $('input[value=endpoint]').attr('checked', 'checked')
                            $('#link').hide();
                            $('#endpoint').show();
                            $('#feature').hide();
                        } else if (data.link != null) {
                            $('input[name=feature]').val(data.link)
                            $('input[value=feature]').attr('checked', 'checked')
                            $('#link').hide();
                            $('#endpoint').hide();
                            $('#feature').show();
                        }
                        $('select[name=type]').val(data.type).trigger('change')
                        $('select[name=target]').val(data.target).trigger('change')
                        swap('#container-table', '#container-form')
                        break;
                    case 'remove':
                        showDeleteConfirmation(data);
                        break;
                    case 'role':
                        const target = url('system/menu/role');
                        let content = '';
                        content += `<form action="${target}" id="form-role">` +
                            `<input type="hidden" name="id" value="${data.id}"/>`;
                        roles.forEach(function(item) {
                            content += `<div class="form-group">` +
                                `<div class="custom-control custom-checkbox">` +
                                `<input class="custom-control-input" type="checkbox" id="cb${item.id}" name="role_ids[]" value="${item.id}">` +
                                `<label for="cb${item.id}" class="custom-control-label">${item.name}</label>` +
                                `</div>` +
                                `</div>`;
                        });
                        content += `</form>`;
                        $.confirm({
                            title: 'Hak Akses',
                            content: content,
                            buttons: {
                                ya: {
                                    text: 'Simpan',
                                    btnClass: 'btn-primary',
                                    action: function() {
                                        const valid = $('#form-role').valid();
                                        if (valid)
                                            $("#form-role").submit();
                                        return valid;
                                    }
                                },
                                batal: {
                                    text: 'Batal'
                                }
                            },
                            onContentReady: function() {
                                $('input:checkbox').removeAttr('checked');
                                roles.forEach(function(item) {
                                    console.log(item.id, data.role_ids)
                                    if (data.role_ids != null) {
                                        if (`,${item.id},`.includes(data.role_ids))
                                            $('#cb' + item.id).attr('checked',
                                                'checked')
                                    }
                                });
                                $('#form-role').formHandler({}, updateRole)
                            }
                        });
                        break;
                }
            });

            $('.filter').on('click', function() {
                $('#container-filter').toggle(500, 'swing')
            });

            $('.add').on('click', function() {
                swap('#container-table', '#container-form')
            });

            $('.close-form').on('click', function() {
                swap('#container-form', '#container-table')
            });

            $('#form-filter').on('change', 'select', function() {
                datatable.ajax.reload();
            });

            $('.select2-parent').select2({
                ajax: {
                    url: url('system/menu/source'),
                    dataType: 'json'
                }
            });

            $('select[name=type]').on('change', function() {
                if ($(this).val() == 'label') {
                    $('#form-target').slideUp()
                    $('#form-icon').slideUp()
                    $('#form-link').slideUp()
                } else {
                    $('#form-target').slideDown()
                    $('#form-icon').slideDown()
                    $('#form-link').slideDown()
                }
            });

            $('input[name=link_type]').on('change', function() {
                switch ($(this).val()) {
                    case 'link':
                        $('#link').show();
                        $('#endpoint').hide();
                        $('#feature').hide();
                        break;
                    case 'endpoint':
                        $('#link').hide();
                        $('#endpoint').show();
                        $('#feature').hide();
                        break;
                    case 'feature':
                        $('#link').hide();
                        $('#endpoint').hide();
                        $('#feature').show();
                        break;
                }
            });

            $('#form-menu').formHandler({
                rules: {
                    name: {
                        required: true
                    },
                    type: {
                        required: true
                    },
                    link_type: {
                        required: function(element) {
                            return $('input[name=type]').val() == 'menu';
                        }
                    },
                    link: {
                        url: true,
                        required: function(element) {
                            return $('input[name=link_type]:checked').val() == 'link';
                        }
                    },
                    endpoint: {
                        required: function(element) {
                            console.log($('input[name=link_type]').val())
                            return $('input[name=link_type]:checked').val() == 'endpoint';
                        }
                    },
                    feature: {
                        required: function(element) {
                            console.log($('input[name=link_type]').val())
                            return $('input[name=link_type]:checked').val() == 'feature';
                        }
                    }
                }
            }, storeMenu)
        });

        function buildDatatableParam() {
            return $('#form-filter').serializeObject();
        }

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

        function storeMenu(form) {
            const submitButton = $('#form-menu button[type=submit]')
            $.ajax({
                url: $(form).attr('action'),
                data: $(form).serializeObject(),
                type: POST,
                dataType: JSON_DATA,
                before: function() {
                    loading(submitButton, true);
                    disable(submitButton);
                },
                success: function(payload, message, xhr) {
                    if (payload.code == 200) {
                        showMessage(message)
                        $('#form-menu')[0].reset();
                        swap('#container-form', '#container-table');
                    } else {
                        showMessage(message, 'error');
                    }
                },
                error: function(xhr, message) {
                    showMessage(message, 'error')
                },
                complete: function(payload) {
                    loading(submitButton, false);
                    enable(submitButton);
                    datatable.ajax.reload();
                }
            })
        }

        function updateRole(form) {
            $.ajax({
                url: $(form).attr('action'),
                ...getHeaderToken(),
                data: $(form).serializeObject(),
                type: POST,
                dataType: JSON_DATA,
                success: function(payload, message, xhr) {
                    if (payload.code == 200) {
                        showMessage(message)
                        swap('#container-form', '#container-table');
                    } else {
                        showMessage(message, 'error');
                    }
                },
                error: function(xhr, message) {
                    showMessage(message, 'error')
                },
                complete: function(payload) {
                    datatable.ajax.reload();
                }
            })
        }

        function removeData(id) {
            $.ajax({
                url: url('system/menu/remove'),
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
    </script>
@endsection
