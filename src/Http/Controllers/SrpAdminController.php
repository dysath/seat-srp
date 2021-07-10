<?php

namespace Denngarr\Seat\SeatSrp\Http\Controllers;

use Denngarr\Seat\SeatSrp\Http\DataTables\GroupRulesDataTable;
use Denngarr\Seat\SeatSrp\Http\DataTables\TypeRulesDataTable;
use Denngarr\Seat\SeatSrp\Models\AdvRule;
use Denngarr\Seat\SeatSrp\Models\KillMail;
use Denngarr\Seat\SeatSrp\Validation\AddReason;
use Denngarr\Seat\SeatSrp\Validation\ValidateAdvancedSettings;
use Denngarr\Seat\SeatSrp\Validation\ValidateRule;
use Denngarr\Seat\SeatSrp\Validation\ValidateSettings;
use Seat\Eveapi\Models\Sde\InvGroup;
use Seat\Eveapi\Models\Sde\InvType;
use Seat\Web\Http\Controllers\Controller;

class SrpAdminController extends Controller
{
    public function srpGetKillMails()
    {
        $killmails = KillMail::where('approved', '>', '-2')->orderby('created_at', 'desc')->get();

        return view('srp::list', compact('killmails'));
    }

    public function srpApprove($kill_id, $action)
    {
        $killmail = KillMail::find($kill_id);

        switch ($action) {
            case 'Approve':
                $killmail->approved = '1';
                break;
            case 'Reject':
                $killmail->approved = '-1';
                break;
            case 'Paid Out':
                $killmail->approved = '2';
                break;
            case 'Pending':
                $killmail->approved = '0';
                break;
            case 'Delete':
                $killmail->approved = '99';
        }

        $killmail->approver = auth()->user()->name;
        $killmail->save();

        return json_encode(['name' => $action, 'value' => $kill_id, 'approver' => auth()->user()->name]);
    }

    public function srpAddReason(AddReason $request)
    {

        $kill_id = $request->input('srpKillId');

        $killmail = Killmail::find($kill_id);

        if (is_null($killmail))
        return redirect()->back()
            ->with('error', trans('srp::srp.not_found'));

        $reason = $killmail->reason();
        if (! is_null($reason))
            $reason->delete();

        KillMail::addNote($request->input('srpKillId'), 'reason', $request->input('srpReasonContent'));

        return redirect()->back()
                         ->with('success', trans('srp::srp.note_updated'));
    }

    public function getSrpSettings()
    {
        $rules = AdvRule::all();

        $groups = InvGroup::where('categoryID', 6)->get();
        $types = InvType::whereIn('groupID', $groups->pluck('groupID')->all())->get();

        $type_rules = $rules->where('rule_type', 'type');
        $group_rules = $rules->where('rule_type', 'group');

        return view('srp::settings', compact(['groups', 'types', 'type_rules', 'group_rules']));
    }

    public function saveSrpSettings(ValidateSettings $request)
    {
        setting(['denngarr_seat_srp_webhook_url', $request->webhook_url], true);
        setting(['denngarr_seat_srp_mention_role', $request->mention_role], true);
        setting(['denngarr_seat_srp_advanced_srp', $request->srp_method], true);

        return redirect()->back()->with('success', 'SRP Settings have successfully been updated.');
    }

    public function saveSrpRule(ValidateRule $request)
    {

        // logger()->error($request->rule_type);

        // $e = AdvRule::where('type_id', $request->type_id)
        //     ->where('rule_type', $request->rule_type)
        //     ->get();
        // if ($e->count() > 0) { // Only an issue for now. In future want to update existing
        //     // We are updating an exisiting row
        //     return response()->json(['message' => 'Entry already exists for this type'], 400);
        // }

        $rule = AdvRule::updateOrCreate([
            'rule_type' => $request->rule_type,
            'type_id' => $request->type_id,
            'group_id' => $request->group_id,
        ]);

        $rule->update([
            'price_source' => $request->source,
            'base_value' => $request->base_value,
            'hull_percent' => $request->hull_percent,
            'cargo_percent' => $request->cargo_percent,
            'fit_percent' => $request->fit_percent,
            'deduct_insurance' => $request->deduct_insurance,
        ]);

        $rule->save();

        return response('Added/Updated Type Rule', 200);
    }

    public function removeSrpRule(AdvRule $rule)
    {
        $rule->delete();

        return redirect()->back()->with('success', 'Rule successfully removed');
    }

    public function typesData(TypeRulesDataTable $datatable)
    {
        return $datatable->render('srp::settings');
    }

    public function groupsData(GroupRulesDataTable $datatable)
    {
        return $datatable->render('srp::settings');
    }

    public function saveAdvDefaultSettings(ValidateAdvancedSettings $request)
    {

        setting(['denngarr_seat_srp_advrule_def_source', $request->default_source], true);
        setting(['denngarr_seat_srp_advrule_def_base', $request->default_base], true);
        setting(['denngarr_seat_srp_advrule_def_hull', $request->default_hull_pc], true);
        setting(['denngarr_seat_srp_advrule_def_fit', $request->default_fit_pc], true);
        setting(['denngarr_seat_srp_advrule_def_cargo', $request->default_cargo_pc], true);

        $insurance = 1;
        if (is_null($request->default_ins)) {
            $insurance = 0;
        }

        setting(['denngarr_seat_srp_advrule_def_ins', $insurance], true);

        return redirect()->back()->with('success', 'SRP Settings have successfully been updated.');
    }

    public function getTestView()
    {
        return view('srp::srptest');
    }

    public function runDeletions()
    {
        $deleted = KillMail::where('approved', 99)->delete();
        logger()->info('Deleted ' . $deleted . ' killmails from SRP table');

        return json_encode(['deleted' => $deleted]);
    }
}
