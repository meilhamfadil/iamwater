<?php

namespace Modules\Condition\Http\Controllers;

use App\Http\Controllers\AdminController;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Kreait\Firebase\Factory;

class ConditionController extends AdminController
{

    private $rules = [
        [[0, 0], [70, 0], [90, 1], [100, 1]],
        [[0, 0], [50, 0], [70, 1], [90, 0], [100, 0]],
        [[0, 0], [35, 0], [50, 1], [70, 0], [100, 0]],
        [[0, 0], [25, 0], [35, 1], [50, 0], [100, 0]],
        [[0, 1], [25, 1], [35, 0], [100, 0]],
    ];

    public function index(Request $request)
    {
        $refId = $request->query('ref_id');
        $data = DB::table('condition_process')->where('condition_id', $refId)->first();
        $detail = DB::table('condition_area')->where('condition_id', $refId)->get();

        if ($data == null) {
            return "Data has not been processed";
        }

        $all = generateWholeArea(
            $data->category_sangat_buruk,
            $data->category_buruk,
            $data->category_sedang,
            $data->category_baik,
            $data->category_sangat_baik
        );

        [$areas] = $this->calculate($all);
        $this->content['all'] = $all;
        $this->content['area'] = $areas;
        $this->content['rules'] = $this->rules;
        $this->content['data'] = $data;
        $this->content['detail'] = $detail;
        return view('condition::index', $this->content);
    }

    public function graph(Request $request)
    {
        $types = $request->query('types');
        $date = $request->query('date');
        $node = $request->query('node');

        $this->content['type'] = $types;
        $this->content['graph'] = array_map(
            function ($data) use ($types) {
                $time = date("Y-m-d H:i:s", strtotime($data->created_at . ' + 7 hours'));
                return [
                    "x" => $time,
                    "y" => $data->$types,
                ];
            },
            DB::table('condition')
                ->where("source_id", $node)
                ->whereRaw("DATE(created_at) = '$date'")
                ->orderByDesc('created_at')
                ->get()
                ->toArray()
        );
        return view('condition::graph', $this->content);
    }

    public function nodes()
    {
        return DB::table('sources')
            ->orderByDesc('created_at')
            ->get();
    }

    public function histories(Request $request)
    {
        $refId = $request->query('ref_id');
        $date = $request->query('date');
        $data = DB::table('condition')
            ->leftJoin("condition_process", 'condition.ref_id', 'condition_process.condition_id')
            ->where("source_id", $refId)
            ->whereRaw("DATE(condition.created_at) = '$date'")
            ->orderByDesc('condition.created_at')
            ->get(['condition.*', 'condition_process.output'])
            ->toArray();

        return array_map(function ($m) {
            $time = date("Y-m-d H:i:s", strtotime($m->created_at . ' + 7 hours'));
            return [
                "source_id" => $m->source_id,
                "ph" => $m->ph,
                "metals" => $m->metals,
                "oxygen" => $m->oxygen,
                "particles" => $m->particles,
                "created_at" => $time,
                "updated_at" => $m->updated_at,
                "ref_id" => $m->ref_id,
                "output" => $m->output,
            ];
        }, $data);
    }

    public function detail(Request $request)
    {
        $refId = $request->query('ref_id');
        $data = DB::table('condition')->where('ref_id', $refId)->first();
        $preProcess = DB::table('condition_process')->where('condition_id', $refId)->first();
        $calculation = DB::table('condition_area')->where('condition_id', $refId)->get();
        return [
            'data' => $data,
            'pre_process' => $preProcess,
            'calculation' => $calculation,
        ];
    }

