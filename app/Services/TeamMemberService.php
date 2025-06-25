<?php

namespace App\Services;

use App\Enums\TeamPosition;
use App\Exceptions\MemberException;
use App\Http\Utility\ConvertUtility;
use App\Models\Position;
use App\Models\TeamMember;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamMemberService
{
 /**
     * Create a member.
     */
    public function create(array $data): string
    {
        $returnMessage = __('success.member.selected');
        $member = $this->getMember($data['user_id']);
        if ($member) {
            throw new BadRequestHttpException(__('error.member.have_team'));
        }

        $latestNumber = $this->getLatestNumber($data['team_id']);
        TeamMember::create([
            'team_id' => $data['team_id'],
            'user_id' => $data['user_id'],
            'team_position' => TeamPosition::MEMBER->value,
            'sequence' => $latestNumber + 1
        ]);

        return $returnMessage;
    }

    /**
     * Update a member.
     */
    public function update(array $data, $discontinue = false): string
    {
        $returnMessage = __('success.member.selected');
        $member = $this->getMember($data['user_id']);

        if (!$member) {
            throw new BadRequestHttpException(__('error.member.no_team'));
        }

        // Check if the user is a leader. If the user is a leader, they must delete the team before the update.
        if ($member->team_position == TeamPosition::LEADER->value && !$discontinue) {
            throw new MemberException(__('error.member.leader'));
        }

        // Check if the team has one member, they must delete the team before the update.
        $count = $this->countOnlyMember($member['user_id'], $member['team_id']);
        if ($count <= 0 && !$discontinue) {
            throw new MemberException(__('error.member.team_min_members'));
        }

        // Update member to another team
        $oldTeam = $member->team_id;
        $newTeam = $data['team_id'];

        $latestNumber = $this->getLatestNumber($newTeam);
        $member->update([
            'team_id' => $data['team_id'],
            'user_id' => $data['user_id'],
            'team_position' => TeamPosition::MEMBER->value,
            'sequence' => $latestNumber + 1
        ]);
        $this->reIndex($oldTeam, $member['sequence']);

        // Delete or disband the team when the user is a leader or when the team has member less than 1
        if (($member->team_position == TeamPosition::LEADER->value || $count <= 0) && $discontinue) {
            $teamService = new TeamService();
            $teamService->delete($member->team);
            $returnMessage = __('success.team.deleted');
        }

        return $returnMessage;
    }

    /**
     * Delete a member.
     */
    public function delete(array $data, $discontinue = false): string
    {
        $returnMessage = __('success.member.leaved');
        $member = $this->getMember($data['user_id']);
        if (!$member) {
            throw new BadRequestHttpException(__('error.member.no_team'));
        }

        $teamId = $member->team_id;
        $deleteNumber = $member->sequence;

        // Check if the user is a leader. If the user is a leader, they must delete the team before the delete.
        if ($member->team_position == TeamPosition::LEADER->value && !$discontinue) {
            throw new MemberException(__('error.member.leader'));
        }

        // Check if the team has one member, they must delete the team before the delete.
        $count = $this->countOnlyMember($data['user_id'], $teamId);
        if ($count <= 0 && !$discontinue) {
            throw new MemberException(__('error.member.team_min_members'));
        }

        // Delete or disband the team when the user is a leader or when the team has member less than 1
        if (($member->team_position == TeamPosition::LEADER->value || $count <= 0) && $discontinue) {
            $teamService = new TeamService();
            $teamService->delete($member->team);
            $returnMessage = __('success.team.deleted');
        }

        // Delete a member
        $member->delete();
        // Re index member in team
        $this->reIndex($teamId, $deleteNumber);
        return $returnMessage;
    }

    /**
     * Get total members except own self in the team
     */
    public function countOnlyMember(int $userId, int $teamId)
    {
        $userId = ConvertUtility::toInt($userId);
        $count = TeamMember::where('user_id', '!=', $userId)->where('team_id', $teamId)->where('team_position', TeamPosition::MEMBER->value)->count();
        return $count;
    }

    /**
     * Retrieve the total number of team members, excluding yourself.
     */
    public function getMember(int $userId)
    {
        $userId = ConvertUtility::toInt($userId);
        $query = TeamMember::where('user_id', $userId);
        return $query->firstOrFail();
    }

    /**
     * Retrieve the last number of team members.
     */
    public function getLatestNumber($teamId)
    {
        $teamId = ConvertUtility::toInt($teamId);
        return TeamMember::where('team_id', $teamId)->max('sequence') ?? 0;
    }

    /**
     * Re index number in team member.
     */
    public function reIndex($teamId, $deleteNumber)
    {
        $teamId = ConvertUtility::toInt($teamId);
        $deleteNumber = ConvertUtility::toInt($deleteNumber);
        TeamMember::where('team_id', $teamId)->where('sequence', '>', $deleteNumber)
            ->decrement('sequence');
    }
}