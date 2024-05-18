@extends('master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Master Hak Akses Fitur</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Fitur</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-1">

        <div class="container-fluid">

            <div class="card card-default">
                <form action="{{ url('system/feature/map') }}" id="form-feature">
                    {{ csrf_field() }}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <select id="roles" class="select2 form-control" name="role_id">
                                    <option value="-">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        @if ($role->id != 1)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12" id="select-container" style="display: none">
                                <div class="form-group">
                                    <select class="duallistbox" multiple="multiple" name="features"></select>
                                </div>
                                <button class="btn btn-primary float-right" type="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Hak Akses</th>
                                <th>Fitur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td class="role" data-id="{{ $role->id }}">
                                        {{ $role->id == 1 ? 'Akses Seluruh Fitur' : $role->permissions }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </section>
@endsection

@section('css')
    <style>
        .moveall,
        .removeall {
            display: none !important;
        }

        .move {
            margin-left: 0px !important;
            border-top-left-radius: .25rem !important;
        }

        .remove {
            border-top-right-radius: .25rem !important;
        }

        .filter {
            margin-bottom: 0.75rem !important;
        }

        select {
            border: 1px solid #6c757d !important;
        }
    </style>
@endsection

@section('js')
    <script>
        let selectFeatures;
        let origin;

        $('document').ready(function() {

            selectFeatures = $('select[name=features]').bootstrapDualListbox({
                selectorMinimalHeight: 275,
                filterTextClear: 'Tampilkan semua',
                filterPlaceHolder: 'Cari',
                infoText: 'Menampilkan {0} data',
                infoTextFiltered: '<span class="label label-warning">Menampilkan</span> {0} dari {1}',
                infoTextEmpty: 'Data tidak ada',
                removeAllLabel: '',
                moveOnSelect: false,
            })

            $('#form-feature').formHandler({
                rules: {
                    role_id: {
                        required: true,
                        number: true
                    },
                },
                message: {
                    role_id: {
                        required: 'Role wajib diisi',
                        number: 'Role harus berupa numerik'
                    }
                }
            }, mapFeature);

            $('#roles').on('change', function() {
                let id = $(this).val();
                if (isNaN(id)) {
                    $('#select-container').slideUp();
                } else {
                    loadData(id);
                }
            });
        });

        function loadData(role) {
            $.ajax({
                url: url('/system/feature/source/' + role),
                beforeSend: function() {
                    $('#select-container').slideUp();
                },
                success: function(payload, message, xhr) {
                    selectFeatures.empty();
                    payload.data.options.forEach(item => {
                        let option =
                            `<option ${(item.selected) ? 'selected' : ''}>${item.name}</option>`
                        selectFeatures.append(option);
                    })
                    selectFeatures.bootstrapDualListbox('refresh');
                    $('#select-container').slideDown();
                }
            })
        }

        function mapFeature(form) {
            const submitButton = $('#form-feature button[type=submit]');
            let data = $(form).serializeObject();
            if (!Array.isArray(data.features))
                data.features = [data.features];
            $.ajax({
                url: $(form).attr('action'),
                data: data,
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
                        $('#roles').select2('val', '-');
                        $('#select-container').slideUp();
                        $(`.role[data-id=${data.role_id}]`).text(data.features.join());
                    }
                },
                error: function(xhr, message, error) {
                    let payload = xhr.responseJSON
                    showMessage(payload.message, 'error')
                },
                complete: function(data) {
                    loading(submitButton, false)
                    enable(submitButton);
                }
            });
        }
    </script>
@endsection
