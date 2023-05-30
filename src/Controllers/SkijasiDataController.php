<?php

namespace NadzorServera\Skijasi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use NadzorServera\Skijasi\Facades\Skijasi;
use NadzorServera\Skijasi\Helpers\ApiResponse;

class SkijasiDataController extends Controller
{
    public function getComponents(Request $request)
    {
        $components = Skijasi::getComponents();
        $component_list = collect($components)->map(function ($component) {
            return [
                'value' => $component,
                'label' => ucfirst(str_replace('_', ' ', $component)),
            ];
        })->toArray();

        $data['components'] = $component_list;

        return ApiResponse::success($data);
    }

    public function getFilterOperators(Request $request)
    {
        $operators = Skijasi::getFilterOperator();

        return ApiResponse::success($operators);
    }

    public function getSupportedTableRelations()
    {
        $table_relations = Skijasi::getSupportedTableRelations();
        $table_relations = collect($table_relations)->map(function ($table_relation) {
            return [
                'value' => $table_relation,
                'label' => ucfirst(str_replace('_', ' ', $table_relation)),
            ];
        })->toArray();

        $data['table_relations'] = $table_relations;

        return ApiResponse::success($data);
    }

    public function getConfigurationGroups()
    {
        $groups = config('skijasi.configuration_groups') ?? [];

        $data['groups'] = $groups;

        return ApiResponse::success($data);
    }
}