    public function preProcess(Request $request)
    {
        try {
            $refId = $request->query('ref_id');
            $data = DB::table('condition')->where('ref_id', $refId)->first();

            // Initialization
            $ph = $data->ph;
            $metal = $data->metals;
            $oxygen = $data->oxygen;
            $tds = $data->particles;

            // Calculate PH
            $phAsam = phAsam($ph);
            $phBaik = phBaik($ph);
            $phBasa = phBasa($ph);

            // Calculate Metal
            $metalBaik = metalBaik($metal);
            $metalSedang = metalSedang($metal);
            $metalBuruk = metalBuruk($metal);

            // Calculate Oxygen
            $oxygenBaik = oxygenBaik($oxygen);
            $oxygenCukup = oxygenCukup($oxygen);
            $oxygenBuruk = oxygenBuruk($oxygen);

            // Calculate TDS
            $tdsBaik = tdsBaik($tds);
            $tdsSedang = tdsSedang($tds);
            $tdsBuruk = tdsBuruk($tds);

            // Categorize
            $sangatBuruk = max(
                min($phAsam, $metalBuruk, $oxygenCukup, $tdsSedang),
                min($phAsam, $metalBuruk, $oxygenCukup, $tdsBuruk),
                min($phAsam, $metalBuruk, $oxygenBuruk, $tdsSedang),
                min($phAsam, $metalBuruk, $oxygenBuruk, $tdsBuruk),
                min($phBasa, $metalBuruk, $oxygenCukup, $tdsBuruk),
                min($phBasa, $metalBuruk, $oxygenBuruk, $tdsSedang),
                min($phBasa, $metalBuruk, $oxygenBuruk, $tdsBuruk),
            );
            $buruk = max(
                min($phAsam, $metalBaik, $oxygenCukup, $tdsBaik),
                min($phAsam, $metalBaik, $oxygenCukup, $tdsSedang),
                min($phAsam, $metalBaik, $oxygenBuruk, $tdsSedang),
                min($phAsam, $metalBaik, $oxygenBuruk, $tdsBuruk),
                min($phAsam, $metalSedang, $oxygenCukup, $tdsSedang),
                min($phAsam, $metalSedang, $oxygenCukup, $tdsBuruk),
                min($phAsam, $metalSedang, $oxygenBuruk, $tdsSedang),
                min($phAsam, $metalSedang, $oxygenBuruk, $tdsBuruk),
                min($phBaik, $metalBuruk, $oxygenCukup, $tdsBuruk),
                min($phBasa, $metalBaik, $oxygenCukup, $tdsSedang),
                min($phBasa, $metalBaik, $oxygenCukup, $tdsBuruk),
                min($phBasa, $metalBaik, $oxygenBuruk, $tdsSedang),
                min($phBasa, $metalBaik, $oxygenBuruk, $tdsBuruk),
                min($phBasa, $metalSedang, $oxygenCukup, $tdsSedang),
                min($phBasa, $metalSedang, $oxygenCukup, $tdsBuruk),
                min($phBasa, $metalSedang, $oxygenBuruk, $tdsSedang),
                min($phBasa, $metalSedang, $oxygenBuruk, $tdsBuruk),
                min($phBasa, $metalBuruk, $oxygenCukup, $tdsSedang),
            );
            $sedang = max(
                min($phBaik, $metalBuruk, $oxygenBaik, $tdsBaik),
                min($phBaik, $metalBuruk, $oxygenBaik, $tdsSedang),
                min($phBaik, $metalBuruk, $oxygenCukup, $tdsBaik),
                min($phBaik, $metalBuruk, $oxygenCukup, $tdsSedang),
            );
            $baik = max(
                min($phBaik, $metalBaik, $oxygenCukup, $tdsSedang),
                min($phBaik, $metalSedang, $oxygenBaik, $tdsSedang),
                min($phBaik, $metalSedang, $oxygenCukup, $tdsBaik),
                min($phBaik, $metalSedang, $oxygenCukup, $tdsSedang),
            );
            $sangatBaik = max(
                min($phBaik, $metalBaik, $oxygenBaik, $tdsBaik),
                min($phBaik, $metalBaik, $oxygenBaik, $tdsSedang),
                min($phBaik, $metalBaik, $oxygenCukup, $tdsBaik),
                min($phBaik, $metalSedang, $oxygenBaik, $tdsBaik),
            );

            $all = generateWholeArea($sangatBuruk, $buruk, $sedang, $baik, $sangatBaik);

            DB::table('condition_area')->where('condition_id', $refId)->delete();
            DB::table('condition_process')->where('condition_id', $refId)->delete();

            [$areas, $totalArea, $totalMomen, $areaData, $momenData] = $this->calculate($all);

            DB::table('condition_process')->insert([
                "condition_id" => $refId,
                "ph_asam" => $phAsam,
                "ph_baik" => $phBaik,
                "ph_basa" => $phBasa,
                "metal_baik" => $metalBaik,
                "metal_sedang" => $metalSedang,
                "metal_buruk" => $metalBuruk,
                "oxygen_baik" => $oxygenBaik,
                "oxygen_cukup" => $oxygenCukup,
                "oxygen_buruk" => $oxygenBuruk,
                "tds_baik" => $tdsBaik,
                "tds_sedang" => $tdsSedang,
                "tds_buruk" => $tdsBuruk,
                "category_sangat_buruk" => $sangatBuruk,
                "category_buruk" => $buruk,
                "category_sedang" => $sedang,
                "category_baik" => $baik,
                "category_sangat_baik" => $sangatBaik,
                "area" => $totalArea,
                "momen" => $totalMomen,
                "output" => ($totalArea == 0) ? 0 : $totalMomen / $totalArea,
            ]);


            foreach ($areaData as $index => $conArea) {
                [$left, $right, $upper, $lower] = explode('-', $index);
                DB::table('condition_area')->insert([
                    "condition_id" => $refId,
                    "x_start" => ($left == $right) ? $upper : $lower,
                    "y_start" => $left,
                    "x_end" => ($left == $right) ? $lower : $upper,
                    "y_end" => $right,
                    "area" => $conArea,
                    "momen" => $momenData[$index],
                ]);
            }


            return "OK";
        } catch (Exception $e) {
            return $e;
        }
    }

