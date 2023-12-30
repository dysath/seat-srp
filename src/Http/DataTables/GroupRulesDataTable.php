<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015 to 2021 Leon Jacobs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace Denngarr\Seat\SeatSrp\Http\DataTables;

use Denngarr\Seat\SeatSrp\Models\AdvRule;
use Seat\Eveapi\Models\Sde\InvType;
use Yajra\DataTables\Services\DataTable;

/**
 * Class MembersDataTable.
 *
 * @package Seat\Web\Http\DataTables\Squads
 */
class GroupRulesDataTable extends DataTable
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax(): \Illuminate\Http\JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('group', function ($row) {
                // Performance penalty here for an aesthetic most will likely never notice
                $type = InvType::where('groupID', $row->group->groupID)->inRandomOrder()->first();

                return view('web::partials.type', ['type_id' => $type->typeID, 'type_name' => $row->group->groupName])->render();
            })
            ->editColumn('action', fn($row) => view('srp::buttons.ruleremove', ['row' => $row])->render())
            ->editColumn('deduct_insurance', fn($row): string => $row->deduct_insurance > 0 ? 'Yes' : 'No')
            ->rawColumns(['group', 'action'])
            ->toJson();
    }

    /**
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->postAjax()
            ->columns($this->columns())
            ->parameters([
                'drawCallback' => 'function() { $("[data-toggle=tooltip]").tooltip(); }',
            ])
            ->addAction();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return AdvRule::where('rule_type', 'group');
    }

    /**
     * @return array
     */
    public function columns(): array
    {
        return [
            // ['data' => 'type', 'title' => 'Type'],
            ['data' => 'price_source', 'title' => 'Price Source'],
            ['data' => 'base_value', 'title' => 'Base Value'],
            ['data' => 'hull_percent', 'title' => 'Hull Percent'],
            ['data' => 'fit_percent', 'title' => 'Fit Percent'],
            ['data' => 'cargo_percent', 'title' => 'Cargo Percent'],
            ['data' => 'srp_price_cap', 'title' => 'Price Cap'],
            ['data' => 'deduct_insurance', 'title' => 'Insurance Deducted'],
        ];
    }
}
