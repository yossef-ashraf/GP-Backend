<?php

namespace App\Services;

use Carbon\Carbon;

class HijriDateServices
{
    public function getHijriYear(Carbon $date): int
    {
        // This is a simplified calculation. For production, you should use a proper Hijri calendar library
        $gregorianYear = $date->year;
        $hijriYear = $gregorianYear - 579;
        return $hijriYear;
    }

    public function getRamadanDatesApi(?int $hijriYear = null): array
    {
        if (!$hijriYear) {
            $hijriYear = $this->getHijriYear(Carbon::now());
        }

        // This is a simplified calculation. For production, you should use a proper Hijri calendar library
        // and calculate the actual Ramadan dates based on moon sightings
        $startDate = Carbon::createFromDate($hijriYear + 579, 3, 1);
        $endDate = Carbon::createFromDate($hijriYear + 579, 3, 30);

        return [
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'hijri_year' => $hijriYear,
        ];
    }

    public function getEidAlFitrDatesApi(?int $hijriYear = null): array
    {
        if (!$hijriYear) {
            $hijriYear = $this->getHijriYear(Carbon::now());
        }

        // This is a simplified calculation. For production, you should use a proper Hijri calendar library
        // and calculate the actual Eid al-Fitr dates based on moon sightings
        $startDate = Carbon::createFromDate($hijriYear + 579, 4, 1);
        $endDate = Carbon::createFromDate($hijriYear + 579, 4, 3);

        return [
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'hijri_year' => $hijriYear,
        ];
    }

    public function getEidAlAdhaDatesApi(?int $hijriYear = null): array
    {
        if (!$hijriYear) {
            $hijriYear = $this->getHijriYear(Carbon::now());
        }

        // This is a simplified calculation. For production, you should use a proper Hijri calendar library
        // and calculate the actual Eid al-Adha dates based on moon sightings
        $startDate = Carbon::createFromDate($hijriYear + 579, 7, 10);
        $endDate = Carbon::createFromDate($hijriYear + 579, 7, 13);

        return [
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'hijri_year' => $hijriYear,
        ];
    }
} 