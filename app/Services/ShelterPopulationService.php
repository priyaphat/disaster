<?php

namespace App\Services;

use App\Enums\TeamPosition;
use App\Exceptions\WarningException;
use App\Http\Utility\ConvertUtility;
use App\Models\Population;
use App\Models\ShelterPopulation;
use App\Models\TeamMember;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShelterPopulationService
{
    /**
     * Create a member.
     */
    public function updateOrCreate(array $data)
    {
        return ShelterPopulation::updateOrCreate(
            [
                'population_id' => $data['population_id']
            ],
            [
                'population_id' => $data['population_id'],
                'shelter_id' => $data['shelter_id']
            ]
        );
    }

    /**
     * Update a member.
     */
    public function update(Population $population, array $data)
    {
        $shelterService = app(ShelterService::class);

        if ($shelterService->isShelterMax($data['shelter_id'], $population->id)) {
            throw new BadRequestHttpException(__('error.shelter.full'));
        }

        // Check if the shelter is open. If it is not, the data cannot be updated
        if (!$shelterService->isShelterOpen($data['shelter_id'])) {
            throw new BadRequestHttpException(__('error.shelter.closed'));
        }

        return $this->updateOrCreate(['population_id' => $population->id, 'shelter_id' => $data['shelter_id']]);
    }

    /**
     * Delete a member.
     */
    public function delete(Population $population)
    {
        return $population->shelterPopulation->update(['shelter_id' => NULL]);
    }

    public function deleteByShelter(array $data)
    {
        return ShelterPopulation::where('shelter_id', $data['shelter_id'])->get()->each->update(['shelter_id' => NULL]);
    }
}
