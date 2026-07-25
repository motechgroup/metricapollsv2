<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\PublicOpinion\Models\Region;
use App\Modules\PublicOpinion\Models\County;
use App\Modules\PublicOpinion\Models\Constituency;
use App\Modules\PublicOpinion\Models\PoliticalParty;
use App\Modules\PublicOpinion\Models\Politician;
use App\Modules\PublicOpinion\Models\PublicOpinion;
use App\Modules\PublicOpinion\Models\PublicOpinionVote;

class GeographicAndPoliticalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed 8 Standard Regions
        $regionsData = [
            'National' => 'NAT',
            'Central' => 'CTL',
            'Coast' => 'CST',
            'Nairobi' => 'NBI',
            'North Eastern' => 'NE',
            'Nyanza' => 'NYZ',
            'Rift Valley' => 'RV',
            'Western' => 'WST',
        ];

        $regions = [];
        foreach ($regionsData as $name => $code) {
            $regions[$name] = Region::updateOrCreate(
                ['name' => $name],
                ['code' => $code]
            );
        }

        // 2. Seed 47 Counties mapped to Regions
        $countiesMapping = [
            'Nairobi' => [
                ['name' => 'Nairobi City', 'code' => 47]
            ],
            'Central' => [
                ['name' => 'Nyandarua', 'code' => 18],
                ['name' => 'Nyeri', 'code' => 19],
                ['name' => 'Kirinyaga', 'code' => 20],
                ['name' => "Murang'a", 'code' => 21],
                ['name' => 'Kiambu', 'code' => 22],
                ['name' => 'Meru', 'code' => 12],
                ['name' => 'Tharaka-Nithi', 'code' => 13],
                ['name' => 'Embu', 'code' => 14],
            ],
            'Coast' => [
                ['name' => 'Mombasa', 'code' => 1],
                ['name' => 'Kwale', 'code' => 2],
                ['name' => 'Kilifi', 'code' => 3],
                ['name' => 'Tana River', 'code' => 4],
                ['name' => 'Lamu', 'code' => 5],
                ['name' => 'Taita Taveta', 'code' => 6],
            ],
            'North Eastern' => [
                ['name' => 'Garissa', 'code' => 7],
                ['name' => 'Wajir', 'code' => 8],
                ['name' => 'Mandera', 'code' => 9],
                ['name' => 'Marsabit', 'code' => 10],
                ['name' => 'Isiolo', 'code' => 11],
            ],
            'Nyanza' => [
                ['name' => 'Siaya', 'code' => 41],
                ['name' => 'Kisumu', 'code' => 42],
                ['name' => 'Homa Bay', 'code' => 43],
                ['name' => 'Migori', 'code' => 44],
                ['name' => 'Kisii', 'code' => 45],
                ['name' => 'Nyamira', 'code' => 46],
            ],
            'Rift Valley' => [
                ['name' => 'Turkana', 'code' => 23],
                ['name' => 'West Pokot', 'code' => 24],
                ['name' => 'Samburu', 'code' => 25],
                ['name' => 'Trans Nzoia', 'code' => 26],
                ['name' => 'Uasin Gishu', 'code' => 27],
                ['name' => 'Elgeyo Marakwet', 'code' => 28],
                ['name' => 'Nandi', 'code' => 29],
                ['name' => 'Baringo', 'code' => 30],
                ['name' => 'Laikipia', 'code' => 31],
                ['name' => 'Nakuru', 'code' => 32],
                ['name' => 'Narok', 'code' => 33],
                ['name' => 'Kajiado', 'code' => 34],
                ['name' => 'Kericho', 'code' => 35],
                ['name' => 'Bomet', 'code' => 36],
                ['name' => 'Kitui', 'code' => 15],
                ['name' => 'Machakos', 'code' => 16],
                ['name' => 'Makueni', 'code' => 17],
            ],
            'Western' => [
                ['name' => 'Kakamega', 'code' => 37],
                ['name' => 'Vihiga', 'code' => 38],
                ['name' => 'Bungoma', 'code' => 39],
                ['name' => 'Busia', 'code' => 40],
            ],
        ];

        $counties = [];
        foreach ($countiesMapping as $regName => $cList) {
            $regObj = $regions[$regName];
            foreach ($cList as $c) {
                $counties[$c['name']] = County::updateOrCreate(
                    ['name' => $c['name']],
                    ['region_id' => $regObj->id, 'code' => $c['code']]
                );
            }
        }

        // 3. Seed Sample Constituencies (for Kisii and Nairobi)
        if (isset($counties['Kisii'])) {
            $kisiiConst = ['Kitutu Chache South', 'Kitutu Chache North', 'Nyaribari Chache', 'Nyaribari Masaba', 'Bonchari', 'South Mugirango', 'Bomachoge Borabu', 'Bomachoge Chache', 'Bobasi'];
            foreach ($kisiiConst as $cn) {
                Constituency::updateOrCreate(
                    ['county_id' => $counties['Kisii']->id, 'name' => $cn]
                );
            }
        }

        if (isset($counties['Nairobi City'])) {
            $nbiConst = ['Dagoretti North', 'Westlands', 'Starehe', 'Langata', 'Kasarani', 'Kibra', 'Embakasi East'];
            foreach ($nbiConst as $cn) {
                Constituency::updateOrCreate(
                    ['county_id' => $counties['Nairobi City']->id, 'name' => $cn]
                );
            }
        }

        // 4. Seed Political Parties
        $partiesData = [
            [
                'name' => 'United Democratic Alliance',
                'abbreviation' => 'UDA',
                'party_color' => '#EAB308', // Yellow
                'logo_path' => '/images/parties/uda.png',
                'description' => 'Bottom Up Economic Model Alliance.',
            ],
            [
                'name' => 'Orange Democratic Movement',
                'abbreviation' => 'ODM',
                'party_color' => '#F97316', // Orange
                'logo_path' => '/images/parties/odm.png',
                'description' => 'Social Democracy Movement.',
            ],
            [
                'name' => 'Jubilee Party',
                'abbreviation' => 'Jubilee',
                'party_color' => '#EF4444', // Red
                'logo_path' => '/images/parties/jubilee.png',
                'description' => 'Tuko Pamoja Development Movement.',
            ],
            [
                'name' => 'Wiper Democratic Movement',
                'abbreviation' => 'Wiper',
                'party_color' => '#3B82F6', // Blue
                'logo_path' => '/images/parties/wiper.png',
                'description' => 'One Kenya Alliance Movement.',
            ],
            [
                'name' => 'Independent / Coalition',
                'abbreviation' => 'IND',
                'party_color' => '#6B7280', // Gray
                'logo_path' => '/images/parties/ind.png',
                'description' => 'Independent candidate without party sponsorship.',
            ],
        ];

        $parties = [];
        foreach ($partiesData as $p) {
            $parties[$p['abbreviation']] = PoliticalParty::updateOrCreate(
                ['abbreviation' => $p['abbreviation']],
                $p
            );
        }

        // 5. Seed Politicians
        $kisiiCounty = $counties['Kisii'] ?? null;
        $nairobiCounty = $counties['Nairobi City'] ?? null;
        $nyanzaRegion = $regions['Nyanza'] ?? null;
        $nairobiRegion = $regions['Nairobi'] ?? null;
        $nationalRegion = $regions['National'] ?? null;

        $politiciansData = [
            // Kisii Woman Representative Candidates
            [
                'name' => 'Hon. Dorice Donya Aburi',
                'photo_path' => '/images/politicians/donya.jpg',
                'political_party_id' => $parties['Wiper']->id,
                'level' => 'county',
                'region_id' => $nyanzaRegion?->id,
                'county_id' => $kisiiCounty?->id,
                'position_title' => 'Woman Representative',
                'bio' => 'Incumbent Woman Representative for Kisii County, championing women empowerment and grassroots development.',
            ],
            [
                'name' => 'Hon. Janet Ongera',
                'photo_path' => '/images/politicians/ongera.jpg',
                'political_party_id' => $parties['ODM']->id,
                'level' => 'county',
                'region_id' => $nyanzaRegion?->id,
                'county_id' => $kisiiCounty?->id,
                'position_title' => 'Woman Representative',
                'bio' => 'Former Kisii County Woman Representative and experienced legislator.',
            ],
            [
                'name' => 'Teresa Bitutu',
                'photo_path' => '/images/politicians/bitutu.jpg',
                'political_party_id' => $parties['UDA']->id,
                'level' => 'county',
                'region_id' => $nyanzaRegion?->id,
                'county_id' => $kisiiCounty?->id,
                'position_title' => 'Woman Representative',
                'bio' => 'Community leader advocating for youth education and economic empowerment in Kisii.',
            ],

            // Nairobi Gubernatorial Candidates
            [
                'name' => 'Hon. Johnson Sakaja',
                'photo_path' => '/images/politicians/sakaja.jpg',
                'political_party_id' => $parties['UDA']->id,
                'level' => 'county',
                'region_id' => $nairobiRegion?->id,
                'county_id' => $nairobiCounty?->id,
                'position_title' => 'Governor',
                'bio' => 'Incumbent Governor of Nairobi City County.',
            ],
            [
                'name' => 'Polycarp Igathe',
                'photo_path' => '/images/politicians/igathe.jpg',
                'political_party_id' => $parties['Jubilee']->id,
                'level' => 'county',
                'region_id' => $nairobiRegion?->id,
                'county_id' => $nairobiCounty?->id,
                'position_title' => 'Governor',
                'bio' => 'Corporate executive and former Deputy Governor of Nairobi.',
            ],

            // National Presidential Candidates
            [
                'name' => 'H.E. William Samoei Ruto',
                'photo_path' => '/images/politicians/ruto.jpg',
                'political_party_id' => $parties['UDA']->id,
                'level' => 'national',
                'region_id' => $nationalRegion?->id,
                'position_title' => 'President',
                'bio' => '5th President of the Republic of Kenya.',
            ],
            [
                'name' => 'Rt. Hon. Raila Odinga',
                'photo_path' => '/images/politicians/raila.jpg',
                'political_party_id' => $parties['ODM']->id,
                'level' => 'national',
                'region_id' => $nationalRegion?->id,
                'position_title' => 'President',
                'bio' => 'High Representative for Infrastructure & African Union envoy.',
            ],
            [
                'name' => 'Hon. Alvin Palapala',
                'photo_path' => '/images/politicians/palapala.jpg',
                'political_party_id' => $parties['ODM']->id,
                'level' => 'constituency',
                'region_id' => $nairobiRegion?->id,
                'county_id' => $nairobiCounty?->id,
                'position_title' => 'Member of County Assembly (MCA)',
                'bio' => 'Kitisuru Ward Member of County Assembly.',
            ],
            [
                'name' => 'Hon. Robert Alai',
                'photo_path' => '/images/politicians/alai.jpg',
                'political_party_id' => $parties['ODM']->id,
                'level' => 'constituency',
                'region_id' => $nairobiRegion?->id,
                'county_id' => $nairobiCounty?->id,
                'position_title' => 'Member of County Assembly (MCA)',
                'bio' => 'Kileleshwa Ward Member of County Assembly.',
            ],
        ];

        foreach ($politiciansData as $pol) {
            Politician::updateOrCreate(
                ['name' => $pol['name']],
                $pol
            );
        }

        // 6. Seed Sample Live & Ended Polls
        // Poll 1: Kisii County Woman Representative Poll (LIVE)
        $donya = Politician::where('name', 'Hon. Dorice Donya Aburi')->first();
        $ongera = Politician::where('name', 'Hon. Janet Ongera')->first();
        $bitutu = Politician::where('name', 'Teresa Bitutu')->first();

        $kisiiPollOptions = [
            [
                'name' => 'Hon. Dorice Donya Aburi',
                'politician_id' => $donya?->id,
                'party_name' => 'Wiper',
                'party_color' => '#3B82F6',
                'photo' => '/images/politicians/donya.jpg',
                'votes' => 1450,
            ],
            [
                'name' => 'Hon. Janet Ongera',
                'politician_id' => $ongera?->id,
                'party_name' => 'ODM',
                'party_color' => '#F97316',
                'photo' => '/images/politicians/ongera.jpg',
                'votes' => 1120,
            ],
            [
                'name' => 'Teresa Bitutu',
                'politician_id' => $bitutu?->id,
                'party_name' => 'UDA',
                'party_color' => '#EAB308',
                'photo' => '/images/politicians/bitutu.jpg',
                'votes' => 890,
            ],
        ];

        PublicOpinion::updateOrCreate(
            ['topic' => '2026 Preferred Woman Representative Candidate for Kisii County'],
            [
                'target_level' => 'county',
                'region_id' => $nyanzaRegion?->id,
                'county_id' => $kisiiCounty?->id,
                'position_title' => 'Woman Representative',
                'options' => ['Hon. Dorice Donya Aburi', 'Hon. Janet Ongera', 'Teresa Bitutu'],
                'candidates_data' => $kisiiPollOptions,
                'status' => 'live',
                'allow_public_voting' => true,
                'expires_at' => now()->addDays(14),
                'votes_count' => 3460,
            ]
        );

        // Poll 2: Nairobi Gubernatorial Poll (ENDED)
        $sakaja = Politician::where('name', 'Hon. Johnson Sakaja')->first();
        $igathe = Politician::where('name', 'Polycarp Igathe')->first();

        $nairobiPollOptions = [
            [
                'name' => 'Hon. Johnson Sakaja',
                'politician_id' => $sakaja?->id,
                'party_name' => 'UDA',
                'party_color' => '#EAB308',
                'photo' => '/images/politicians/sakaja.jpg',
                'votes' => 4580,
            ],
            [
                'name' => 'Polycarp Igathe',
                'politician_id' => $igathe?->id,
                'party_name' => 'Jubilee',
                'party_color' => '#EF4444',
                'photo' => '/images/politicians/igathe.jpg',
                'votes' => 3920,
            ],
        ];

        PublicOpinion::updateOrCreate(
            ['topic' => 'Nairobi County Governor Approval & Popularity Rating'],
            [
                'target_level' => 'county',
                'region_id' => $nairobiRegion?->id,
                'county_id' => $nairobiCounty?->id,
                'position_title' => 'Governor',
                'options' => ['Hon. Johnson Sakaja', 'Polycarp Igathe'],
                'candidates_data' => $nairobiPollOptions,
                'status' => 'ended',
                'expires_at' => now()->subDays(2),
                'votes_count' => 8500,
            ]
        );

        // Poll 3: National Presidential Popularity Poll (LIVE)
        $ruto = Politician::where('name', 'H.E. William Samoei Ruto')->first();
        $raila = Politician::where('name', 'Rt. Hon. Raila Odinga')->first();

        $presidentialPollOptions = [
            [
                'name' => 'H.E. William Samoei Ruto',
                'politician_id' => $ruto?->id,
                'party_name' => 'UDA',
                'party_color' => '#EAB308',
                'photo' => '/images/politicians/ruto.jpg',
                'votes' => 12400,
            ],
            [
                'name' => 'Rt. Hon. Raila Odinga',
                'politician_id' => $raila?->id,
                'party_name' => 'ODM',
                'party_color' => '#F97316',
                'photo' => '/images/politicians/raila.jpg',
                'votes' => 11850,
            ],
        ];

        PublicOpinion::updateOrCreate(
            ['topic' => 'National Presidential Popularity Tracking Index 2026'],
            [
                'target_level' => 'national',
                'region_id' => $nationalRegion?->id,
                'position_title' => 'President',
                'options' => ['H.E. William Samoei Ruto', 'Rt. Hon. Raila Odinga'],
                'candidates_data' => $presidentialPollOptions,
                'status' => 'live',
                'expires_at' => now()->addDays(30),
                'votes_count' => 24250,
            ]
        );

        // Poll 4: Member of County Assembly (MCA) Ward Poll (LIVE)
        $palapala = Politician::where('name', 'Hon. Alvin Palapala')->first();
        $alai = Politician::where('name', 'Hon. Robert Alai')->first();

        $mcaPollOptions = [
            [
                'name' => 'Hon. Alvin Palapala',
                'politician_id' => $palapala?->id,
                'party_name' => 'ODM',
                'party_color' => '#F97316',
                'photo' => '/images/politicians/palapala.jpg',
                'votes' => 2150,
            ],
            [
                'name' => 'Hon. Robert Alai',
                'politician_id' => $alai?->id,
                'party_name' => 'ODM',
                'party_color' => '#F97316',
                'photo' => '/images/politicians/alai.jpg',
                'votes' => 1840,
            ],
        ];

        PublicOpinion::updateOrCreate(
            ['topic' => 'Preferred Member of County Assembly (MCA) Candidate Rating 2026'],
            [
                'target_level' => 'county',
                'region_id' => $nairobiRegion?->id,
                'county_id' => $nairobiCounty?->id,
                'position_title' => 'Member of County Assembly (MCA)',
                'options' => ['Hon. Alvin Palapala', 'Hon. Robert Alai'],
                'candidates_data' => $mcaPollOptions,
                'status' => 'live',
                'allow_public_voting' => true,
                'expires_at' => now()->addDays(20),
                'votes_count' => 3990,
            ]
        );
    }
}