    private function calculate($all)
    {
        // Generating all area point
        $area = [];
        $tempArea = [];
        $tempPeakPoint = $all[0][1];
        foreach ($all as $point) {
            if ($tempPeakPoint != $point[1]) {
                array_push($area, $tempArea);
                $tempArea = [];
                array_push($tempArea, $point);
            } else {
                array_push($tempArea, $point);
            }
            $tempPeakPoint = $point[1];
        }
        array_push($area, $tempArea);

        // Get all rectangles from area
        $rectangles = [];
        $temp = [];
        foreach ($area as $itemArea) {
            $temp = $itemArea;
            usort($temp, function ($a, $b) {
                return $a[0] <=> $b[0];
            });
            $min = $temp[0];
            $max = array_pop($temp);
            array_push($rectangles, [$min, $max]);
        }

        // Get all trapezoids from area
        $trapezoids = [];
        foreach ($rectangles as $index => $rect) {
            if ($index > 0) {
                $ax = $rect[0][0];
                $ay = $rect[0][1];
                $bx = $rectangles[$index - 1][1][0];
                $by = $rectangles[$index - 1][1][1];
                array_push($trapezoids, [[$ax, $ay], [$bx, $by]]);
            }
        }

        // Filter Zero Area
        $unfiltered = [...$rectangles, ...$trapezoids];
        $filteredAreas = array_values(array_filter($unfiltered, function ($item) {
            return bccomp($item[0][1], 0, 3) == 1 || bccomp($item[1][1], 0, 3) == 1;
        }));

        // Calculate Area
        $areaData = [];
        $totalArea = 0;
        foreach ($filteredAreas as $a) {
            $left = $a[0][1];
            $right = $a[1][1];
            $upper = $a[0][0];
            $lower = $a[1][0];
            $areaValue = 0;

            if ($left != $right) {
                // Trapezoid
                $areaValue = (($left + $right) / 2) * abs($upper - $lower);
            } else {
                // Rectangle
                $areaValue = $left * ($lower - $upper);
            }
            $areaData[$left . '-' . $right . '-' . $upper . '-' . $lower] = $areaValue;
            $totalArea = $totalArea + $areaValue;
        }

        // Calculate Momen
        $momenData = [];
        $totalMomen = 0;
        foreach ($filteredAreas as  $a) {
            $left = $a[0][1];
            $right = $a[1][1];
            $upper = $a[0][0];
            $lower = $a[1][0];
            $momenValue = 0;

            if ($left != $right) {
                // Trapezoid
                if ($lower >= 25 && $upper <= 35) {
                    $momenValue = $right > $left
                        ? moment1($upper) - moment1($lower) : moment2($upper) - moment2($lower);
                } elseif ($lower >= 35 && $upper <= 50) {
                    $momenValue = $right > $left
                        ? moment3($upper) - moment3($lower) : moment4($upper) - moment4($lower);
                } elseif ($lower >= 50 && $upper <= 70) {
                    $momenValue = $right > $left
                        ? moment5($upper) - moment5($lower) : moment6($upper) - moment6($lower);
                } elseif ($lower >= 70 && $upper <= 90) {
                    $momenValue = $right > $left
                        ? moment7($upper) - moment7($lower) : moment8($upper) - moment8($lower);
                }
            } else {
                // Rectangle
                $a = $left * 0.5 * pow($upper, 2);
                $b = $left * 0.5 * pow($lower, 2);
                $momenValue = abs($a - $b);
            }
            $momenData[$left . '-' . $right . '-' . $upper . '-' . $lower] = $momenValue;
            $totalMomen = $totalMomen + $momenValue;
        }

        return [$filteredAreas, $totalArea, $totalMomen, $areaData, $momenData];
    }

