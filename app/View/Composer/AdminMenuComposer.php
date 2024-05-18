<?php

namespace App\View\Composer;

use App\Models\Menu;
use Exception;

class AdminMenuComposer
{

    function compose($view)
    {
        if (!is_null(auth()->user()))
            $view->with('app_menu', $this->getMenu(auth()->user()));
    }


    private function getMenu($user)
    {
        $source =  Menu::where('deleted_at', null);

        if ($user->role_id != 1)
            $source = $source->where('role_ids', 'like', '%' . $user->role_id . '%');

        $source = $source->orderBy('parent')
            ->orderBy('order')
            ->get()
            ->toArray();

        // Get Label Menu
        $menus = array_filter($source, function ($item) {
            return $item['type'] == 'label' && $item['parent'] == 0;
        });

        // Get Main Menu
        foreach ($menus as $indexlabel => $label) {
            $label['sub'] = array_filter($source, function ($item) use ($label) {
                return $item['type'] == 'menu' && $item['parent'] == $label['id'];
            });
            $label['name'] = strtoupper($label['name']);
            $label['padding'] = ($indexlabel == 0) ? 'padding: 0rem 1rem .5rem !important;' : '';
            $menus[$indexlabel] = $label;

            // Get Sub Menu
            foreach ($label['sub'] as $indexmain => $main) {
                $main['sub'] = array_filter($source, function ($item) use ($main) {
                    return $item['type'] == 'menu' && $item['parent'] == $main['id'];
                });
                $main['link'] = $this->generateLink($main['link']);
                $activemain = request()->url() == $main['link'];
                $activesub = !empty(array_filter($main['sub'], function ($item) {
                    return request()->url() == $this->generateLink($item['link']);
                }));
                $main['active'] = $activemain || $activesub  ? 'active' : '';
                $main['open'] = $activesub ? 'menu-open' : '';
                $main['subclass'] = empty($main['sub']) ? '' : 'has-treeview';
                $menus[$indexlabel]['sub'][$indexmain] = $main;

                // Get Active Sub
                foreach ($main['sub'] as $indexsub => $sub) {
                    $sub['link'] = $this->generateLink($sub['link']);
                    $sub['active'] = request()->url() == $sub['link'] ? 'active' : '';
                    $menus[$indexlabel]['sub'][$indexmain]['sub'][$indexsub] = $sub;
                }
            }
        }

        return json_decode(json_encode($menus));
    }

    private function generateLink($link)
    {
        if (is_null($link))
            return "#";

        if (preg_match("/http/", $link))
            return $link;

        if (str_starts_with($link, '/'))
            return url($link);

        try {
            return route($link);
        } catch (Exception $e) {
            return url('/' . $link);
        }
    }
}
