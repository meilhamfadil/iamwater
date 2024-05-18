 <!-- Main Sidebar Container -->
 <aside class="main-sidebar sidebar-dark-primary elevation-4">

     <!-- Brand Logo -->
     <a href="{{ url('') }}" class="brand-link">
         <img src="/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
         <span class="brand-text font-weight-light">Base Project</span>
     </a>
     <!-- Sidebar -->
     <div class="sidebar">
         <!-- Sidebar user (optional) -->
         <div class="user-panel mt-3 pb-3 mb-3 d-flex">
             <div class="image">
                 <img src="/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
             </div>
             <div class="info">
                 <a href="#" class="d-block">{{ auth()->user() != null ? auth()->user()->name : '' }}</a>
             </div>
         </div>

         <!-- Sidebar Menu -->
         <nav class="mt-2">
             <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                 data-accordion="false">

                 @php
                     $appmenu = isset($app_menu) ? $app_menu : [];
                 @endphp
                 @foreach ($appmenu as $label)
                     <li class="nav-header" style="{{ $label->padding }}">{{ $label->name }}</li>
                     @foreach ($label->sub as $menu)
                         <li class="nav-item {{ $menu->subclass }} {{ $menu->open }}">
                             <a href="{{ $menu->link }}" class="nav-link {{ $menu->active }}"
                                 target="{{ $menu->target }}">
                                 <i class="nav-icon {{ $menu->icon }}"></i>
                                 <p>
                                     {{ $menu->name }}
                                     @if (!empty($menu->sub))
                                         <i class="right fas fa-angle-left"></i>
                                     @endif
                                 </p>
                             </a>
                             @if (!empty($menu->sub))
                                 <ul class="nav nav-treeview">
                                     @foreach ($menu->sub as $sub)
                                         <li class="nav-item ml-3">
                                             <a href="{{ $sub->link }}" class="nav-link {{ $sub->active }}"
                                                 target="{{ $sub->target }}">
                                                 <i class="far fa-circle nav-icon"></i>
                                                 <p>{{ $sub->name }}</p>
                                             </a>
                                         </li>
                                     @endforeach
                                 </ul>
                             @endif
                         </li>
                     @endforeach
                 @endforeach

             </ul>
         </nav>
         <!-- /.sidebar-menu -->
     </div>
     <!-- /.sidebar -->
 </aside>
