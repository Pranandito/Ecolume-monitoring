<?php

namespace App\Enums;

enum WeatherCode: int
{
    case CLEAR = 0;
    case MAINLY_CLEAR = 1;
    case PARTLY_CLOUDY = 2;
    case OVERCAST = 3;

    case FOG = 45;
    case RIME_FOG = 48;

    case DRIZZLE_LIGHT = 51;
    case DRIZZLE_MODERATE = 53;
    case DRIZZLE_DENSE = 55;
    case FREEZING_DRIZZLE_LIGHT = 56;
    case FREEZING_DRIZZLE_DENSE = 57;

    case RAIN_SLIGHT = 61;
    case RAIN_MODERATE = 63;
    case RAIN_HEAVY = 65;
    case FREEZING_RAIN_LIGHT = 66;
    case FREEZING_RAIN_HEAVY = 67;

    case SNOW_SLIGHT = 71;
    case SNOW_MODERATE = 73;
    case SNOW_HEAVY = 75;
    case SNOW_GRAINS = 77;

    case RAIN_SHOWER_SLIGHT = 80;
    case RAIN_SHOWER_MODERATE = 81;
    case RAIN_SHOWER_VIOLENT = 82;

    case SNOW_SHOWER_SLIGHT = 85;
    case SNOW_SHOWER_HEAVY = 86;

    case THUNDERSTORM = 95;
    case THUNDERSTORM_HAIL_LIGHT = 96;
    case THUNDERSTORM_HAIL_HEAVY = 99;

    public function label(): string
    {
        return match ($this) {
            self::CLEAR,
            self::MAINLY_CLEAR
            => 'Cerah',

            self::PARTLY_CLOUDY
            => 'Cerah Berawan',

            self::OVERCAST
            => 'Mendung',

            self::FOG,
            self::RIME_FOG
            => 'Kabut',

            self::DRIZZLE_LIGHT,
            self::DRIZZLE_MODERATE,
            self::DRIZZLE_DENSE,
            self::FREEZING_DRIZZLE_LIGHT,
            self::FREEZING_DRIZZLE_DENSE
            => 'Gerimis',

            self::RAIN_SLIGHT,
            self::RAIN_MODERATE,
            self::RAIN_HEAVY,
            self::FREEZING_RAIN_LIGHT,
            self::FREEZING_RAIN_HEAVY,
            self::RAIN_SHOWER_SLIGHT,
            self::RAIN_SHOWER_MODERATE,
            self::RAIN_SHOWER_VIOLENT,
            self::SNOW_SLIGHT,
            self::SNOW_MODERATE,
            self::SNOW_HEAVY,
            self::SNOW_GRAINS,
            self::SNOW_SHOWER_SLIGHT,
            self::SNOW_SHOWER_HEAVY
            => 'Hujan',

            self::THUNDERSTORM,
            self::THUNDERSTORM_HAIL_LIGHT,
            self::THUNDERSTORM_HAIL_HEAVY
            => 'Badai Petir',
        };
    }
}
