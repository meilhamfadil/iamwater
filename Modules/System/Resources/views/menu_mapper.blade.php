@extends('master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Menu Mapper</h1>
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
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aturan</h3>
                        </div>
                        <div class="card-body">
                            Level 1
                            <ul>
                                <li>Menu akan bertipe label grup</li>
                            </ul>
                            <hr>
                            Level 2
                            <ul>
                                <li>Menu dapat berupa aksi menuju 1 halaman</li>
                                <li>Menu dengan target # tidak memiliki aksi</li>
                                <li>Menu dengan anak akan menjadi grup</li>
                            </ul>
                            <hr>
                            Level 3
                            <ul>
                                <li>Menu merupakan sebuah aksi</li>
                            </ul>
                            <hr>
                            Unmapped
                            <ul>
                                <li>Tidak akan ada submenu</li>
                                <li>Semua akan disejajarkan di unmapped</li>
                            </ul>
                            <hr>
                            <hr>
                            General
                            <ul>
                                <li>
                                    Hitam : Text Menu
                                </li>
                                <li>
                                    <span class="text-primary">Biru : Target Menu</span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <span class="text-danger">Untuk mapping lakukan Drag & Drop</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Mapped</h3>
                            <div class="card-tools">
                                <button type="button" id="save" class="btn btn-tool close-form">
                                    <i class="fas fa-save"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="nested-main" class="list-group col nested-sortable">
                                @foreach ($menus as $label)
                                    <div data-sortable-id="{{ $label->id }}" class="list-group-item nested-1">
                                        <span>{{ $label->name }}</span>
                                        <small class="text-primary">{{ $label->link }}</small>
                                        <div class="list-group nested-sortable">
                                            @foreach ($label->sub as $main)
                                                <div data-sortable-id="{{ $main->id }}"
                                                    class="list-group-item nested-2">
                                                    <span>{{ $main->name }}</span>
                                                    <small class="text-primary">{{ $main->link }}</small>
                                                    <div class="list-group nested-sortable">
                                                        @foreach ($main->sub as $sub)
                                                            <div data-sortable-id="{{ $sub->id }}"
                                                                class="list-group-item nested-3">
                                                                {{ $sub->name }}
                                                                <small class="text-primary">{{ $sub->link }}</small>
                                                                <div class="list-group nested-sortable"></div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Unmapped</h3>
                        </div>
                        <div class="card-body">
                            <div id="nested-side" class="list-group col nested-sortable">
                                @foreach ($menus_unmapped as $label)
                                    <div data-sortable-id="{{ $label->id }}" class="list-group-item nested-1">
                                        {{ $label->name }} <small class="text-primary">{{ $label->link }}</small>
                                        <div class="list-group nested-sortable"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <style>
        ul,
        li {
            margin: 0;
            padding: 0;
        }

        li {
            list-style: none
        }

        .list-group .list-group-item {
            background: #ffffff;
        }

        .list-group .list-group-item .list-group .list-group-item {
            background: #f6f6f6;
        }

        .list-group .list-group-item .list-group .list-group-item .list-group .list-group-item {
            background: #ffffff;
        }

        .list-moving-class {
            background: #e9e9e9 !important;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            var nestedSortables = [].slice.call($('.nested-sortable'));
            var draggedDepth = 0;

            for (var i = 0; i < nestedSortables.length; i++) {
                new Sortable(nestedSortables[i], {
                    group: {
                        name: 'nested',
                        pull: function(to, from) {
                            var toLvl = $(to.el).parents('.nested-sortable').length;
                            if (toLvl > 2) {
                                return false;
                            }
                            if (draggedDepth > 0) {
                                return false;
                            }
                            return true;
                        }
                    },
                    animation: 150,
                    ghostClass: 'list-moving-class',
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onMove: function(evt, originalEvent) {

                    },
                });
            }

            $('#save').on('click', function() {
                const data = {
                    main: rearangeData(getSortableData(document.getElementById('nested-main'))),
                    side: rearangeData(getSortableData(document.getElementById('nested-side')))
                }
                $.ajax({
                    url: url('system/menu/map'),
                    ...getHeaderToken(),
                    data: data,
                    type: POST,
                    dataType: JSON_DATA,
                    success: function(payload, message, xhr) {
                        showMessage(
                            payload.message,
                            payload.code == 200 ? 'success' : 'error'
                        )
                    },
                    complete: function(data) {
                        location.reload()
                    }
                })
            });
        });

        function rearangeData(serialized) {
            return serialized.reduce(function(acc, label) {
                acc.push({
                    parent: '0',
                    id: label.id
                });
                label.children.forEach(function(main) {
                    acc.push({
                        parent: label.id,
                        id: main.id
                    });
                    main.children.forEach(function(sub) {
                        acc.push({
                            parent: main.id,
                            id: sub.id
                        });
                    });
                });
                return acc.sort(menuSortComparison);
            }, []);
        }

        function menuSortComparison(a, b) {
            if (a.parent < b.parent)
                return -1;
            if (a.parent > b.parent)
                return 1;
            return 0;
        }

        function getSortableData(sortable) {
            const nestedQuery = '.nested-sortable';
            const identifier = 'sortableId';
            var serialized = [];
            var children = [].slice.call(sortable.children);
            for (var i in children) {
                var nested = children[i].querySelector(nestedQuery);
                serialized.push({
                    id: children[i].dataset[identifier],
                    children: nested ? getSortableData(nested) : []
                });
            }
            return serialized;
        }
    </script>
@endsection
