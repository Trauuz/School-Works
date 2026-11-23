<?php

namespace App\Services;

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountSetup
{
    public function __construct(public MembershipManager $memberships, public LocationSearch $locations) {}

    /** @param array<string, mixed> $attributes */
    public function complete(User $user, array $attributes): void
    {
        $profile = $this->profile($attributes);
        DB::transaction(function () use ($user, $attributes, $profile): void {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);
            if ($user->profile_completed_at) {
                return;
            }
            $isAdmin = $user->can_manage_members || $attributes['role'] === 'admin';
            $user->forceFill([...$profile, 'name' => trim($attributes['first_name'].' '.$attributes['last_name']), 'can_manage_members' => $isAdmin, 'profile_completed_at' => now()])->save();
            $member = Member::whereRaw('LOWER(email) = ?', [mb_strtolower($user->email)])->lockForUpdate()->first();
            if ($member) {
                if ($member->user_id && $member->user_id !== $user->id) {
                    throw ValidationException::withMessages(['auth' => 'This membership is already linked to an account. Contact an administrator.']);
                }
                $member->forceFill(['user_id' => $user->id])->save();
            } elseif (! $isAdmin) {
                $member = Member::create(['first_name' => $attributes['first_name'], 'last_name' => $attributes['last_name'], 'email' => $user->email, 'phone' => $attributes['phone'] ?? null, 'membership_category_id' => $attributes['membership_category_id'], 'membership_level_id' => $attributes['membership_level_id']]);
                $member->forceFill(['user_id' => $user->id])->save();
                $this->memberships->create($member, 'application');
            }
        });
    }

    /** @param array<string, mixed> $attributes */
    public function update(User $user, array $attributes): void
    {
        $profile = $this->profile($attributes);
        DB::transaction(function () use ($user, $profile): void {
            $user->forceFill($profile)->save();
            $user->member?->update(['phone' => $profile['phone']]);
        });
    }

    /** @param array<string, mixed> $attributes @return array<string, mixed> */
    private function profile(array $attributes): array
    {
        $profile = array_intersect_key($attributes, array_flip(['phone', 'address_line', 'city', 'region', 'country_code', 'postal_code', 'address_latitude', 'address_longitude']));
        $profile['phone'] ??= null;
        $profile += ['weather_location_id' => null, 'weather_city' => null, 'weather_latitude' => null, 'weather_longitude' => null, 'weather_timezone' => null];
        if (! empty($attributes['weather_location_id'])) {
            $city = $this->locations->find((int) $attributes['weather_location_id']);
            if (strcasecmp($city['name'], $attributes['city']) !== 0 || $city['country_code'] !== $attributes['country_code']) {
                throw ValidationException::withMessages(['weather_location_id' => 'The weather city must match your address city and country. Search again or clear the weather city.']);
            }
            $profile = [...$profile, 'weather_location_id' => $city['id'], 'weather_city' => $city['name'], 'weather_latitude' => $city['latitude'], 'weather_longitude' => $city['longitude'], 'weather_timezone' => $city['timezone']];
        }

        return $profile;
    }
}