    public function n_crud()
    {
        return view('condition::ncrud', $this->content);
    }

    public function n_datatable()
    {
        $query = DB::table('sources');
        return DataTables::of($query)->toJson();
    }

    public function n_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $result = null;
        if ($request->post('id') != null) {
            $result = DB::table('sources')->where('id', $request->post('id'))
                ->update([
                    'name' => $request->post('name'),
                    'description' => $request->post('description'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $result = DB::table('sources')->insert([
                'name' => $request->post('name'),
                'description' => $request->post('description'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->responseJson($result);
    }


    public function n_destroy(Request $request)
    {
        $id = $request->post('id');
        $result = DB::table('sources')->where('id', $id)->delete();
        return $this->responseJson(
            $result,
            ($result == 0) ? 'Gagal menghapus data' : 'Data berhasil dihapus',
            ($result == 0) ? 400 : 200,
            ($result == 0) ? 400 : 200
        );
    }

    public function h_crud()
    {
        $this->content['nodes'] = $this->nodes();
        return view('condition::hcrud', $this->content);
    }

    public function h_datatable()
    {
        $query = DB::table('condition')->join("sources", "condition.source_id", "=", "sources.id");
        return DataTables::of($query)->toJson();
    }

    public function h_store(Request $request)
    {
        $request->validate([
            'ph' => 'required',
            'metals' => 'required',
            'oxygen' => 'required',
            'particles' => 'required',
        ]);

        $result = null;
        if ($request->post('id') != null) {
            $result = DB::table('condition')->where('ref_id', $request->post('id'))
                ->update([
                    'source_id' => $request->post('source_id'),
                    'ph' => $request->post('ph'),
                    'metals' => $request->post('metals'),
                    'oxygen' => $request->post('oxygen'),
                    'particles' => $request->post('particles'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $result = DB::table('condition')->insertGetId([
                'source_id' => $request->post('source_id'),
                'ph' => $request->post('ph'),
                'metals' => $request->post('metals'),
                'oxygen' => $request->post('oxygen'),
                'particles' => $request->post('particles'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $client = new Client();
            $client->request('GET', 'http://akuasih.my.id/condition/api/calculate?ref_id=' . $result);
        }

        return $this->responseJson($result);
    }


    public function h_destroy(Request $request)
    {
        $id = $request->post('id');
        $result = DB::table('sources')->where('id', $id)->delete();
        return $this->responseJson(
            $result,
            ($result == 0) ? 'Gagal menghapus data' : 'Data berhasil dihapus',
            ($result == 0) ? 400 : 200,
            ($result == 0) ? 400 : 200
        );
    }

    public function sync(Request $request)
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount([
                    "type" => "service_account",
                    "project_id" => "akuasih2-bd0a3",
                    "private_key_id" => "7b54585c282b1baf53c43cc694f5c2a3fdb18b02",
                    "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCSgpdBRJ8XWLJW\nPnWnWmKTT4vK+Xk2L7Y69Kicwn/5BP/r0+DQVsBYuTOfn7i0RX9DNgYzFk5bEnXI\n3vWrY7E2+Gg4fo5BB1Nu9l+4BbdxFn2E70nXLEj+1MgA8Qdlvkq1yD80U9CkA5rb\n7HIVfhGa/bc4JtqXfSZ4GxoSkSY8ucbjbvPlgCMn2GxNCNEF99sDv8qw2qNs9H4u\nh8VzLA/umTgCWyP1upuaSDoi3XyFV7PdgXseymgNY4yiDKMhOdm/JxeVyfFKdRHI\nfuZPmqFBk+x9wOJYka6VVwaUtvDpBH0dsa8hQhtq3XK3uDn5JcuS5R0/+dpWY+OO\nhijwukOdAgMBAAECggEAA4ETHBEHmxfYgB6TUtLjG7Hh3pNz9jtS6PsUE1fFls+r\nTVkufd0tUZHwbad00/77icB/inZEnoIFBi5rAtPqF63+VCeWwHBr6vICnRSp7A8F\nJopQolBhWJL/S6J4MlNIVyu4c0hz65BYgl0X53Kx+hqKaNHpFYl9Q48mYv5Mw1QG\n7R1Y8OCHZGR/B12aAN4N3dKoVR5zmyn3UQMzBZkZvrFskiGR+Llq7fR9mlesPxp7\nPjFFHoqo2xGUtQHQ9BJvAuKDTYlKUam6pcinNmtGslv4R5AhVrlJi/6A8hY9STqm\nJfvgv6eWeGg4+2gtkT96a8xIvJH7OnoEOp3gt0dgUwKBgQDFtnpbZG9WsJgayACn\ny2K/SXm/BKV+QrrOWDvJdcaEMiQNgxXui0f7wOd24YIYS4/1zlixuIF3BY/PhEUt\nMwTL5wFy8iFzAI2Qs+7SvvBCJtXRqTjgPsmWRPRJpuV5eB1Bb6lKJm0UMOQiPjB9\n6QP8tkJCmn7r2KeDeIbT9oe0ewKBgQC9s8+YYsZyADYK02cmyL2njCHtVQgIZve1\nuFNT+CaH7pGKRhYTJxjwI/IGKTj/qKKxyDIgZZYLSZKcmg9TpvAJ9FC6byLsX0qB\nZ6Ro9+JJ7shz6hqaAn0BpgmTmEqWUCsxc2XGJfmCXly2CXflo66PXjHqGosNHroO\nKRaM41FoxwKBgQC8EjHgH/C9MOzvhNMCyjibp4QVDZFzQny6bjopEEyPUbbz138U\nVA9cToqfjjIXdEFz3B6Ip+8XTgYXq0W2kjJ817iMJAFniN4hCNgkRpb7BkAc2XEN\n9wwBUoRiT004N+b0aQhLTbQzIbLRVAECtJYjXSg4fQhAxu3J5Ou0U22RYwKBgCqH\ntqIeewkx/Ou+a6DpXoPCyhRwfOWNWDnYgm4P82uEVALhJa/Tkya7mFZDRbEjuJ4N\nGRfkTphnPUR40bjac3R33uV9ZyIBVy3d86FI+eXDcBN0x9QBfM8yz3DUstwySwzC\nJ24eM2tEBpsVUlkcslUYNC6dFtGxMttb4N9jU2wfAoGAVzeR26W46+SJhcYMr5Yc\nmSlSYpwo/hpCXupqLzK+9aReRzveLdUDNvj0hhVmWUQ1ZxMQO3ll77F+n9lqY4ul\nQKyWUnuMmKSs41kJDz5LPdHJVrc35pWziQfzoUlmdt9tqkG5Nkwx7tWzBIHD8ri2\nA85Lr+1ZeFfJlqZcg00PQrg=\n-----END PRIVATE KEY-----\n",
                    "client_email" => "firebase-adminsdk-k4x4d@akuasih2-bd0a3.iam.gserviceaccount.com",
                    "client_id" => "101460172811256485585",
                    "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
                    "token_uri" => "https://oauth2.googleapis.com/token",
                    "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
                    "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-k4x4d%40akuasih2-bd0a3.iam.gserviceaccount.com",
                    "universe_domain" => "googleapis.com"
                ])
                ->withDatabaseUri('https://akuasih2-bd0a3-default-rtdb.asia-southeast1.firebasedatabase.app');
            $database = $factory->createDatabase();

            $node1 = $database->getReference('Node1');
            $data1 = $node1->getValue();
            $id1 = DB::table('sources')->where('name', 'Node1')->first()->id;
            DB::table('condition')->insert([
                'source_id' => $id1,
                'ph' => $data1['pH'],
                'metals' => $data1['EC'],
                'oxygen' => $data1['DO'],
                'particles' => $data1['TDS'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $node2 = $database->getReference('Node2');
            $data2 = $node2->getValue();
            $id2 = DB::table('sources')->where('name', 'Node2')->first()->id;
            DB::table('condition')->insert([
                'source_id' => $id2,
                'ph' => $data2['pH'],
                'metals' => $data2['EC'],
                'oxygen' => $data2['DO'],
                'particles' => $data2['TDS'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return "OK";
        } catch (Exception $e) {
            return $e;
        }
    }
}
